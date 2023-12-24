<?
include_once($_SERVER['DOCUMENT_ROOT'].'/_modules/_inc/header.php');
$stores = storesManager::getAll();
?>

        <div class="container">
             <h1>Welcome!</h1>
            <?php if(count($stores) > 0):?>
                <div class="row">

                    <div class="col-md-6">
                        <h3>Stores:</h3>
                    </div>

                </div>
                <?php foreach($stores as $key=>$store):?>

                    <? $url = $Seo->getUrl(array("mdl_id"=>$modulesArr['stores']), $key); ?>
                    <div class="card card-body mb-3">
                        <h4 class="card-title"><?php echo $store['title'];?></h4>
                        <a href="/<?php echo $url;?>"> <?php echo 'More';?></a>
                    </div>
                <?php endforeach; ?>
            <?php endif;?>


<? include_once($_SERVER['DOCUMENT_ROOT'].'/_modules/_inc/footer.php'); ?>
