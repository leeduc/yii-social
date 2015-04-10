<?php
namespace leeduc\authclient\clients;

use yii\authclient\OAuth1;
use leeduc\authclient\SocialInterface;
class Tumblr2 extends OAuth1 implements SocialInterface
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'http://www.tumblr.com/oauth/authorize';
    /**
     * @inheritdoc
     */
    public $requestTokenUrl = 'http://www.tumblr.com/oauth/request_token';
    /**
     * @inheritdoc
     */
    public $requestTokenMethod = 'POST';
    /**
     * @inheritdoc
     */
    public $accessTokenUrl = 'http://www.tumblr.com/oauth/access_token';
    /**
     * @inheritdoc
     */
    public $accessTokenMethod = 'POST';
    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'http://api.tumblr.com/v2';

    public $access_token;

    public $access_token_secret;

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return $this->api('/blog/good.tumblr.com/info', 'GET');
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'tumblr';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'Tumblr';
    }

    /**
     * get component set to class
     */
    public function setToken()
    {
        $token = new \yii\authclient\OAuthToken([
            'token' => $this->access_token,
            'tokenSecret' => $this->access_token_secret
        ]);
        return $this->setAccessToken($token);
    }

    /**
     * set key and call tumblr sdk
     * @return obj Tumblr Object
     */
    public function getTumblr()
    {
        $tumblr = new \Tumblr\API\Client($this->consumerKey, $this->consumerSecret);
        $tumblr->setToken($this->getAccessToken()->getToken(), $this->getAccessToken()->getTokenSecret());
        return $tumblr;
    }

    /**
     * get your info
     * @param  array  $params options for query
     * @return json           data
     */
    public function getMeProfile(array $params = array())
    {
        return $this->getTumblr()->getUserInfo();
    }

    /**
     * get your timeline
     * @param  array  $params options for query
     * @return json           data
     */
    public function getMeTimeline(array $params = array())
    {
        return $this->getTumblr()->getDashboardPosts();
    }

    /**
     * get profile user
     * @param  string $blogName name of blog
     * @param  array  $params   options for query
     * @return json             data
     */
    public function getUserProfile($blogName,array $params = array())
    {
        return $this->getTumblr()->getBlogInfo($blogName,$params);
    }

    /**
     * get timeline of user
     * @param  string $blogName name of blog
     * @param  array  $params   options for query
     * @return json             data
     */
    public function getUserTimeline($blogName,array $params = array())
    {
        return $this->getTumblr()->getBlogPosts($blogName,$params);
    }

    /**
     * get post detail
     * @param  string $blogName name of blog
     * @param  array  $params   option for query
     *         id               id of post
     * @return json             data
     */
    public function getPostDetail($blogName, array $params = array())
    {
        return $this->getTumblr()->getBlogPosts($blogName,$params);
    }
}