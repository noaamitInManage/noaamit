<div data-role="page" id="<?=$mdlName;?>_page" >
<script type="text/javascript">
$("#<?=$mdlName;?>_page").bind( "pagecreate", function(event){

});
</script>

<? include($_SERVER['DOCUMENT_ROOT'].'/mobile/layout/top.inc.php'); ?>
	<div data-role="content" id="<?=$mdlName;?>_content">	
		<center>hello world!</center>
	</div>
<? include($_SERVER['DOCUMENT_ROOT'].'/mobile/layout/footer.inc.php'); ?>
</div>  