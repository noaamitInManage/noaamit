<?
include_once($_SERVER['DOCUMENT_ROOT'].'/_modules/_inc/header.php');
$store = new storesManager($objID);
$categories = storesManager::getStoreCategories($objID);

$store_properties = array(
    1 => 'koser',
    2 => 'shipping',
    4 => 'accessible',
);

?>

        <div class="container">

    <div class="row">

        <div class="col-md-6">
            <h1><?php echo  $store->title;?></h1>
        </div>

    </div>


    <!--store's data-->
    <div>
        <div>
            active :
        <?php if($store->active == 1):?>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
            </svg>
        <?php else:?>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
            </svg>
    <?php endif;?>
        </div>

        <div>
            open :
            <?php if($store->open == 1):?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                    <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                </svg>
            <?php else:?>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                    <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                </svg>
            <?php endif;?>
        </div>

        <div>
            last update :
            <?php echo  date('d.m.Y',$store->last_update);?>
        </div>

        <?php foreach ($store_properties as $key_prop=>$prop):?>
            <div>
                <?php echo  $prop . ' : ';?>
                <?php if(is_bitflag_set($store->bitwise_array, $key_prop) ):?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                    </svg>
                <?php else:?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x-lg" viewBox="0 0 16 16">
                        <path d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z"/>
                    </svg>
                <?php endif;?>
            </div>
        <?php endforeach?>

    </div>
        <!--store's categories-->
        <?php if(count($categories) > 0):?>
            <div class="row">

                <div class="col-md-6">
                    <h3>Categories:</h3>
                </div>

            </div>
            <?php foreach($categories as $category):?>
                <?
                $url = $Seo->getUrl(array("mdl_id"=>$modulesArr['categories']), $category['id']);
                $category_icon = new mediaManager($category['media_id']);
                $icon_path =  $category_icon->path;
                ?>
                <div class="card card-body mb-3">
                    <?php if ($icon_path != ''): ?>
                        <img id="site_logo" src=<?php echo $icon_path;?> alt='Image'>
                    <?php endif; ?>
                    <h4 class="card-title"><?php echo $category['title'];?></h4>
                    <a href="/<?php echo $url;?>"> <?php echo 'More';?></a>
                </div>
            <?php endforeach; ?>
        <?php endif;?>


<? include_once($_SERVER['DOCUMENT_ROOT'].'/_modules/_inc/footer.php'); ?>