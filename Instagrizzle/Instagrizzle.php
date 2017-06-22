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
            // Otherwise, get the feed from Instagram
            $media = $this->getFeed()->toArray();
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

    private function getFeed()
    {
        $feed = json_decode(file_get_contents("https://www.instagram.com/{$this->username}/media"), true);

        return collect($feed['items'])->map(function ($item) {
            return $item + [
                'url' => $item['link'],
                'image' => $item['images']['standard_resolution']['url'],
            ];
        });
    }
}
