<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace leeduc\authclient\clients;

use yii\authclient\OAuth1;

/**
 * Twitter allows authentication via Twitter OAuth.
 *
 * In order to use Twitter OAuth you must register your application at <https://dev.twitter.com/apps/new>.
 *
 * Example application configuration:
 *
 * ~~~
 * 'components' => [
 *     'authClientCollection' => [
 *         'class' => 'leeduc\authclient\Collection',
 *         'clients' => [
 *             'twitter' => [
 *                 'class' => 'leeduc\authclient\clients\Twitter',
 *                 'consumerKey' => 'twitter_consumer_key',
 *                 'consumerSecret' => 'twitter_consumer_secret',
 *             ],
 *         ],
 *     ]
 *     ...
 * ]
 * ~~~
 *
 * @see https://dev.twitter.com/apps/new
 * @see https://dev.twitter.com/docs/api
 *
 * @author Le Duc <lee.duc55@gmail.com>
 * @since 1.0
 */
class Twitter extends OAuth1
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'https://api.twitter.com/oauth/authenticate';
    /**
     * @inheritdoc
     */
    public $requestTokenUrl = 'https://api.twitter.com/oauth/request_token';
    /**
     * @inheritdoc
     */
    public $requestTokenMethod = 'POST';
    /**
     * @inheritdoc
     */
    public $accessTokenUrl = 'https://api.twitter.com/oauth/access_token';
    /**
     * @inheritdoc
     */
    public $accessTokenMethod = 'POST';
    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://api.twitter.com/1.1';
    /**
     * access token for app
     */
    public $access_token;
    /**
     * access token sercet for app
     */
    public $access_token_secret;

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return $this->api('account/verify_credentials.json', 'GET');
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'twitter';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'Twitter';
    }

    /**
     * setToken from component to controller
     * @param string $access_token        access token
     * @param string $access_token_secret secret access token
     */
    public function setToken($access_token = null, $access_token_secret = null)
    {
        $token = new \yii\authclient\OAuthToken([
            'token' => ($access_token) ? $access_token : $this->access_token,
            'tokenSecret' => ($access_token_secret) ? $access_token_secret : $this->access_token_secret,
        ]);
        $this->setAccessToken($token);
    }

    /**
     * redirect url to auth of twitter
     */
    public function redirectAuth()
    {
        $data = $this->fetchRequestToken();
        $a = $this->buildAuthUrl($data);
        $b = \Yii::$app->getResponse()->redirect($a);
        $this->redirect($b);
    }

    /**
     * get you info
     * @param  array  $params params for query
     * @return json           data
     */
    public function getMeProfile(array $params = array())
    {
        return $this->api('account/verify_credentials.json', 'GET', $params);
    }

    /**
     * get time line of me
     * @param  array  $params params for query
     * @return json           data
     */
    public function getMeTimeline(array $params = array())
    {
        return $this->api('statuses/home_timeline.json', 'GET', $params);
    }

    /**
     * get timeline of user
     * @param  int    $user_id  user id
     * @param  string $username user screen name
     * @return json             data
     */
    public function getUserTimeline($user_id = null, array $params = array())
    {
        return $this->api('statuses/user_timeline.json', 'GET', array_merge([
            'user_id' => $user_id,
        ], $params));
    }

    /**
     * get user profile
     * @param  int    $user_id  user id
     * @param  string $username user screen name
     * @return json             data
     */
    public function getUserProfile($user_id = null, array $params = array())
    {
        return $this->api('users/show.json', 'GET', array_merge([
            'user_id' => $user_id,
        ], $params));
    }

    /**
     * get post detail
     * @param  int    $id post id
     * @return json       data
     */
    public function getPostDetail($id, array $params = array())
    {
        return $this->api('statuses/show.json', 'GET', array_merge([
            'id' => $id,
        ], $params));
    }

    public function getPagePosts($name, array $params = array())
    {
        return $this->api('statuses/user_timeline.json', 'GET', array_merge([
            'screen_name' => $name,
        ], $params));
    }

    public function postReplyComment($id, array $params = array())
    {
        return $this->api('statuses/update.json', 'POST', array_merge([
            'in_reply_to_status_id' => $id,
        ], $params));
    }
}
