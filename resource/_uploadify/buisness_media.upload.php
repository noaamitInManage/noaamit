<?
include_once($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/dblayer.inc.php');

/**
 * upload post media to _media/temp
 */		
$answer=array("err"=>"");
error_reporting(E_ALL);
ini_set('display_errors', '0');
$image_types_arr=array('jpg','png','gif','jepg','bmp');
$image_typesDot_arr=array('.jpg','.png','.gif','.jepg','.bmp');
$items_dir='/_media/buisness_media_temp/'.$_REQUEST['hash'].'/';
$totalImages=0;
$oldItems=scandir($_SERVER['DOCUMENT_ROOT'].$items_dir);
foreach ($oldItems AS $key=>$value){
	if(in_array(end(explode('.',$value)),$image_types_arr)){
		$totalImages++;
	}
}
$allowed_images_num=4;
$allowed_images_num *=2;// we have thumb for every pic;
if($totalImages>=$allowed_images_num){
	$answer['err']="
	לא ניתן להעלות תמונה 
	\n
	הגעת למספר המקסימלי של תמונות";
	echo json_encode($answer);
	exit();
}
if (!empty($_FILES)) {
	$tempFile = $_FILES['Filedata']['tmp_name'];
	$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
	$targetFile =  str_replace('//','/',$targetPath) . time().'_'.$_FILES['Filedata']['name'];
	// $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
	// $fileTypes  = str_replace(';','|',$fileTypes);
	// $typesArray = split('\|',$fileTypes);
	 $fileParts  = pathinfo($_FILES['Filedata']['name']);
	// mail('gal@inmanage.co.il','subject',print_r(array($fileParts),true),'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");

	 if (in_array(strtolower($fileParts['extension']),$image_types_arr)) {
		// Uncomment the following line if you want to make the directory if it doesn't exist
		// mkdir(str_replace('//','/',$targetPath), 0755, true);
		if(!file_exists($items_dir)){
			mkdir($targetPath,0755);
			chmod($targetPath,0755);
		}
		$targetFile=str_replace($image_typesDot_arr,'.jpg',$targetFile);
		move_uploaded_file($tempFile,$targetFile);
		
		
//watermark size 69x36
		//&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&
		$image_new_width=368;
		$image_new_height=252;
		$offsetX=$offsetY=4;
		$water_mark_link=$_SERVER['DOCUMENT_ROOT'].'/_media/images/small-avatar.png';
		list($water_mark_width, $water_mark_height) = getimagesize($water_mark_link);		
		list($w, $h) = getimagesize($targetFile);	
		
				
		$image = new Imagick($targetFile);
		//$image->ThumbnailImage($image_new_width,$image_new_height,true);
		if($h > $w){
			$image->ThumbnailImage($image_new_height,$image_new_width,true);
		}else{
			$image->cropThumbnailImage($image_new_width,$image_new_height);
		}		
		//$image->cropThumbnailImage($image_new_width,$image_new_height);
		$image->setImageFormat("jpg");		

		$image->writeImage($targetFile);
		list($real_image_width, $real_image_height) = getimagesize($targetFile);					
		$watermark = new Imagick($water_mark_link);

		list($x,$y) = array( ($real_image_width- ($water_mark_width ) - $offsetX), (($real_image_height-$water_mark_height) - $offsetY));
		$image->compositeImage($watermark,$watermark->getImageCompose(), $x,$y);
		
		//&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&
		$image->writeImage($targetFile);
		$thumb = new Imagick($targetFile);
		//$thumb->ThumbnailImage(93,62,true);
		$thumb->cropThumbnailImage(93,62);
		$targetFile=str_replace('.jpg','_thumb.jpg',$targetFile);
		$thumb->writeImage($targetFile);		
	    $answer['src']= str_replace($_SERVER['DOCUMENT_ROOT'],'',$targetFile);
	    
		
		//&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&^&
	 } else {
	 	$answer['err']= 'סיומת קובץ לא מאושרת';
	 }
	 $answer['count']=count($oldItems)-2;
	 echo json_encode($answer);
}

?>