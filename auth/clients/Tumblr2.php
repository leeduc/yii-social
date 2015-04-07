<?php
namespace yii\authclient\clients;

use yii\authclient\OAuth1;
class Tumblr2 extends OAuth1
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

    public function getToken()
    {
        return $this->_accessToken;
    }
}