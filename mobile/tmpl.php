<?
include($_SERVER['DOCUMENT_ROOT'].'/mobile/restrict.php'); 
include($_SERVER['DOCUMENT_ROOT'].'/_static/home_page_categories.inc.php');//$homePageCatArr 

?>
<!DOCTYPE html> 
<html> 
	<head> 
	<title>CityWall - הלוח החברתי הראשון בישראל</title> 
	<meta name="viewport" content="width=device-width, initial-scale=1"> 
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />	
	<link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.1/jquery.mobile-1.1.1.min.css" />
	<script src="http://code.jquery.com/jquery-1.7.1.min.js"></script>
	<script src="http://code.jquery.com/mobile/1.1.1/jquery.mobile-1.1.1.min.js"></script>
</head> 
<body> 

<div data-role="page">

	<div data-role="header">
		<h1 style="">CityWall</h1> <img style="position:absolute;top:5px;right:10px; " src="http://www.citywall.co.il/_media/images/city_wall2.png" width="50" />
			
	</div><!-- /header -->

	<div data-role="content">	
		<a href="" data-role="button" data-icon="arrow-l" data-theme="e" onclick="alert('בקרוב');">פרסם עכשיו</a>	
		<a href="" data-role="button" data-icon="arrow-l" data-theme="c" onclick="alert('בקרוב');">מחפש? ספר לנו</a>	
	<? foreach ($homePageCatArr AS $key=>$value){if(in_array($key,array(19,20))){continue;}?>
		<a href="category.php?id=<?=$key;?>" data-role="button" data-icon="arrow-l" data-theme="b"><?=$value['title'];?></a>
	<? } ?>
	
	</div><!-- /content -->
	<div data-role="footer" style="text-align:center;padding:5px;">
		<img src="http://www.citywall.co.il/_media/images/inmanageLogo.png" /> 
	</div><!-- /footer -->
</div><!-- /page -->

</body>
</html>