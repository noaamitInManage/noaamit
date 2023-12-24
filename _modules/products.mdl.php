<?
include_once($_SERVER['DOCUMENT_ROOT'].'/_modules/_inc/header.php');
$product = new productsManager($objID);

?>

<link rel="stylesheet" href="<?php echo '/_media';?>/css/modules/products.css">
<div class="container"

    <ul>
        <?php foreach($product as $key=>$data):?>
            <li>  <?php echo $key . '=>' .$data;?></li>
        <?php endforeach;?>

    </ul>

<? include_once($_SERVER['DOCUMENT_ROOT'].'/_modules/_inc/footer.php'); ?>