<?php
 for( $iji=0; $iji<420; $iji++) {
    rename($_SERVER['DOCUMENT_ROOT']."/salat2/_public/icons/icon_ ($iji).png",$_SERVER['DOCUMENT_ROOT']."/salat2/_public/icons/icon_$iji.png");
     echo ($_SERVER['DOCUMENT_ROOT']."/salat2/_public/icons/icon_ ($iji).png");
   //  file_get_contents($_SERVER['DOCUMENT_ROOT']."/salat2/_public/icons/icon_('.$iji.').png");
     echo (file_get_contents($_SERVER['DOCUMENT_ROOT']."/salat2/_public/icons/icon_ ($iji).png"));
}
?>