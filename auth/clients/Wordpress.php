<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace leeduc\authclient\clients;

use yii\authclient\OAuth2;
use yii\authclient\OAuthToken;
/**
 * Facebook allows authentication via Facebook OAuth.
 *
 * In order to use Facebook OAuth you must register your application at <https://developers.facebook.com/apps>.
 *
 * Example application configuration:
 *
 * ~~~
 * 'components' => [
 *     'authClientCollection' => [
 *         'class' => 'yii\authclient\Collection',
 *         'clients' => [
 *             'facebook' => [
 *                 'class' => 'yii\authclient\clients\Facebook',
 *                 'clientId' => 'facebook_client_id',
 *                 'clientSecret' => 'facebook_client_secret',
 *             ],
 *         ],
 *     ]
 *     ...
 * ]
 * ~~~
 *
 * @see https://developers.facebook.com/apps
 * @see http://developers.facebook.com/docs/reference/api
 *
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class Wordpress extends OAuth2
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'https://public-api.wordpress.com/oauth2/authorize';
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://public-api.wordpress.com/oauth2/token';
    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://public-api.wordpress.com/rest/v1.1';
    /**
     * @inheritdoc
     */
    public $scope = 'read';

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return $this->api('me', 'GET');
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'wordpress';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'Wordpress';
    }

    public function refreshAccessToken(OAuthToken $token,$_params = array())
    {
        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token'
        ];
        $params = array_merge($token->getParams(), $params);
        $params = array_merge($params, $_params);
        $response = $this->sendRequest('POST', $this->tokenUrl, $params);

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }
}
