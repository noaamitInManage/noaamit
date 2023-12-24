<?
include_once($_SERVER['DOCUMENT_ROOT'].'/_modules/_inc/header.php');
$product = new productsManager($objID);
$product_icon = new mediaManager($product->media_id);
$icon_path =  $product_icon->path;
?>

    <div class="container">

    <div class="row">

        <div class="col-md-6">
            <h1><?php echo  $product->title;?></h1>
        </div>

    </div>

    <?php if ($icon_path != ''): ?>
        <img id="site_logo" src=<?php echo $icon_path;?> alt='Image'>
    <?php endif; ?>

    <!--products's data-->
    <div>
        <div>
            active :
            <?php if($product->active == 1):?>
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
            <?php echo  date('d.m.Y',$product->last_update);?>
        </div>

        <div>
            <?php echo  $product->description;?>
        </div>



    </div>

<? include_once($_SERVER['DOCUMENT_ROOT'].'/_modules/_inc/footer.php'); ?>