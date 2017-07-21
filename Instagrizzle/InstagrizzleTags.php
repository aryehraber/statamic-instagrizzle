<?php

namespace Statamic\Addons\Instagrizzle;

use Statamic\Extend\Tags;

class InstagrizzleTags extends Tags
{
    public function __call($method, $args)
    {
        if ($name = explode(':', $this->tag)[1]) {
            return $this->index($name);
        }
    }

    public function index($username = null)
    {   
        $username = $username ?: $this->get('username');
        $limit = $this->getParamInt('limit');
        $offset = $this->getParamInt('offset');

        $instagrizzle = new Instagrizzle($username, $limit, $offset);

        return $this->parseLoop($instagrizzle->getMedia());
    }

    public function debug()
    {
        $instagrizzle = new Instagrizzle($this->get('username'));

        dd($instagrizzle->getMedia());
    }
}
