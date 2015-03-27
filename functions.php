<?php

require_once(__DIR__ . DS . 'TwitterAPIExchange.php');


function twitter_feed_get_items($keyword = false, $results_count = 5)
{


    $oauth_access_token = get_option('access_token', 'twitter_feed');
    $oauth_access_token_secret = get_option('access_token_secret', 'twitter_feed');
    $consumer_key = get_option('consumer_key', 'twitter_feed');
    $consumer_secret = get_option('consumer_secret', 'twitter_feed');

    if (!$oauth_access_token || !$oauth_access_token_secret || !$consumer_key || !$consumer_secret) {
        return false;
    }

    $cache_expiration_minutes = 1;
    $cache_id = md5($keyword . $results_count);
    $cache_group = 'twitter_feed';
    $cached_results = cache_get($cache_id, $cache_group);
    if ($cached_results != false) {
        return $cached_results;
    }


    $settings = array(
        'oauth_access_token' => $oauth_access_token,
        'oauth_access_token_secret' => $oauth_access_token_secret,
        'consumer_key' => $consumer_key,
        'consumer_secret' => $consumer_secret
    );

    $url = 'https://api.twitter.com/1.1/search/tweets.json';
    $query = urlencode($keyword);
    $count = intval($results_count);
    $getfield = '?count=' . $count . '&q=' . $query;
    $requestMethod = 'GET';

    $twitter = new TwitterAPIExchange($settings);
    $response = $twitter->setGetfield($getfield)
        ->buildOauth($url, $requestMethod)
        ->performRequest();
 

    $items = json_decode($response, true);
    $return = array();
    if (isset($items['statuses'])) {
        foreach ($items['statuses'] as $status => $statusData) {
            $tweet = array();
            $tweet['url'] = false;
            $tweet['media'] = false;
            $tweet['name'] = false;
            $tweet['profile_image'] = false;
            $tweet['screen_name'] = false;

            $tweet['id'] = $statusData['id'];
            $tweet['created_at'] = $statusData['created_at'];
            $tweet['ago'] = mw()->format->ago($statusData['created_at']);

            if (isset($statusData['entities']['urls'][0])) {

                if (is_array($statusData['entities']['urls'][0])) {
                    $tweet['url'] = $statusData['entities']['urls'][0]['url'];
                } else {
                    $tweet['url'] = $statusData['entities']['urls'][0];

                }
            }
            if (isset($statusData['entities']['media'][0])) {
                $tweet['media'] = $statusData['entities']['media'][0]['media_url'];
                if ($tweet['url'] == false) {
                    $tweet['url'] = $statusData['entities']['media'][0]['expanded_url'];
                }
            }
            if (isset($statusData['user'])) {
                $tweet['user_data'] = $statusData['user'];
                $tweet['screen_name'] = $statusData['user']['screen_name'];
                $tweet['name'] = $statusData['user']['name'];
                $tweet['profile_image'] = $statusData['user']['profile_image_url_https'];
                if ($tweet['url'] == false) {
                    $tweet['url'] = 'https://twitter.com/' . $tweet['screen_name'] . '/status/' . $statusData['id_str'];
                }
            }
            if (isset($statusData['text'])) {
                $tweet['text'] = $statusData['text'];
            }
            $return[] = $tweet;
        }
    }
    if (!empty($return)) {
        cache_save($return, $cache_id, $cache_group, $cache_expiration_minutes);
    }


    return $return;
}
