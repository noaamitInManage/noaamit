<?php
//------------------------------------------
//Constants 
//------------------------------------------

//bvd constants -----------------------------
define("HOME_PAGE_PRIORITY","1.0");
define("BUSINESSES_INDEX_PRIORITY", "0.9");
define("BUSINESSES_PRIORITY", "0.9");
define("STATIC_PAGES_PRIORITY", "0.85");
define("CATEGORIES_PRIORITY", "0.8");
define("ARTICLES_PRIORITY", "0.75");
define("FORUMS_PRIORITY", "0.7");
define("FORUM_PAGES_PRIORITY", "0.69");
define("PRODUCT_CATEGORIES_PRIORITY", "0.65");
define("PRODUCTS_PRIORITY", "0.6");
define("BUSINESSES_ON_PAGE",8);
define("MAIN_MESSEGES_ON_FORUM_PAGE",12);
define("URL","http://".$_SERVER['HTTP_HOST']);
//------------------------------------------

//static pages -----------------------------
$static_pages=array();
//home page
//$static_pages[]=array('loc'=>URL,'lastmod'=>'','changefreq'=>'daily','priority'=>HOME_PAGE_PRIORITY);

// main modules

//$static_pages[]=array('loc'=>URL."contact" ,'lastmod'=>'','changefreq'=>'daily','priority'=>0.8);


?>