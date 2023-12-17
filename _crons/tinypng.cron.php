<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 08/08/13
 * Time: 13:03
 * MCDONALDS  parse data
 *
 * this cron delete data from  /_static/data/parsed and save only 10 last recoreds
 *
 * @frqancy : EVERY 24 Hours at 3:00 pm ;
 */
/*$cp='qq978ARXjVyMdd';
if($_REQUEST['cp']!=$cp){die('...');}*/
if(!isset($_REQUEST["cp"]) || $_REQUEST["cp"]!="qq978ARXjVyMdd"){
	die("No permission!");
}
// do auto tinypng to images
ini_set('memory_limit','1024M');
set_time_limit(600);

include_once($_SERVER['DOCUMENT_ROOT']."/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path.$_includes_path."dblayer.inc.php"); // load database connection
include_once($_project_server_path.$_includes_path."modules.array.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."site.array.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."modules.functions.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."html.functions.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."mobile_html.functions.inc.php");  // load module functions
include_once($_project_server_path.$_salat_path.$_includes_path."functions.inc.php"); // load various functions
set_time_limit(600);
error_reporting(E_ALL);
ini_set('display_errors', '1');
include $_SERVER['DOCUMENT_ROOT'].'/resource/tinypng/vendor/autoload.php';

$Tinypng = tinypngManager::getInstance();
if($itemsArr=$Tinypng->get_images_to_export()){
	$Tinypng->compress_images($itemsArr);
}

/**
 * @author : gal zalait
 * @desc :
 * @var : 1.0
 * @last_update :
 */

class tinypngManager
{

	private static $instnace = null;
	private $ts = 0;
	private $key='';// tSP2pqtCQdhuUHbrpwxu0hF4YwOfBGpE - gal@inmanage.co.il dont use this account !!!
	public $monthly_limit=500;
	public $allowed_extArr=array(
		"jpg",
		"jpeg",
		"png"
	);

	/*----------------------------------------------------------------------------------*/

	function __construct()
	{
		$this->ts = time();
		\Tinify\setKey($this->key); // gal@inmanage.co.il

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

	public static function getInstance()
	{
		if (null === self::$instnace) {
			//	echo "new";
			self::$instnace = new tinypngManager();
		} else {
			//	echo "memory";
		}
		return self::$instnace;
	}

	/*----------------------------------------------------------------------------------*/
	/**
	 * get images that we didnt do tinypng on them
	 * pull only images under the monthly usage limit
	 */
	public function get_images_to_export(){
		$Db = Database::getInstance();
		$curr_limit = $this->get_monthly_usage();
		$limit = $this->monthly_limit - $curr_limit;
		$query="
			SELECT Main.*,MediaCat.`mobile` FROM `tb_media`  AS Main
				LEFT JOIN  `tb_media_category`  AS MediaCat ON (
					Main.`category_id`=MediaCat.`id`
					)
					WHERE Main.`tinypng_ts` < Main.`last_update`
					LIMIT {$limit}
				";
		$result=$Db->query($query);
		if($result->num_rows) {
			$itemsArr = array();
			while ($row = $Db->get_stream($result)) {
				$itemsArr[$row['id']] = $row;
			}
			return $itemsArr;
		}
		return false;
	}

	/*----------------------------------------------------------------------------------*/

	public function get_monthly_usage(){
		try {
			$compressionsThisMonth = \Tinify\compressionCount();
		}
		catch (Exception $E){
			echo '<span style="color:red;font-weight:bold;"> Caught exception: ',  $E->getMessage(), "</span><br>";
		}

	}

	/*----------------------------------------------------------------------------------*/
	/**
	 * @param $itemsArr [ID][ id 	category_id 	title 	alt 	credit 	description 	img_ext 	width 	height 	tinypng_ts 	last_update 	mobile]
	 *
	 */
	public function compress_images($itemsArr){
		$counter=1;
		foreach ($itemsArr AS $imageArr){
			$pathArr=$this->get_images_paths($imageArr,$counter++);
			$this->update_image($imageArr['id']);

		}
	}

	/*----------------------------------------------------------------------------------*/
	public function update_image ($media_id){
		$Db =Database::getInstance();
		$query="UPDATE `tb_media` SET `tinypng_ts`={$this->ts} 
					WHERE `id`={$media_id}";
		$Db->query($query);
	}

	/*----------------------------------------------------------------------------------*/
	/**
	 * @param $imageArr - [ id 	category_id 	title 	alt 	credit 	description 	img_ext 	width 	height 	tinypng_ts 	last_update 	mobile]
	 */
	public function get_images_paths($imageArr,$counter=1){
		$pathArr=array();

		$pathArr['web_site']= $_SERVER['DOCUMENT_ROOT']."/_media/media/{$imageArr['category_id']}/{$imageArr['id']}.{$imageArr['img_ext']}";
		if($imageArr['mobile']==1){
			$pathArr['iphone']= $_SERVER['DOCUMENT_ROOT']."/_media/media/{$imageArr['category_id']}/{$imageArr['id']}_iphone.{$imageArr['img_ext']}";
			$pathArr['android']= $_SERVER['DOCUMENT_ROOT']."/_media/media/{$imageArr['category_id']}/{$imageArr['id']}_android.{$imageArr['img_ext']}";
		}
		foreach ($pathArr as $platform=>$path){
			if((!file_exists($path)) || (!in_array($imageArr['img_ext'],$this->allowed_extArr)) ){
				continue;
			}

			try{
				$source = \Tinify\fromFile($path);
				$source->toFile($path);
			}
			catch (Exception $e) {
				echo '<span style="color:red;font-weight:bold;"> Caught exception: ',  $e->getMessage(), "</span><br>";
			}

			usleep(rand(300,600));
			echo "<span style=\"color:green;font-weight:bold;\">{$counter}.{$path} </span><br>";

		}
	}
	/*----------------------------------------------------------------------------------*/
}
?>