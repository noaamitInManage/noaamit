<?
include_once($_SERVER['DOCUMENT_ROOT'].'/_modules/_inc/header.php');
$stores = storesManager::getAll();
?>

        <link rel="stylesheet" href="<?php echo '/_media';?>/css/modules/home.css">
        <div class="container"
            <ul>
            <?php foreach($stores as $key=>$data):?>
                <?$url = $Seo->getUrl(array("mdl_id"=>$modulesArr['stores']), $key);?>
                <li> <a href="<?php echo $url;?>"> <?php echo $data['title'];?></a></li>
            <?php endforeach;?>
            </ul>

<? include_once($_SERVER['DOCUMENT_ROOT'].'/_modules/_inc/footer.php'); ?>
