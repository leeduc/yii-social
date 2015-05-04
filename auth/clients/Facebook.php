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
 * Facebook allows authentication via Facebook OAuth.
 *
 * In order to use Facebook OAuth you must register your application at <https://developers.facebook.com/apps>.
 *
 * Example application configuration:
 *
 * ~~~
 * 'components' => [
 *     'authClientCollection' => [
 *         'class' => 'leeduc\authclient\Collection',
 *         'clients' => [
 *             'facebook' => [
 *                 'class' => 'leeduc\authclient\clients\Facebook',
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
 * @author Le Duc <lee.duc55@gmail.com>
 * @since 1.0
 */
class Facebook extends OAuth2 implements SocialInterface
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'https://www.facebook.com/dialog/oauth';
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://graph.facebook.com/oauth/access_token';
    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'https://graph.facebook.com';
    /**
     * @inheritdoc
     */
    public $scope = 'publish_pages,publish_actions,read_stream,email,user_likes,manage_pages';

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
        return 'facebook';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'Facebook';
    }

    /**
     * redirect auth login to facebook
     */
    public function redirectAuth(array $params = array())
    {
        $a = $this->buildAuthUrl($params);
        $b = \Yii::$app->getResponse()->redirect($a);
        return $this->redirect($b);
    }

    /**
     * get info of me
     * @param  array  $params options for query
     * @return json           data
     */
    public function getMeProfile(array $params = array())
    {
        return $this->api('me','GET',$params);
    }

    /**
     * meAccount get pages of me
     * @param  array  $params params for query
     * @return json   data
     */
    public function getMeAccount(array $params = array())
    {
        return $this->api('me/accounts','GET',$params);
    }

    /**
     * meAccount get pages of me
     * @param  array  $params params for query
     * @return json   data
     */
    public function getUserAccount($id, array $params = array())
    {
        return $this->api($id.'/accounts','GET',$params);
    }

    /**
     * get All me posts in timeline
     * @param  array  $params params for query
     * @return json   data
     */
    public function getMeTimeline(array $params = array())
    {
        return $this->api('me/feed','GET',$params);
    }

    /**
     * get All page posts
     * @param  int    $page_id page id
     * @param  array  $params params for query
     * @return json   data
     */
    public function getPagePosts($page_id, array $params = array())
    {
        return $this->api($page_id.'/posts','GET',$params);
    }

    /**
     * get Detail post
     * @param  int    $post_id post id
     * @param  array  $params params for query
     * @return json   data
     */
    public function getPostDetail($post_id, array $params = array())
    {
        return $this->api($post_id,'GET',$params);
    }

    /**
     * post to wall
     * @param  string $object name or id of object
     * @param  array  $params params for query
     *         'message'      message
     *         'from'         my app id
     *         'to'           object id
     *         'caption'      caption
     *         'name'         title
     *         'link'         http://www.example.com/'
     *         'picture'      thumbnail post
     *         'description'  description
     * @return json           data
     */
    public function postToWall($object = 'me', array $params = array())
    {
        return $this->api($object.'/feed','POST',$params);
    }

    /**
     * get user profile
     * @param  int    $id        user id
     * @param  array  $params    params for query
     * @return json              data
     */
    public function getUserProfile($id,array $params = array())
    {
        return $this->api($id,'GET',$params);
    }

    /**
     * get timeline of user
     * @param  int    $id        user id
     * @param  array  $params    params for query
     * @return json              data
     */
    public function getUserTimeline($id,array $params = array())
    {
        return $this->api($id.'/feed','GET',$params);
    }

    /**
     * refresh accessToken expire
     * @param  obj        $token   OAuthToken object
     * @param  array      $_params options for query
     * @return obj        OAuthToken object
     */
    public function refreshAccessToken(OAuthToken $token,$_params = array())
    {
        $params = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];
        $params = array_merge($token->getParams(), $params);
        $params = array_merge($params, $_params);
        // dump($params); die;
        $response = $this->sendRequest('POST', $this->tokenUrl, $params);

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }

    /**
     * fetch token of app to OAuth
     * @param  array  $params options for query
     * @return json           TOken object
     */
    public function fetchAppAccessToken(array $params = array())
    {
        $defaultParams = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type' => 'client_credentials',
        ];
        $response = $this->sendGetAppToken('GET', $this->tokenUrl, array_merge($defaultParams, $params));
        parse_str($response,$params);

        $token = $this->createToken(['params' => $params]);
        $this->setAccessToken($token);

        return $token;
    }

    /**
     * get token app
     * @param  string $method  method for request
     * @param  string $url     url for request
     * @param  array  $params  options for query
     * @param  array  $headers header for request
     * @return string          data
     */
    public function sendGetAppToken($method, $url, array $params = array(), array $headers = array())
    {
        $curlOptions = $this->mergeCurlOptions(
            $this->defaultCurlOptions(),
            $this->getCurlOptions(),
            [
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_URL => $url,
            ],
            $this->composeRequestCurlOptions(strtoupper($method), $url, $params)
        );
        $curlResource = curl_init();
        foreach ($curlOptions as $option => $value) {
            curl_setopt($curlResource, $option, $value);
        }
        $response = curl_exec($curlResource);
        $responseHeaders = curl_getinfo($curlResource);

        // check cURL error
        $errorNumber = curl_errno($curlResource);
        $errorMessage = curl_error($curlResource);

        curl_close($curlResource);

        if ($errorNumber > 0) {
            throw new Exception('Curl error requesting "' .  $url . '": #' . $errorNumber . ' - ' . $errorMessage);
        }
        if (strncmp($responseHeaders['http_code'], '20', 2) !== 0) {
            throw new InvalidResponseException($responseHeaders, $response, 'Request failed with code: ' . $responseHeaders['http_code'] . ', message: ' . $response);
        }
        return $response;
    }
}
