<?
include_once($_SERVER['DOCUMENT_ROOT'].'/_modules/_inc/header.php');
$store = new storesManager($objID);
?>

<link rel="stylesheet" href="<?php echo '/_media';?>/css/modules/stores.css">
</head>
    <body>
        <img id="site_logo" src=<?php echo $full_path;?> alt='Image'>
        <div class="container"
        <ul>
            <?php foreach($store as $key=>$data):?>

                <li>  <?php echo $key . '=>' .$data;?></li>
            <?php endforeach;?>
        </ul>
        </div>
    </body>
</html>
