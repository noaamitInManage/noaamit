<?
$galleryId = $_REQUEST['gal_id'];
include($_SERVER['DOCUMENT_ROOT'].'/_static/mediaGroup/mediaGroup-'.$galleryId.'.inc.php');//$mediaGroupsArr
echo draw_genric_gallery_images($galleryId);
?>