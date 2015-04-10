<?php
namespace leeduc\authclient;

interface SocialInterface
{
	/**
	 * get me account
	 */
    public function getMeProfile(array $params);
    public function getMeTimeline(array $params);

    /**
     * get user info
     */
    public function getUserProfile($id,array $params);
    public function getUserTimeline($id,array $params);

    /**
     * get post detail
     */
    public function getPostDetail($post_id, array $params);
}