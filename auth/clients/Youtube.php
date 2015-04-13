<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace leeduc\authclient\clients;

use yii\authclient\OAuth2;
use leeduc\authclient\SocialInterface;

/**
 * GoogleOAuth allows authentication via Google OAuth.
 *
 * In order to use Google OAuth you must create a project at <https://console.developers.google.com/project>
 * and setup its credentials at <https://console.developers.google.com/project/[yourProjectId]/apiui/credential>.
 * In order to enable using scopes for retrieving user attributes, you should also enable Google+ API at
 * <https://console.developers.google.com/project/[yourProjectId]/apiui/api/plus>
 *
 * Example application configuration:
 *
 * ~~~
 * 'components' => [
 *     'authClientCollection' => [
 *         'class' => 'leeduc\authclient\Collection',
 *         'clients' => [
 *             'youtube' => [
 *                 'class' => 'leeduc\authclient\clients\Youtube',
 *                 'clientId' => 'google_client_id',
 *                 'clientSecret' => 'google_client_secret',
 *             ],
 *         ],
 *     ]
 *     ...
 * ]
 * ~~~
 *
 * @see https://console.developers.google.com/project
 *
 * @author Le Duc <lee.duc55@gmail.com>
 * @since 1.0
 */
class Youtube extends OAuth2
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'https://accounts.google.com/o/oauth2/auth';
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://accounts.google.com/o/oauth2/token';
    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://www.googleapis.com/youtube/v3';


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->scope === null) {
            $this->scope = implode(' ', [
                'https://www.googleapis.com/auth/youtube',
                'https://www.googleapis.com/auth/youtube.force-ssl',
                'https://www.googleapis.com/auth/youtube.readonly',
                'https://www.googleapis.com/auth/youtube.upload',
                'https://www.googleapis.com/auth/youtubepartner',
                'https://www.googleapis.com/auth/youtubepartner-channel-audit'
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return $this->api('activities', 'GET');
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'youtube';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'Youtube';
    }

    /**
     * get your profile
     * @param  array  $params options for params
     * @return array          data
     */
    public function getMeProfile(array $params = array())
    {
        return $this->api('channels','GET',[
            'part' => 'snippet,contentDetails',
            'mine' => 'true'
        ]);
    }

    /**
     * get all your video
     * @param  array  $params options for params
     * @return array          data
     */
    public function getMeTimeline(array $params = array())
    {
        $id = $this->getMeProfile()['items'][0]['id'];
        return $this->api('search','GET',[
            'part' => 'snippet',
            'channelId' => $id,
        ]);
    }

    /**
     * get home suggest video
     * @return array data
     */
    public function getHomeSuggest()
    {
        return $this->api('playlists','GET',[
            'part' => 'snippet',
            'home' => 'true',
        ]);
    }

    /**
     * get all your playlist
     * @return array data
     */
    public function getMyPlaylistInChannel()
    {
        return $this->api('playlists','GET',[
            'part' => 'snippet,contentDetails',
            'mine' => 'true',
        ]);
    }

    /**
     * get all playlist of channel
     * @param  string $id id of channel
     * @return array      data
     */
    public function getPlaylistInChannel($id)
    {
        return $this->api('playlists','GET',[
            'part' => 'snippet,contentDetails',
            'channelId' => $id,
        ]);
    }

    /**
     * get all videos in playlist
     * @param  string $id id of playlist
     * @return array      data
     */
    public function getVideosInPlaylist($id)
    {
        return $this->api('playlistItems','GET',[
            'part' => 'snippet,contentDetails',
            'playlistId' => $id,
        ]);
    }

    /**
     * get all users subr me
     * @return array data
     */
    public function getMySubscription()
    {
        return $this->api('subscriptions','GET',[
            'part' => 'snippet,contentDetails,subscriberSnippet',
            'mySubscribers' => 'true',
        ]);
    }

    /**
     * get all your subr
     * @return array   data
     */
    public function getSubscription()
    {
        return $this->api('subscriptions','GET',[
            'part' => 'snippet,contentDetails,subscriberSnippet',
            'mine' => 'true',
        ]);
    }

    /**
     * get user profile
     * @param  string $id     id of user
     * @param  array  $params options for params
     * @return array          data
     */
    public function getUserProfile($id,array $params = array())
    {
        return $this->api('channels','GET',[
            'part' => 'snippet,contentDetails',
            'id' => $id
        ]);
    }

    /**
     * get all videos of user
     * @param  string $id     id of user
     * @param  array  $params options for params
     * @return array          data
     */
    public function getUserTimeline($id,array $params = array())
    {
        return $this->api('search','GET',[
            'part' => 'snippet',
            'channelId' => $id
        ]);
    }

    /**
     * get post detail
     * @param  string $post_id post id
     * @param  array  $params  options for params
     * @return array           data
     */
    public function getPostDetail($post_id, array $params = array())
    {
        return $this->api('videos', 'GET',[
            'part' => 'snippet,contentDetails,statistics',
            'id' => $post_id,
        ]);
    }
}
