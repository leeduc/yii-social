<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace leeduc\authclient\clients;

use yii\authclient\OAuth2;
use yii\authclient\OAuthToken;
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
 *             'google' => [
 *                 'class' => 'leeduc\authclient\clients\GoogleOAuth',
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
 * @author Paul Klimov <klimov.paul@gmail.com>
 * @since 2.0
 */
class GoogleOAuth extends OAuth2 implements SocialInterface
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
    public $apiBaseUrl = 'https://www.googleapis.com/plus/v1';

    public $access_token;
    public $refresh_token;
    public $id_token;
    public $expires_in;
    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->scope === null) {
            $this->scope = implode(' ', [
                'https://www.googleapis.com/auth/plus.login',
                'https://www.googleapis.com/auth/plus.me',
                'https://www.googleapis.com/auth/userinfo.email',
                'https://www.googleapis.com/auth/userinfo.profile',
                'https://www.googleapis.com/auth/plus.stream.write'
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return $this->api('people/me', 'GET');
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'google';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'Google';
    }

    public function redirectAuth(array $params = array())
    {
        $a = $this->buildAuthUrl($params);
        $b = \Yii::$app->getResponse()->redirect($a);
        $this->redirect($b);
    }

    /**
     * refresh google token
     * @param  OAuthToken $token Token object
     * @return obj               Token object
     */
    public function refreshAccessToken(\yii\authclient\OAuthToken $token)
    {
        $this->setAccessToken($token);

        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'refresh_token',
            'refresh_token' => $this->refresh_token,
        ];
        // $params = array_merge($token->getParams(), $params);
        // unset($params['expires_in']);
        // unset($params['token_type']);
        // unset($params['id_token']);
        // unset($params['access_token']);
        $response = $this->sendRequest('POST', $this->tokenUrl, $params);

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }

    /**
     * get you info
     * @param  array  $params params for query
     * @return json           data
     */
    public function getMeProfile(array $params = array())
    {
        return $this->api('people/me','GET',$params);
    }

    /**
     * get time line of me
     * @param  array  $params params for query
     * @return json           data
     */
    public function getMeTimeline(array $params = array())
    {
        return $this->api('people/me/activities/public','GET',$params);
    }

    /**
     * get timeline of user
     * @param  int    $user_id  user id
     * @param  string $username user screen name
     * @return json             data
     */
    public function getUserTimeline($user_id = null,array $params = array())
    {
        return $this->api('people/'.$user_id.'/activities/public','GET',$params);
    }

    /**
     * get user profile
     * @param  int    $user_id  user id
     * @param  string $username user screen name
     * @return json             data
     */
    public function getUserProfile($user_id = null,array $params = array())
    {
        return $this->api('people/'.$user_id,'GET', array_merge($params,[
        ]));
    }

    /**
     * get post detail
     * @param  int    $id post id
     * @return json       data
     */
    public function getPostDetail($post_id, array $params = array())
    {
        return $this->api('activities/'.$post_id,'GET',$params);
    }
}
