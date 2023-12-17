<?php
/**
 * File: seo.class.inc.php
 * Author: Gal Zalait (galzalait@gmail.com)
 * Last Modified: 31/10/11
 * Last Modified: 04/02/12
 * Last Modified: 09/04/12 add strict address seo addon
 * Last Modified: 18/04/12 add meta tags auto load
 * Last Modified: 26/07/12 add url '+' char support
 * Last Modified: 19/03/13 add multi lang full support
 * Last Modified: 27/03/13 add SeoMobile class
 * @version 1.0
 * @desc: genric web site seo support in duble slashes mode and suppoort the old links method
 *        you can copy this to any web site just add the information to function
 *
 *          devote 5 min to read the code dont be lazy  !!!!
 *
 **/
define('_ServerRoot', $_SERVER['DOCUMENT_ROOT']);

class Seo extends BaseManager
{

    public $url = '';
    public $lang_prefix = '';
    public $multi_lang_site = true;
    public $lang_id = '';
    public $lang = '';

    const   StaticPrefix = '/_static/';
    const   StaticSuffix = '.inc.php';

    /*----------------------------------------------------------------------------------*/

    function __construct($lang_id = '')
    {
        parent::__construct();
        $this->ts = time();
        $urlArr = explode('.', $_SERVER['HTTP_HOST']);
        $this->lang_prefix = ((strlen($urlArr[0]) == 2)) ? $urlArr[0] : (('www') ? '' : 'm');

        if (!in_array($this->lang_prefix, array('', 'm'))) {
            // change site lang
            include($_SERVER['DOCUMENT_ROOT'] . '/_static/languages.inc.php');//$languagesArr
            foreach ($languagesArr AS $key => $value) {
                if ($this->lang_prefix == $value['title']) {
                    $_SESSION['lang'] = $value['title'];
                    $_SESSION['lang_id'] = $key;
                }
            }
        } else {
            $_SESSION['lang'] = default_lang;
            $_SESSION['lang_id'] = default_lang_id;
        }

        $this->lang_id = (isset($_SESSION['lang_id']) && ($_SESSION['lang_id'])) ? $_SESSION['lang_id'] : default_lang_id;
        if ($lang_id) {
            $this->lang_id = $lang_id;
            if (!isset($languagesArr)) {
                include($_SERVER['DOCUMENT_ROOT'] . '/_static/langagues.inc.php');//$languagesArr
            }
            $this->lang = $languagesArr[$lang_id]['title'];
        }

        $this->lang = ($this->lang) ? $this->lang : (isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang;

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
     * get_category
     *
     * @param unknown_type $mdlName
     * @param (int) $mdl_id if by module id return the moduleArr by session lang
     * @return (array) moduleArr
     */
    public function get_category($mdlName = '', $mdl_id = 0)
    {
        $categoryArr = self::is_category('_blank', true);

        foreach ($categoryArr AS $key => $value) {

            if ($mdlName) {
                if ($value['mdl_name'] == $mdlName) {
                    return array($key, $categoryArr[$key]);
                }
            }
            if ($mdl_id && isset($value['module_id'])) {
                if (($value['module_id'] == $mdl_id)) {
                    return array($key, $categoryArr[$key]);
                }
            }
        }

    }


    /*----------------------------------------------------------------------------------*/
    /**
     * function is_category
     *
     * @param $category name in herbrew
     * @return static file and module details  using it for parse url
     *  directAccess ->  access in o(1) to link
     *  directAccess -> smart dir = true  - 1000 items per dir -
     *
     *
     * for making  url to dynamic pages
     *
     *
     * to add module to new method link just add this array to $categoryArr
     * example
     *
     *     "[DESIRE NAME]"=>array(
     *        "file"=>'categoryFlat',   /// static file name with all items the static file should be in this type:  AND with the suffix .inc.php
     *                                                                                                $categoryFlatArr=array (
     *                                                                                                          1 => 'a',
     *                                                                                                          544 => 'b',
     *                                                                                                          );
     *        "arr_name"=>'categoryFlatArr', // static file arr name
     *        "mdlId"=>'10',               // front module ID
     *        "mdlName"=>'category',
     *        "seoStrict"=>true,            // if this option is true the system will make auto 301 redirect if url is not valid
     *        "directAccess"=>array(
     *                                 "smart_dir"=>false,           // if this option is true the static file split 1000 in dir
     *                                "file"=>'category/category-',  // direct file url
     *                                "arr_name"=>'categoryArr',  // direct file arr name
     *                       )
     *        ),
     */
    public function is_category($category, $get = null)
    {
        global $force_lang;
        if (isset($force_lang)) {
            $lang = $force_lang;
        } else {
            $lang = (isset($this->lang)) ? $this->lang : ((isset($_SESSION['lang']) && ($_SESSION['lang'])) ? $_SESSION['lang'] : default_lang);
        }

        @include($_SERVER['DOCUMENT_ROOT'] . '/_static/seo.' . $lang . '.inc.php');//$categoryArr
        /*	  $categoryArr=array(
                          "tour"=>array(
                                "file"=>'tours',
                                "arr_name"=>'toursArr',
                                "mdlId"=>'22',
                                "mdlName"=>'tour',
                                "priority"=>'0.75',
                                "seoStrict"=>true,
                                "directAccess"=>array(
                                                         "smart_dir"=>true,
                                                        "file"=>'tours/en/tour-',
                                                        "arr_name"=>'tourArr',
                                               )
                          ),



          );
            */

        if ($get == true) {
            return $categoryArr;
        }
        if (!empty($categoryArr[$category])) {
            return is_array($categoryArr[$category]) ? $categoryArr[$category] : '';
        }
    }

    /*----------------------------------------------------------------------------------*/

    /**
     *  static file must to have a title field
     *  (array/string) - if $mdl = string get direct url , else if $mdl is array with mdl_id param return thr url with lang sub domain
     * @return url
     */
    public function getUrl($mdl, $obj = null, $additionalData = null)
    {

        $mdl_id = '';
        if (is_array($mdl)) {
            $mdl_id = $mdl['mdl_id'];
            $mdl = '';
        }

        list($modulePrefix, $moduleArr) = $this->get_category($mdl, $mdl_id);

        $url = '';
        if ($obj) {
            if (is_array($moduleArr['directAccess'])) {   // O(1) direct acsess to url
                if ($moduleArr['directAccess']['smart_dir']) {
                    $tmpArr = explode('/', $moduleArr['directAccess']['file']);

                    $holder = array_pop($tmpArr);
                    $tmpArr[] = self::get_item_dir($obj);

                    if ($this->multi_lang_site) {
                        $tmpArr[] = $this->lang;
                    }
                    $tmpArr[] = $holder;
                    //$tmpArr[]=$holder;
                    $moduleArr['directAccess']['file'] = implode('/', $tmpArr);
                }
                $itemArr = @self::includeStatic($moduleArr['directAccess']['file'] . $obj, $moduleArr['directAccess']['arr_name']);
                $url = self::urlize($itemArr['title']);
            } else {
                $itemsArr = @self::includeStatic($moduleArr['file'], $moduleArr['arr_name']);

                $url = self::urlize($itemsArr[$obj]);
            }
        }
        /*
        else{
           return static module
                    dont need for this web site
        }
        */


        if (isset($additionalData['site_map'])) {
            unset($additionalData);
        }
        $extra_param = is_array($additionalData) ? '?' . http_build_query($additionalData, '?') : '';
        if (default_lang_id != $this->lang_id) {
            return 'http://' . $this->lang_prefix . '.' . $_SERVER['HTTP_HOST'] . '/' . implode('/', array(trim($modulePrefix), trim($obj), trim($url))) . '/' . $extra_param;
        }
        return implode('/', array(trim($modulePrefix), trim($obj), trim($url))) . '/' . $extra_param; // def lang
    }

    /*----------------------------------------------------------------------------------*/

    public function parseUrl($url = null, $queryString = null)
    {

        $urlArr = array(
            "category_name" => "",
            "id" => "",
            "title" => "",
        );
        $_path = '';
        if (trim($url) != '/') {
            @list($_,
                $urlArr['category_name'],
                $urlArr['id'],
                $urlArr['title']
                ) = explode('/', $url);

            if ($this->lang_prefix) {
                list($_,
                    $urlArr['lang'],
                    $urlArr['category_name'],
                    $urlArr['id'],
                    $urlArr['title']
                    ) = explode('/', $this->lang_prefix . '/' . $url);
                $urlArr['lang'] = $this->lang_prefix;
            }
        }

        foreach ($urlArr AS $key => $value) {
            $urlArr[$key] = trim(self::decode($value));
        }

//die('<hr /><pre>' . print_r($urlArr, true) . '</pre><hr />');

        if ($moduleArr = self::is_category(trim($urlArr['category_name']))) {

            unset($itemsArr);
            switch ((bool)$moduleArr['directAccess']['smart_dir']) {
                case true:

                    if ($this->multi_lang_site) {

                        $temp = array_shift(explode('/', $moduleArr['directAccess']['file']));
                        $file_path = $_SERVER['DOCUMENT_ROOT'] . '/_static/' . str_replace($temp . '/', $temp . '/' . get_item_dir($urlArr['id']) . '/' . $this->lang . '/', $moduleArr['directAccess']['file']) . $urlArr['id'] . self::StaticSuffix;

                        if (is_file($file_path)) {
                            $_path = $file_path;
                            $_path = str_replace(array(self::StaticPrefix, self::StaticSuffix), array('/', ''), '/' . $_path);
                        }
                    } elseif (is_file(_ServerRoot . self::StaticPrefix . '/' . str_replace('/', '/' . self::get_item_dir($urlArr['id']) . '/', $moduleArr['directAccess']['file']) . $urlArr['id'] . self::StaticSuffix)) {
                        $_path = str_replace('/', '/' . self::get_item_dir($urlArr['id']) . '/', $moduleArr['directAccess']['file']) . $urlArr['id'];
                    }
                    break;
                case false:
                default:
                    if (is_file(_ServerRoot . self::StaticPrefix . '/' . $moduleArr['directAccess']['file'] . $urlArr['id'] . self::StaticSuffix)) {//self::get_item_dir
                        $_path = $moduleArr['directAccess']['file'] . $urlArr['id'];

                    }
                    break;
            }
            if (!$_path) {// only if file not exists check all
                $itemsArr[$urlArr['id']] = @self::includeStatic($moduleArr['file'], $moduleArr['directAccess']['arr_name']); //$itemsArr
            } else {

                $itemsArr[$urlArr['id']] = @self::includeStatic(str_replace($_SERVER['DOCUMENT_ROOT'], '', $_path), $moduleArr['directAccess']['arr_name']); //$itemsArr

            }

            if ($itemsArr[$urlArr['id']]) { /* only if id exists */
                $answerArr = array(
                    "mdlName" => $moduleArr['mdl_name'],
                    "mdlId" => $moduleArr['module_id'],
                    "objID" => $urlArr['id'],
                    "additionalData" => array('redirect' => array("404" => false)),
                );

                if (isset($moduleArr['seo_strict']) && $moduleArr['seo_strict']) {

                    $item_title = (is_array($itemsArr[$urlArr['id']])) ? self::urlize($itemsArr[$urlArr['id']]['title']) : self::urlize($itemsArr[$urlArr['id']]);
                    if (str_replace(array('+'), array(' '), $item_title) != $urlArr['title']) {
                        self::header_301('/' . implode('/', array($urlArr['category_name'], $urlArr['id'], $item_title)) . '/');
                    }
                    global $metaTags;
                    $desc = '';
                    $commDescripation = array("content", "decripation", "about");
                    foreach ($commDescripation AS $key => $value) {
                        if (isset($itemsArr[$urlArr['id']][$value])) {
                            $desc .= $itemsArr[$urlArr['id']][$value];
                        }
                    }

                    $metaTags = array(
                        'title' => isset($itemsArr[$urlArr['id']]['title']) ? $itemsArr[$urlArr['id']]['title'] : '',
                        'keywords' => '',
                        'description' => chop_str(strip_tags(trim($desc)), 280),
                    );

                    return $answerArr;
                } else {

                    return $answerArr = array(
                        "mdlName" => $moduleArr['mdl_name'],
                        "mdlId" => $moduleArr['module_id'],
                        "objID" => $urlArr['id'],
                        "additionalData" => array('redirect' => array("404" => true)),
                    );
                }
            }
        }

        //	self::header_404('/404');  only if the seo class work alone
        return false;
    }

    /*----------------------------------------------------------------------------------*/


    public function getStaticUrl($mdlID = 0, $innerID = 0, $langId = '')
    {
        global $urlAliasArr;
        if (!$langId) {
            $langId = $_SESSION['lang_id'];
        }
        if (default_lang_id != $langId) {
            return 'http://' . $_SERVER['HTTP_HOST'] . '/' . str_replace(' ', '_', $urlAliasArr["{$mdlID}-{$innerID}-{$langId}"]);
        }
        return str_replace(' ', '_', $urlAliasArr["{$mdlID}-{$innerID}-{$langId}"]);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * clean unfriendly characters
     */
    public static function urlize($str)
    {
        return trim(str_replace(array(' ', '&', '\'', '"', '`', "'", '?', '\\', '/', ',', '%'), array('-', '_', '', '`', '_', '_', '', '-', '-', '_', '_'), $str));
    }

    /*----------------------------------------------------------------------------------*/
    /**
     *  every 1000 in dir
     * @return  ceil(intval($id)/1000)
     */
    public static function get_item_dir($id)
    {
        if ($id) {
            return ceil(intval($id) / 1000);
        }
    }

    /*----------------------------------------------------------------------------------*/

    public static function header_301($url)
    {
        header_status("301 Moved Permanently");
        header("location: " . $url);
        exit();
    }

    /*----------------------------------------------------------------------------------*/

    public static function header_302($url)
    {
        //header_status("301 Moved Permanently");
        header("location: " . $url, true, 302);
        exit();
    }

    /*----------------------------------------------------------------------------------*/

    public static function header_404($url = '404.php')
    {
        global $res;
        header('HTTP/1.0 404 Not Found', true, 404);
        $res['module_id'] = 4;
        $res['inner_id'] = 404;

    }

    /*----------------------------------------------------------------------------------*/
    /**
     * urldecode
     */
    public static function decode($str)
    {
        return urldecode($str);
    }

    /*----------------------------------------------------------------------------------*/

    private static function includeStatic($path, $arr_name, $debug = 0)
    {
        ${$arr_name} = array();

        @include(_ServerRoot . self::StaticPrefix . $path . self::StaticSuffix); // $arr_name

        if ($debug) {
            die('<hr /><pre>' . print_r(${$arr_name}, true) . '</pre><hr />');
        }
        return ${$arr_name};
    }

    /*----------------------------------------------------------------------------------*/


}

class SeoMobile extends Seo
{

    public function load_mobile_extra_url()
    {
        global $urlAliasArr, $moduleNameArr, $mobileOnlyModulesArr;
        $urlAliasArr["400-1"] = 'דיאלוג';

        foreach ($mobileOnlyModulesArr AS $key => $value) {
            if ($value['is_static']) {
                $urlAliasArr["4-{$value['module_id']}-{$value['lang_id']}"] = $value['url'];
                $moduleNameArr[$value['module_id']] = array(1 => $value['module_name']);
            } else {
                $urlAliasArr["{$value['module_id']}-{$value['obj_id']}-{$value['lang_id']}"] = $value['url'];
                $moduleNameArr[$value['module_id']] = array(0 => $value['module_name']);
            }
        }

    }
}

/**
 * File: seo.class.inc.php
 * Class Name: SiteMap
 * Author: Gal Zalait (galzalait@gmail.com)
 * Last Modified: 19/06/2012
 * @version 1.0
 * @desc: genric web site  sitemap maker
 *          this class take all the links thet belong to Seo class and also work with old link method
 *        the main sitemap will be at /_static/sitemap.xml
 *            the rest of the site map in /_static/en.sitemap.xml
 * @Last Modified: [19/06/2012]
 * @Last Modified: [20/06/2012] System will  will recognize Last change of file
 * @Last Modified: [07/01/2014] multi lang fix - Now the system generates a sitemap for each language defined in the salat
 *
 **/
class SiteMap extends Seo
{

    public static function make_site_map_xml($lang = '')
    {
        self::throw_xml_headers();
        self::create_xml_header();
        $categoryArr = self::is_category(999, true);
        $prefix = 'http://' . $_SERVER['HTTP_HOST'] . '/';
        /** build all files from seo class **/
        $ts = time();
        self::create_entity($prefix, $ts, "daily", '1.00');

        if ($lang) {
            $lang_id = self::get_lang_id($lang);
        }
        if (($lang_id) && (default_lang_id != $lang_id)) {
            $prefix = 'http://' . end(explode('.', $_SERVER['HTTP_HOST']));
        }
        $Seo = new Seo();
        foreach ($categoryArr AS $key => $moduleArr) {
            //die('<hr /><pre>' . print_r(array($moduleArr['file_name'],$moduleArr['arr_name']), true) . '</pre><hr />');
            $itemsArr = @self::includeStatic($moduleArr['file_name'], $moduleArr['arr_name'], $lang, 0);
            foreach ($itemsArr AS $item_id => $item_name) {
                $file_time = 0;
                if ($moduleArr['directAccess']['smart_dir']) {
                    $tmpArr = explode('/', $moduleArr['directAccess']['file']);
                    $holder = array_pop($tmpArr);
                    $tmpArr[] = self::get_item_dir($item_id);
                    $tmpArr[] = $lang;
                    $tmpArr[] = $holder;
                    $moduleArr['directAccess']['file'] = implode('/', $tmpArr);
                    $file_time = fileatime($_SERVER['DOCUMENT_ROOT'] . self::StaticPrefix . $moduleArr['directAccess']['file'] . $item_id . self::StaticSuffix);
                } else {
                    $file_time = fileatime($_SERVER['DOCUMENT_ROOT'] . self::StaticPrefix . $moduleArr['directAccess']['file'] . $item_id . self::StaticSuffix);
                }
                if ($moduleArr['priority'] != 'skip') {
                    /*						if($Seo->getUrl(array('mdl_id'=>$moduleArr['module_id'],$item_id,array('site_map'=>true))=='Array')){
                                                die('<hr /><pre>' . print_r(array($Seo->getUrl(array('mdl_id'=>85),$item_id,array('site_map'=>true)),$moduleArr['module_id'],$Seo,$item_id,$Seo->getUrl(array('mdl_id'=>85,$item_id),array('site_map'=>true)),$moduleArr), true) . '</pre><hr />');
                                            }*/
                    $url = $Seo->getUrl(array('mdl_id' => $moduleArr['module_id']), $item_id, array('site_map' => true));
                    if ($url == 'Array') {
                        die('<hr /><pre>' . print_r(array($p_loc, $p_lastmod, $p_changefreq, $p_priority), true) . '</pre><hr />');
                    }
                    list($p_loc, $p_lastmod, $p_changefreq, $p_priority) = array(
                        $prefix . $url,
                        ($file_time) ? $file_time : $ts,
                        "daily",
                        isset($moduleArr['priority']) ? $moduleArr['priority'] : 0.80
                    );
                    if ($url == 'Array') {
                        die('<hr /><pre>' . print_r(array($p_loc, $p_lastmod, $p_changefreq, $p_priority), true) . '</pre><hr />');
                    }
                    self::create_entity($p_loc, $p_lastmod, $p_changefreq, $p_priority);

                }
            }
        }
        /** const page **/
        global $urlAliasArr;// also Support old links system
        foreach ($urlAliasArr AS $key => $value) {
            list($m, $o, $l) = explode('-', $key);
            if ($l == $lang_id) {
                self::create_entity($prefix . $value, $ts, "weekly", 0.85);
            }
        }

        self::create_xml_footer();

    }

    /*----------------------------------------------------------------------------------*/

    public static function create_site_map($file_link = '/_static/sitemap.xml')
    {
        global $force_lang;
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/languages.inc.php');//$languagesArr
        foreach ($languagesArr as $langID => $lang) {
            if (default_lang_id != $langID) {
                $file_link = '/_static/' . $lang['title'] . '.sitemap.xml';
            } else {
                $file_link = '/_static/sitemap.xml';
            }
            ob_start();
            $force_lang = $lang['title'];
            self::make_site_map_xml($lang['title']);
            $c = ob_get_contents(); // define $c for next command
            chmod($_SERVER['DOCUMENT_ROOT'] . $file_link, 0777);
            $hndl = fopen($_SERVER['DOCUMENT_ROOT'] . $file_link, 'w');
            fwrite($hndl, $c);
            fclose($hndl);
            ob_end_clean();
        }
        //exit('sitemap is up to date!');
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_lang_id($lang)
    {
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/languages.inc.php');//$languagesArr

        $langTitle = is_array($lang) ? $lang['title'] : $lang;

        foreach ($langTitle AS $key => $langArr) {
            if ($langArr['title'] == $lang) {
                return $key;
            }
        }
    }

    /*----------------------------------------------------------------------------------*/

    public static function create_xml_header()
    {
        print "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
        print "<urlset xmlns=\"http://www.google.com/schemas/sitemap/0.84\">\n";
    }

    /*----------------------------------------------------------------------------------*/

    public static function create_entity($p_loc, $p_lastmod, $p_changefreq, $p_priority)
    {
        //$loc = htmlentities($p_loc,ENT_COMPAT);
        $loc = $p_loc;
        $loc = str_replace("'", "&apos;", $loc);
        print "\t<url>\n";
        print "\t\t<loc>" . $loc . "</loc>\n";
        $lastmod = ($p_lastmod != "" ? date('Y-m-d', $p_lastmod) : date('Y-m-d'));
        print "\t\t<lastmod>" . $lastmod . "</lastmod>\n";
        $changefreq = ($p_changefreq != "" ? $p_changefreq : DEFAULT_FREQUENCY);
        print "\t\t<changefreq>" . $changefreq . "</changefreq>\n";
        $priority = ($p_priority != "" ? $p_priority : DEFAULT_PRIORITY);
        print "\t\t<priority>" . $priority . "</priority>\n";
        print "\t</url>\n";
    }

    /*----------------------------------------------------------------------------------*/

    public static function create_xml_footer()
    {
        print "</urlset>\n";
    }

    /*----------------------------------------------------------------------------------*/

    public static function throw_xml_headers()
    {
        header("Cache-Control: no-cache, must-revalidate");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Content-type: text/xml; charset=utf-8");
    }

    /*----------------------------------------------------------------------------------*/
    // $lang en,he...
    private static function includeStatic($path, $arrName, $lang = '', $debug = 0)
    {
        ${$arrName} = array();
        if ($lang) {
            @include(_ServerRoot . self::StaticPrefix . "{$path}.{$lang}" . self::StaticSuffix); // $arrName
        } else {
            @include(_ServerRoot . self::StaticPrefix . $path . self::StaticSuffix); // $arrName
        }
        if ($debug) {
            die('<hr /><pre>' . print_r(${$arrName}, true) . '</pre><hr />');
        }
        return ${$arrName};
    }

}

?>