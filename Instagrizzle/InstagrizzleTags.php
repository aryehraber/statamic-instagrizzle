<?php

namespace Statamic\Addons\Instagrizzle;

use Statamic\Extend\Tags;

class InstagrizzleTags extends Tags
{
    public $username;

    public function __call($method, $args)
    {
        if ($name = explode(':', $this->tag)[1]) {
            return $this->index($name);
        }
    }

    public function init()
    {
        $this->username = $this->getParam('username', $this->getConfig('username'));
    }

    public function index($username = null)
    {   
        $username = $username ?: $this->username;
        $limit = $this->getParamInt('limit');
        $offset = $this->getParamInt('offset');

        $instagrizzle = new Instagrizzle($username, $limit, $offset);

        return $this->parseLoop($instagrizzle->getMedia());
    }

    public function debug()
    {
        $instagrizzle = new Instagrizzle($this->username);

        dd($instagrizzle->getMedia());
    }
}