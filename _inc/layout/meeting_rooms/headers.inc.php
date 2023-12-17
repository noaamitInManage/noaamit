<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/_static/front_salat_modules_link.inc.php'); // $front_salat_modules_linkArr
$salat_module_id = array_key_exists(intval($mdlID), $front_salat_modules_linkArr) ? $front_salat_modules_linkArr[$mdlID] : 0;

if(!isset($metaTag)) {
    $metaTag = array(
        'title'=>'',
        'keywords'=>'',
        'description'=>'',
    );
}

$module_to_load =isset($staticModuleNameArr[$mdlID]) ? 4 :$mdlID;
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/_static/meta-tags/meta-'.$salat_module_id.'-'.$objID.'.inc.php')){
    include_once($_SERVER['DOCUMENT_ROOT'].'/_static/meta-tags/meta-'.$salat_module_id.'-'.$objID.'.inc.php');//$metaTagsArr
    $metaTags=$metaTagsArr;
}
include($_SERVER['DOCUMENT_ROOT'].'/_static/meta-tags/noindex.inc.php');//metaTagsNoIndexArr
if(!is_array($metaTags) || empty($metaTags)){
    $query = "SELECT
					meta_title as title,
					meta_keywords as keywords,
					meta_description as description,
					canonical
				FROM tb_metatags
				WHERE  (
			    		(inner_id='{$objID}') AND
			    		(module_id='{$mdlID}') AND
			    		(`lang_id` ='{$_SESSION['lang_id']}')

				    )";
    $result = $Db->query($query) ;
    if(($result->num_rows)){
        $metaTags = $Db->get_stream($result);
    }else{
        $metaTags = array();
    }
}

if(!isset($page_title)) {
    $page_title = '';
}
$metaTitle = $_project_title_main.' - '.stripslashes(($metaTags['title']==''?$page_title:$metaTags['title']));

if ($page_keywords!=''){
    if(!isset($metaTags['keywords'])){$metaTags['description']='';}
    $metaTags['keywords'] = $page_keywords;
}
if ($page_description!=''){
    if(!isset($metaTags['description'])){$metaTags['description']='';}
    $metaTags['description'] = $page_description;
}
if ($metaTags['canonical']) {
    $canonical_tag = '<link rel="canonical" href="'. $metaTags['canonical'] .'" />';
}

//$r = '?r='.time();
$r = '?r='.date('dmy');

?>
    <title><?=$metaTitle;?></title>
    <meta name="description" content="<?=htmlspecialchars($metaTags['description']);?>" />
    <meta name="keywords" content="<?=htmlspecialchars($metaTags['keywords']);?>" />
<?= $canonical_tag ?>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta http-equiv="imagetoolbar" content="yes" />
<?
if(isset($metaTagsCanonicalArr) && array_search($mdlID.'-'.$objID,$metaTagsCanonicalArr)) { ?>
    <meta name="robots" content="noindex" />
<? }else{ ?>
    <meta name="robots" content="index, follow" />
<? } ?>
    <link rel="shortcut icon" href="/_media/images/favicon.ico" />
<? if(count($modulesJSArr[$mdlName])>0){ ?>
    <script type="text/javascript" src="/_static/js/<?=$mdlName;?>.static.js<?=$r;?>"></script>
<? } ?>
<? if(count($modulesCSSArr[$mdlName])>0){?>
    <link rel="stylesheet" media="screen,print" href="/_static/css/<?=$mdlName;?>.static.css<?=$r;?>" type="text/css" />
<? } ?>
    <link rel="stylesheet" media="screen,print" href="/_media/css/modules/meeting_rooms.css<?=$r;?>" type="text/css" />
    <script type="text/javascript">
        /*<[CDATA[*/
        var mdlName='<?=$mdlName;?>';
        var mdlID='<?=$mdlID;?>';
        var objID='<?=$objID;?>';
        var debug=false;
        <?if(in_array($_SERVER['REMOTE_ADDR'],configManager::$familiar_ipsArr)) { ?>
        debug=true;
        <?}?>
        /*]]>*/
    </script>
<? include($_SERVER['DOCUMENT_ROOT'].'/_inc/google_analytics.inc.php');?>