<?
include_once($_SERVER['DOCUMENT_ROOT'] . "/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");

include_once($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/functions.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/site.array.inc.php');
set_time_limit(0);
ini_set('MEMORY_LIMIT','256MB');
$Db = Database::getInstance();
/*
 *  [Filename] => logo.png
    [folder] => /_media/temp/
    [fileext] => *.jpg; *.png; *.bmp;*.jpeg;*.gif;*.mov;*.avi;*.mp4;*.wmv;*.mpg
    [item_id] => 2  //id of the item or event or lead or benefit
    [Upload] => Submit Query
	[type] => 3    // 1.item,2.event.,3.benefit
	[num] => a (or b or other symbol or num..)  // useful for many galleries in the item
 * */

$image_types_arr=array('jpg','png','gif','jpeg','bmp');
$image_typesDot_arr=array('.jpg','.png','.gif','.jpeg','.bmp');
$item_id=isset($_REQUEST['item_id'])? $_REQUEST['item_id'] : '';
$gallery_num=isset($_REQUEST['num'])? $_REQUEST['num'] : ''; //for more than one gallery in the item
$ext=explode('.',$_REQUEST['Filename']);
$img_ext=array_pop($ext);
$file_name = str_replace('.'.$img_ext,'',$_REQUEST['Filename']);
$img_ext=strtolower($img_ext);
if(in_array($img_ext,$image_types_arr) && $item_id ){
	//make new gallery
	$table_name=$_REQUEST['table'];
	$type_name=$_REQUEST['name'];
	$query="SELECT title FROM `{$table_name}` WHERE `obj_id`='{$item_id}'";
	$res=$Db->query($query);
	$title_line=$Db->get_stream($res);
	$gallery_name=($gallery_num)?$title_line['title'].'_'.$item_id.'_'.$type_name.'_'.$gallery_num:$title_line['title'].'_'.$item_id.'_'.$type_name;
	//check if this gallery exists
	$query="SELECT id FROM `tb_media_category` WHERE `title`='{$gallery_name}'";
	$res=$Db->query($query) or die($query);
	if(($res->num_rows)>0){
		$gallery_line=$Db->get_stream($res);
		$gallery_id=$gallery_line['id'];
	}else{
		$db_fields=array(
			"title"=>$gallery_name
		);
		foreach($db_fields AS $key=>$value){
			$db_fields[$key]=$Db->make_escape($value);
		}
		$query = "INSERT INTO `tb_media_category` (`".implode("`,`",array_keys($db_fields))."`) VALUES ('".implode("','",array_values($db_fields))."')";
		$res = $Db->query($query);
		$gallery_id=$Db->get_insert_id();
		$UpdateStatic = new mediaCategoryParagraphUpdateStaticFiles();
		$_REQUEST['inner_id']=$gallery_id;
		$UpdateStatic->updateStatics();
	}

	//save the picture inside new gallery
	if ( (!empty($_FILES)) && $gallery_id>0) {
		$tempFile = $_FILES['Filedata']['tmp_name'];
		$targetPath = $_SERVER['DOCUMENT_ROOT'].'/_media/media/'.$gallery_id.'/';
		$targetFile= $targetPath.$_FILES['Filedata']['name'];
		if(!is_dir($targetPath)){
			mkdir($targetPath,0777);
			chmod($targetPath,0777);
		}
		move_uploaded_file($tempFile,$targetFile);
		list($w, $h) = getimagesize($targetFile);	// image validate
		// on multi upload give picture auto title & alt
		$pic_name=$file_name;
		$db_fields=array(
			"title"=>$pic_name,
			"alt"=>$pic_name,
			"category_id"=>$gallery_id,
			"img_ext"=>$img_ext,
			"last_update"=>time()
		);

		if($w){
			foreach($db_fields AS $key=>$value){
				$db_fields[$key]=$Db->make_escape($value);
			}

			$query = "INSERT INTO `tb_media` (`".implode("`,`",array_keys($db_fields))."`) VALUES ('".implode("','",array_values($db_fields))."')";
			$res = $Db->query($query) or die($query);
			$item_media_id=$Db->get_insert_id();
			$image = new Imagick($targetFile);
			$image->writeImage($targetPath.$item_media_id.'.'.$img_ext);
			$image->destroy();
			@unlink($targetFile);
			usleep(2);
			$UpdateStatic = new mediaUpdateStaticFiles();
			$_REQUEST['inner_id']=$item_media_id;
			$UpdateStatic->updateStatics();
			usleep(2);
			exit(json_encode(array('album_id'=>$gallery_id,"item_id"=>$item_media_id)));
		}
	}
}
exit(json_encode(array('err'=>$errors)));
?>