<?php

class contactManager extends BaseManager
{
    /*----------------------------------------------------------------------------------*/
    /**
     * contactManager constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_topicsArr
     * @description Returns an array of contact topics
     * @param string $lang
     * @return array
     */
    public static function get_topicsArr($lang = '')
    {
        if (!$lang) {
            $lang = (isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;
        }

        $file_path  = $_SERVER['DOCUMENT_ROOT'] . '/_static/contact_topics.' . $lang . '.inc.php';
        if (!file_exists($file_path)) {
            return array();
        }

        $topicsArr = array();
        include($file_path); // $topicsArr

        return $topicsArr;
    }

    /*----------------------------------------------------------------------------------*/

    public static function send($topic_id, $message)
    {
        $User = User::getInstance();

        if (!$User->id) {
            return false;
        }

        if (!$User->email) {
            return false;
        }

        $topicsArr = self::get_topicsArr();
        if (!array_key_exists($topic_id, $topicsArr)) {
            return false;
        }
        $topic = $topicsArr[$topic_id]['title'];

        $Company = new companiesManager($User->company_id);
        $Site = new sitesManager($Company->site_id);

        $to = siteFunctions::get_env() == 'dev' ? $User->email : configManager::$support_email;
        $subject = $Site->salesforce_id . '; ' . $topic . '; ' . $User->salesforce_id;

        siteFunctions::send_mail($to, $subject, $message, array(), '', 0, ['ReplyToAddresses' => [$User->email]], true);

        return true;
    }

    /*----------------------------------------------------------------------------------*/
}