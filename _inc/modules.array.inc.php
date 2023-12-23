<?php
/**
 * @name: 	modules.array.inc.php
 * @author: Albert Harounian 
 * @since:	January 14 2009
 * @desc:	Statics arrays used for site and SALAT
 */


/**
 * @desc SITE static Arrays
 */

$daysArr = array(
	'1' => 'Sun',
	'2' => 'Mon',
	'3' => 'Tue',
	'4' => 'Wed',
	'5' => 'Thu',
	'6' => 'Fri',
	'7' => 'Sat',
);

$monthArr = array(
    1 => 'January',
    2 => 'February',
    3 => 'March',
    4 => 'Aptil',
    5 => 'May',
    6 => 'June',
    7 => 'July',
    8 => 'August',
    9 => 'September',
    10 => 'October',
    11 => 'November',
    12 => 'December'
);

$_weird_arr = array('~','!','@','#','$','%','^','[','{','}',']',';','<','>','/','?');


// NIS unicode symbol
define('NIS', '&#8362;');

/**
 * ***************************
 * 		CACHING SYSTEM
 * ***************************
 */
 

$fbConf = array( 'appId'=>'189377484465976', 'secret'=>'8878a176dadc9171da71e48e435158b6' );

/**
 * @desc CSS & JS caching function
 */
$caching_isOn = false;
$caching_isOn = true;

/**
 * An array of all the modules in the project
 */
$modulesArr = array(
	'home'=>0,
	'content'=>1,	
	'404'=>404,
	'products'=>40,
	'categories'=>50,
	'stores'=>60,

);

$menuBreadCrumbsArr = array(
);

$user_only_modules = array(
);

$bannersAreasArr = array(
);

/**
 * Array for basic CSS files
 */
$basicCSSArr = array(
	//"global",
	//"normalize",
	"layout",
	"plugins/jquery.autocomplete",
);

/**
 * @desc CSS files for each module
 *
 * "module name" => array(
 * 			"css file name"
 * ),
 */
$modulesCSSArr = array(
	'home'=> array(
		'modules/home',
	),
	'content'=> array(
		'modules/content',
	),
	'stores'=> array(
		'modules/stores',
	),

);



/**
 * Array for JS files that will not be compressed
 */
$xCompressedJSArr = array(
	'plugins/jquery.autocomplete',
);

/**
 * Array for basic JS files
 */
$basicJSArr = array(
	'plugins/jquery.autocomplete',
	"main",
	"banner",
	"function",
);


/**
 * @desc JS files for each module
 *
 * "module name" => array(
 * 			"js file name"
 * ),
 */


$modulesJSArr = array(
	'home' => array(
//		'plugins/jquery.cycle.lite',
//		'plugins/jquery.easing.1.3',
//		'modules/home',
	),
	
	'content' => array(


	),
	'stores' => array(


	),
	
);

$_NoYes_FARR=array(
    0=>array(
        "id"=>0,
        "title"=>'לא',
    ),
    1=>array(
        "id"=>1,
        "title"=>'כן',
    )
);

$frontDirectionArr = array(
	1 => "rtl",
	2 => "ltr",
);
/*******************************  mobile ****************************************/
 // mobile static only
$mobileOnlyModulesArr=array(

	'test'=>array(
		'module_id'=>1400,
		'is_static'=>1,
		'obj_id'=>0,
		'url'=>'test',
		'name'=>'test',
		'module_name'=>'test',
		'lang_id'=>1
		),	

);	

$not_allowed_mobile_moduleArr=array(
	'404',
);

/*******************************end mobile ****************************************/
//load language array
include_once($_SERVER['DOCUMENT_ROOT']."/_static/translations.".(!empty($_SESSION['lang']) ? $_SESSION['lang'] : default_lang).".inc.php");//$translationsArr

?>
