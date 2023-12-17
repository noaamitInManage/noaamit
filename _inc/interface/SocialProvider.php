<?php

/**
 * Created by PhpStorm.
 * User: inmanage
 * Date: 20/06/2017
 * Time: 16:57
 */
interface SocialProvider
{
    /**
     * @name has_access
     * @description Checks if we have access to the user profile
     * @return bool
     */
    public function has_access();

    /**
     * @name get_userArr
     * @description Returns user array
     * @return array
     */
    public function get_userArr();

    /**
     * @name get_birthday
     * @description Returns the user birthday timestamp
     * @return int
     */
    public function get_birthday();

    /**
     * @name get_current_position
     * @description Returns the current work position
     * @return string
     */
    public function get_current_position();

    /**
     * @name get_work_historyArr
     * @description Returns work history array from facebook
     * @return array
     */
    public function get_work_historyArr();

    /**
     * @name get_profile_picture_url
     * @description Returns url of user profile picture
     * @return string
     */
    public function get_profile_picture_url();

    /**
     * @name get_base64_profile_picture
     * @description Returns base64 of user profile picture
     * @return string
     */
    public function get_base64_profile_picture();

    /**
     * @name get_profile_url
     * @description Returns the url of the profile
     * @return string
     */
    public function get_profile_url();

    /**
     * @name connect_to_user_account
     * @description Updates the user with social information
     * @param $User
     * @return array|int
     */
    public function connect_to_user_account($User);
}