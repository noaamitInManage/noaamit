<?php
$MetaTagsManager = new MetaTagsManager($mdlID, $objID);
$tagsArr = $MetaTagsManager->resolve();

//$r = '?r='.time();
$r = '?r='.date('dmy');

?>
    <title><?=$tagsArr["meta_tags"][MetaTagsManager::META_TITLE];?></title>
    <meta name="description" content="<?=$tagsArr["meta_tags"][MetaTagsManager::META_DESCRIPTION];?>" />
    <meta name="keywords" content="<?=$tagsArr["meta_tags"][MetaTagsManager::META_KEYWORDS]?>" />
<?foreach ($tagsArr["extra_tags"] as $tag){?>
    <?=$tag?>
<?}?>

    <link rel="shortcut icon" href="/_media/images/favicon.ico">
<?/*<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>*/?>
    <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script type="text/javascript" src="/_static/js/basic.static.js<?=$r;?>"></script>
<? if(count($modulesJSArr[$mdlName])>0){ ?>
    <script type="text/javascript" src="/_static/js/<?=$mdlName;?>.static.js<?=$r;?>"></script>
<? } ?>
    <link rel="stylesheet" media="screen,print" href="/_static/css/basic.static.css<?=$r;?>" type="text/css" />
<? if(count($modulesCSSArr[$mdlName])>0){?>
    <link rel="stylesheet" media="screen,print" href="/_static/css/<?=$mdlName;?>.static.css<?=$r;?>" type="text/css" />
<? } ?>
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
    <!--[if IE]>
    <link rel="stylesheet" href="/_media/css/ie.css" type="text/css" />
    <![endif]-->
    <!--[if IE 7]>
    <link rel="stylesheet" href="/_media/css/ie7.css" type="text/css" />
    <![endif]-->
    <!--[if IE 8]>
    <link rel="stylesheet" href="/_media/css/ie8.css" type="text/css" />
    <![endif]-->
    <!--[if IE 9]>
    <link rel="stylesheet" href="/_media/css/ie9.css" type="text/css" />
    <![endif]-->
<? include($_SERVER['DOCUMENT_ROOT'].'/_inc/google_analytics.inc.php');?>