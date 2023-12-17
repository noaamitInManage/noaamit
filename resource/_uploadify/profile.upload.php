<?
include_once($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/dblayer.inc.php');

/**
 * upload post media to _media/temp
 */		
$answer=array("err"=>"");
error_reporting(E_ALL);
ini_set('display_errors', '0');
$image_types_arr=array('jpg','png','gif','jpeg','bmp');
$image_typesDot_arr=array('.jpg','.png','.gif','.jepg','.bmp','.jpeg');
$items_dir='/_media/user_temp/'.$_REQUEST['hash'].'';

if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	$tmp_name= time().'_'.$_FILES['Filedata']['name'];
	$targetFile =  str_replace('//','/',$targetPath) .$tmp_name;

	 $fileParts  = pathinfo($_FILES['Filedata']['name']);


	 if (in_array(strtolower($fileParts['extension']),$image_types_arr)) {

		$targetFile=str_replace($image_typesDot_arr,'.jpg',$targetFile);
		move_uploaded_file($tempFile,$targetFile);


		
//watermark size 69x36
		//&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&
		$image_new_width=50;
		$image_new_height=50;
		$offsetX=$offsetY=4;

		$image = new Imagick($targetFile);
		$image->ThumbnailImage($image_new_width,$image_new_height,true);
		$image->setImageFormat("jpg");				
		$answer['src']=str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);	
		$targetFileTempArr=explode('.',$targetFile);
		array_pop($targetFileTempArr);
	//	$targetFileTempArr[]='jpg';
		$targetFile=implode('',$targetFileTempArr).'.jpg';		
		$answer['tmp_name']=str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
		$image->writeImage($targetFile);

	 } else {
	 	$answer['err']= 'סיומת קובץ לא מאושרת';
	 }
	 echo json_encode($answer);
	 exit();
}
 echo json_encode($answer);
	 exit();
?>