<?

$category_media_idArr=array(
	1=>8,
	3=>2,
	20=>6,
	18=>4,
	19=>1,
	21=>7,
	24=>5,
	22=>9,
	
); 

?>

<div data-role="page" id="<?=$mdlName;?>_page" data-theme="l">
<script type="text/javascript">
$("#<?=$mdlName;?>_page").bind( "pagecreate", function(event){

});
</script>

<? include($_SERVER['DOCUMENT_ROOT'].'/mobile/layout/top.inc.php'); ?>
	<div data-role="content" id="main_content_slide">	
		<center>hello world - mobile site !</center>
	</div>
<? include($_SERVER['DOCUMENT_ROOT'].'/mobile/layout/footer.inc.php'); ?>
</div>  