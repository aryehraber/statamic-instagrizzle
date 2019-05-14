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
        if (!$this->username = $username) {
            throw new \Exception('Instagrizzle: no username found.');
        }

        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function getMedia()
    {
        // Grab media from cache if available
        if (!$media = $this->cache->get($this->username)) {
            try {
                // Otherwise, get the feed from Instagram
                $media = $this->getFeed()->toArray();
            } catch (\Exception $exception) {
                return [];
            }

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
        $html = file_get_contents("https://www.instagram.com/{$this->username}");
        $re = '/window\._sharedData = {(.*)}/';

        preg_match_all($re, $html, $matches, PREG_SET_ORDER, 0);
        $json = json_decode('{' . $matches[0][1] . '}', true);
        $profile = $json['entry_data']['ProfilePage'][0]['graphql']['user'];
        $media = $profile['edge_owner_to_timeline_media']['edges'];

        return collect($media)->map(function ($item) {
            return array_merge($item['node'], [
                'link' => 'https://www.instagram.com/p/' . $item['node']['shortcode'],
                'thumbnail' => $item['node']['thumbnail_src'],
                'image' => $item['node']['display_url'],
            ]);
        });
    }
}
