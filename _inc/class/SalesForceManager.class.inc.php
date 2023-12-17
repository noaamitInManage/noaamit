<?php

/**
 * Created by PhpStorm.
 * User: inmanage
 * Date: 21/06/2017
 * Time: 10:47
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/vendors/SalesForce.inc.php');

class SalesForceManager extends \Inmanage\SalesForce\SalesForceRequest
{
    private static $instnace = null;

    /**
     * Date format in SalesForce
     * @var string
     */
    private $date_format = 'Y-m-d';

    /**
     * List of field keys to be converted from ts to SalesForce format
     * @var array
     */
    private $date_fieldsArr = array(
        'birthday',
    );

    /**
     * User (Contact) fields conversions array
     * @var array
     */
    private $user_fields_conversionArr = array(
        'one_liner' => 'Headline__c',
        'about_me' => 'App_About_Me__c',
        'linkedin_profile_url' => 'Linkedin__c',
        'facebook_profile_url' => 'Facebook__c',
        'job_title' => 'Title',
        'birthday' => 'Birthdate',
        'gender' => 'Gender__c',
    );

    /**
     * Company (Account) fields conversions array
     * @var array
     */
    private $company_fields_conversionArr = array(
        'one_liner' => 'Headline__c',
        'about_us' => 'App_About_Us__c',
        'website_url' => 'Website',
        'custom_li_profile_url' => 'LinkedIn__c',
        'phone' => 'Phone',
    );

    //-------------------------------------------------------------------------------------------------------------------//
    /**
     * SalesForceManager constructor.
     */
    function __construct()
    {
        parent::__construct();
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function getInstance()
    {
        if (null === self::$instnace) {
            self::$instnace = new self();
        }
        return self::$instnace;
    }

    //-------------------------------------------------------------------------------------------------------------------//
    /**
     * @name convert_fieldsArr
     * @description Converts field keys from our convension to SalesForce's convension
     * @param $fieldsArr
     * @param $conversionArr
     * @return array
     */
    public function convert_fieldsArr($fieldsArr, $conversionArr)
    {
        $new_fieldsArr = array();

        foreach ($fieldsArr as $key => $value) {
            $new_key = array_key_exists($key, $conversionArr) ? $conversionArr[$key] : $key;
            $value = in_array($key, $this->date_fieldsArr) ? date($this->date_format, $value) : $value;

            $new_fieldsArr[$new_key] = $value;
        }

        return $new_fieldsArr;
    }

    //-------------------------------------------------------------------------------------------------------------------//
    /**
     * @name update_user
     * @description Updates a user in SalesForce
     * @param $fieldsArr
     * @param $search_value
     * @param string $search_by
     * @return mixed
     */
    public function update_user($fieldsArr, $search_value, $search_by = 'Id')
    {
        $uriArr = array(
            'sobjects',
            'Contact',
            $search_by,
            $search_value
        );

        $fieldsArr = $this->convert_fieldsArr($fieldsArr, $this->user_fields_conversionArr);

        return $this->update_record($uriArr, $fieldsArr);
    }

    //-------------------------------------------------------------------------------------------------------------------//
    /**
     * @name update_company
     * @description Updates a company in SalesForce
     * @param $fieldsArr
     * @param $search_value
     * @param string $search_by
     * @return mixed
     */
    public function update_company($fieldsArr, $search_value, $search_by = 'Id')
    {
        $uriArr = array(
            'sobjects',
            'Account',
            $search_by,
            $search_value
        );

        $fieldsArr = $this->convert_fieldsArr($fieldsArr, $this->company_fields_conversionArr);

        return $this->update_record($uriArr, $fieldsArr);
    }

    //-------------------------------------------------------------------------------------------------------------------//
}