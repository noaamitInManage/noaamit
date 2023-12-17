<?php

/**
 * Created by PhpStorm.
 * User: Sara
 * Date: 08/01/2018
 * Time: 11:39
 */

include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/siteFunctions.inc.class.php');//siteFunctions

class autoModuleManager
{

    private $ts = 0;

    public $element_name = '';
    public $element_title = '';
    public $category = '';
    public $tb_name = '';

    private $templatesArr = array();

    private $new_filesArr = array();

    /*----------------------------------------------------------------------------------*/

    function __construct($element_name, $category, $element_title = '')
    {
        $this->ts = time();
        $this->element_name = $element_name;
        $this->element_title = $element_title;
        $this->category = $category;
        $this->tb_name = 'tb_' . $this->element_name . 's';

        $this->templatesArr = array(
            1 => '/salat2/_inc/tmplFiles/tmpl.php.tmp', // salat module
            2 => '/salat2/_inc/tmplFiles/tmpl.fields.inc.php.tmp', // salat module fileds
            3 => '/salat2/_inc/tmplFiles/tmpl.class.php.tmp', // class
            4 => '/salat2/_inc/tmplFiles/tmplManager.class.inc.php.tmp' // manager
        );

        $this->new_filesArr = array(
            1 => '/salat2/'. $this->category .'/'. $this->element_name .'.php',
            2 => '/salat2/'. $this->category .'/modules_fields/'. $this->element_name .'.fields.inc.php',
            3 => '/salat2/_inc/UpdateStaticFiles/'. $this->element_name .'.class.php',
            4 => '/_inc/class/module/'. $this->element_name .'Manager.class.inc.php'
        );
    }

    /*----------------------------------------------------------------------------------*/

    function __destruct()
    {

    }

    /*----------------------------------------------------------------------------------*/

    public function __set($var, $val)
    {
        $this->$var = $val;
    }

    /*----------------------------------------------------------------------------------*/

    public function __get($var)
    {
        return $this->$var;
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * Create tables (default + lang)
     */

    public function create_tables() {
        $Db = Database::getInstance();

        //create default table
        $query = "CREATE TABLE IF NOT EXISTS `{$this->tb_name}` (
                      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
                      `order_num` int(11) NOT NULL,
                      `last_update` int(11) unsigned NOT NULL,
                      PRIMARY KEY (`id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

        $response = $Db->query($query);

        if($response) {
            //create lang table
            $query = "CREATE TABLE IF NOT EXISTS `{$this->tb_name}_lang` (
                      `obj_id` int(10) NOT NULL,
                      `lang_id` int(10) NOT NULL,
                      `title` varchar(255) COLLATE utf8_general_ci NOT NULL,
                      PRIMARY KEY (`obj_id`,`lang_id`)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $lang_response = $Db->query($query);
        }

        return isset($lang_response) && $lang_response;
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * Check tables exist (default + lang)
     */

    public function check_tables_exist() {
        $Db = Database::getInstance();

        //check default table
        $query = "SHOW TABLES LIKE `{$this->tb_name}`";
        $response = $Db->query($query);

        if($Db->get_num_rows($response)) {
            return true;
        } else {
            //check lang table
            $query = "SHOW TABLES LIKE `{$this->tb_name}_lang`";
            $response = $Db->query($query);
            if($Db->get_num_rows($response)) {
                return true;
            }
        }

        return false;
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * Create files from template
     */

    public function create_files() {

        $replaceArr = array(
            'tmpl' => $this->element_name,
            'tmpl_title' => $this->element_title,
        );

        foreach ($this->templatesArr as $tmpl_key => $tmpl_file) {
            $new_content = siteFunctions::replace_template_tags( $_SERVER['DOCUMENT_ROOT'] . $tmpl_file, $replaceArr);
            file_put_contents( $_SERVER['DOCUMENT_ROOT'] . $this->new_filesArr[$tmpl_key], $new_content);
        }

        return;
    }

    /*----------------------------------------------------------------------------------*/

    public function create_static_folder() {

        $new_dir = $_SERVER['DOCUMENT_ROOT'] . '/_static/' . $this->element_name . 's';

        if (!is_dir($new_dir)) {
            @mkdir($new_dir, 0777);
            chmod($new_dir, 0777);
        }
        return $new_dir;
    }

    /*----------------------------------------------------------------------------------*/

    public function check_files_exist() {

        foreach ($this->new_filesArr as $new_file_key => $new_file) {
            if(file_exists( $_SERVER['DOCUMENT_ROOT'] . $new_file)) {
                return true;
            }
        }

        return false;
    }

    /*----------------------------------------------------------------------------------*/

}