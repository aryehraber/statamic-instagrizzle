<?php

namespace Statamic\Addons\Instagrizzle;

use Statamic\Extend\Extensible;

class Instagrizzle
{
    use Extensible;

    public $username;
    public $limit;
    public $offset;

    public function __construct($username, $limit = false, $offset = 0)
    {
        if (! $this->username = $username) {
            throw new \Exception('Instagrizzle: no username found.');
        }

        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function getMedia()
    {        
        // Grab media from cache if available
        if (! $media = $this->cache->get($this->username)) {
            // Otherwise, scrape it real good
            $media = $this->scrape()->toArray();
            // Save it to the cache for next time (defaults to 1 hour)
            $this->cache->put($this->username, $media, $this->getConfig('cache_length', 60));
        }

        if ($this->offset > 0) {
            $media = array_splice($media, $this->offset);
        }

        if ($this->limit) {
            $media = array_slice($media, 0, $this->limit);
        }

        return $media;
    }

    public function scrape()
    {
        $source = file_get_contents('http://instagram.com/' . $this->username);
        $shards = explode('window._sharedData = ', $source);
        $json_response = explode(';</script>', $shards[1]);
        $response_array = json_decode($json_response[0], TRUE);
        $nodes = array_get($response_array, 'entry_data:ProfilePage:0:user:media:nodes');

        return collect($nodes)->map(function ($node) {
            $url = 'https://instagram.com/p/' . $node['code'];
            $image = $node['display_src'];

            return $node + [
                'url' => $url,
                'link' => $url,
                'image' => $image
            ];
        });
    }
}
