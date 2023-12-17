<?php
/**
 * Created by PhpStorm.
 * User: inmanage
 * Date: 11/21/2019
 * Time: 3:36 PM
 */

class MetaTagsManager extends BaseManager {
    public const META_TITLE = "title";
    public const META_DESCRIPTION = "description";
    public const META_KEYWORDS = "keywords";

    private static $metaTagsNoIndexArr=null;
    private static $staticModuleNameArr=null;
    private static $front_salat_modules_linkArr=null;
    private static $project_title = null;
    private static $default_page_title = '';
    private $mdl_id;
    private $object_id;
    private $salat_module_id;
    private $meta_tagsArr = [
        'title'=>'',
        'keywords'=>'',
        'description'=>'',
    ];
    private $metaArr = [];

    private $tagsArr = [];

    public function __construct($mdl_id=0, $object_id=0,$db_connection = true)
    {
        parent::__construct($db_connection);
        $this->mdl_id = $mdl_id;
        $this->object_id = intval($object_id);
        $this->data_loading();

    }

    /**
     * loads static data from files to parse the process.
     */
    private function data_loading(){
        global $_project_title_main;
        static::$project_title = $_project_title_main;
        if(static::$metaTagsNoIndexArr===null){
            include($_SERVER['DOCUMENT_ROOT'].'/_static/meta-tags/noindex.inc.php');//metaTagsNoIndexArr
            static::$metaTagsNoIndexArr = $metaTagsNoIndexArr;
        }
        if(static::$staticModuleNameArr===null){
            include($_SERVER['DOCUMENT_ROOT'] . "/_static/static_modules.inc.php"); // $staticModuleNameArr
            static::$staticModuleNameArr = $staticModuleNameArr;
        }
        if(static::$front_salat_modules_linkArr===null){
            include($_SERVER['DOCUMENT_ROOT'] . '/_static/front_salat_modules_link.inc.php'); // $front_salat_modules_linkArr
            static::$front_salat_modules_linkArr = $front_salat_modules_linkArr;
        }
    }

    /**
     * builds the metatags
     * @return array
     */
    public function resolve(){
        if(!$this->static_load()){
            $this->db_load();
        }

        $this->handle_tags();

        return [
            "meta_tags"=>$this->metaArr,
            "extra_tags"=>$this->tagsArr
        ];
    }

    /**
     * adds key&value met tag
     * @param $key
     * @param $value
     */
    private function add_meta($key, $value){
        $this->metaArr[$key]=$value;
    }

    /**
     * builds 2 lists of meta tags.
     * one list contains the base tags(title, description, keywords) as key&value pairs.
     * the other one contains HTML tags to inject.
     */
    private function handle_tags()
    {
        $meta_title = static::$project_title.' - '.stripslashes(($this->meta_tagsArr['title']==''?self::$default_page_title:$this->meta_tagsArr['title']));
        $descipriotn = htmlspecialchars($this->meta_tagsArr['description']);
        $keywrods = htmlspecialchars($this->meta_tagsArr['keywords']);

        $this->add_meta(self::META_TITLE, $meta_title);
        $this->add_meta(self::META_DESCRIPTION, $descipriotn);
        $this->add_meta(self::META_KEYWORDS, $keywrods);

        if(isset($this->meta_tagsArr["canonical"]) && $this->meta_tagsArr["canonical"]){
            $this->add_tag('<link rel="canonical" href="'. $this->meta_tagsArr['canonical'] .'" />');
        }
        $this->add_tag("<meta http-equiv='content-type' content='text/html; charset=utf-8' />");
        $this->add_tag("<meta http-equiv='imagetoolbar' content='yes' />");
        $this->add_tag("<meta name='robots' content='index, follow' />");
        if(isset($_REQUEST['page']) && ($_REQUEST['page']==1)){
            $uri = str_replace(array('?page=1','&page=1'),'',urldecode($_SERVER['REQUEST_URI']));
            $this->add_tag("<link rel='canonical' href='http://'" .$_SERVER['HTTP_HOST'].$uri ."' />");
        }
    }

    /**
     * add HTML tag.
     * @param $tag
     */
    private function add_tag($tag){
        $this->tagsArr[] = $tag;
    }

    /**
     * builds metatags from DB, if exists.
     * @return bool|mysqli_result
     */
    private function db_load(){
        $result = false;
        $module_to_load =isset(static::$staticModuleNameArr[$this->mdl_id]) ? 4 :$this->mdl_id;
        $query = "SELECT
					meta_title as title,
					meta_keywords as keywords,
					meta_description as description,
					canonical
				FROM tb_metatags
				WHERE  (
			    		(inner_id='{$this->object_id}') AND
			    		(module_id='{$module_to_load}') AND
			    		(`lang_id` ='{$_SESSION['lang_id']}')
                )";

        $result=$this->db->query($query);
        if($this->db->get_num_rows($result)){
            $this->set_meta_tags($this->db->get_stream($result));
            $result = true;
        }
        return $result;
    }

    /**
     * builds metatags from static file, if exists.
     * @return bool
     */
    private function static_load()
    {
        $salat_module_id = array_key_exists(intval($this->mdl_id), self::$front_salat_modules_linkArr) ? self::$front_salat_modules_linkArr[$this->mdl_id] : 0;
        $static_path = $_SERVER['DOCUMENT_ROOT'].'/_static/meta-tags/meta-'.$salat_module_id.'-'.$this->object_id.'.inc.php';
        try{
            if(!file_exists($static_path)){
                throw new Exception();
            }
            include_once($static_path);//$metaTagsArr
            $this->set_meta_tags($metaTagsArr);
            $result = true;
        }catch (Exception $Exception){
            $result = false;
        }

        return $result;
    }

    /**
     * setter
     * @param $metatagsArr
     *
     * @return $this
     */
    private function set_meta_tags($metatagsArr){
        $this->meta_tagsArr = $metatagsArr;
        return $this;
    }

}