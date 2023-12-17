<div id="overlay" style="border:1px solid black;">	
	<h2 class="dottTbl"></h2>
	<div id="htmlbox_div" style="display:none">
		<textarea name="htmlbox" id="htmlbox"></textarea>
		<script type="text/javascript">
			window.objFCKeditor = new FCKeditor('htmlbox');
			<? if($_SESSION['miniEN']) { ?>
			objFCKeditor.Config["DefaultLanguage"] = "en";
			objFCKeditor.Config["ContentLangDirection"] = "ltr";
			<? } ?>
			window.objFCKeditor.ReplaceTextarea();
		</script>
	</div>
	<div id="htmlbox_basic_div" style="display:none">
		<textarea name="htmlbox_basic" id="htmlbox_basic"></textarea>
		<script type="text/javascript">
			window.objFCKeditor = new FCKeditor('htmlbox_basic');
			window.objFCKeditor.ToolbarSet = "Basic";
			<? if($_SESSION['miniEN']) { ?>
			objFCKeditor.Config["DefaultLanguage"] = "en";
			objFCKeditor.Config["ContentLangDirection"] = "ltr";
			<? } ?>
			window.objFCKeditor.ReplaceTextarea();
		</script>
	</div>
	<button id="htmlbox-save" class="buttons">סגור ושמור</button>&nbsp;&nbsp;
	<button id="htmlbox-close" class="buttons">סגור</button>
</div>
<script type="text/javascript">
(function($) {
	$(function() {
		var currInp = null;
		var editor = null;
		$('.htmlbox').click(function() {
		   
			if(currInp !== null) {
				if(confirm('האם אתה מעוניין לשמור שינויים אלו?')) {
					$('#htmlbox-save').trigger('click');
				}
			}
			
			if($(this).hasClass("basic")) {
				editor = "htmlbox_basic";
				$("#htmlbox_div").hide();
				$("#htmlbox_basic_div").show();
			} else {
				editor = "htmlbox";
				$("#htmlbox_div").show();
				$("#htmlbox_basic_div").hide();
			}
			
		//	$("html, body").animate({scrollTop: 25}, "slow", function() { });
			
			currInp = $(this).prev();
			var obj = FCKeditorAPI.GetInstance(editor),
				val  = currInp.val();
			$('#overlay > h2').text(currInp.closest('td').prev().find('b').text());
			obj.SetHTML(val);
			//$('#overlay').css({"left":($(window).width()-$('#overlay').width())/2,"top":($(window).height()-$('#overlay').height())/2}).show();
			$('#overlay').css({"left":($(window).width()-$('#overlay').width())/2,"top":($(window).height()-$('#overlay').height())/2}).show();
			$('#overlay').css({"position":"fixed"}).show();
			
			<? if($_SERVER['REMOTE_ADDR'] == '62.219.212.139') { // edit line 52?>
			// old methid
		//	$('#overlay').css({"left":($(window).width()-$('#overlay').width())/2,"top":($(window).height()-$('#overlay').height())/2}).show();
			<? } ?>
			
			<? if ( strpos(strtolower($_SERVER['HTTP_USER_AGENT']),strtolower('MSIE')) > 0){ ?>
      			$('#overlay').css({"left":($(window).width()-$('#overlay').width())/2,"top":($(window).height()-$('#overlay').height())/2}).show();
      			$('#overlay').css({"position":"absolute"}).show();
      			$("html, body").animate({scrollTop: 25}, "slow", function() { });
			<? } ?>
		});
		
		$('#htmlbox-save').click(function(event) {
			var obj = FCKeditorAPI.GetInstance(editor);
			currInp.val(obj.GetHTML());
			currInp = null;
			editor = null;
			$('#overlay').hide();
			event.preventDefault();
		});
		$('#htmlbox-close').click(function(event) {
			currInp = null;
			editor = null;
			$('#overlay').hide();
			event.preventDefault();
		});
		$(window).resize(function(){
			if(currInp !== null) {
				$('#overlay').css({"left":($(window).width()-$('#overlay').width())/2,"top":($(window).height()-$('#overlay').height())/2});
			}
		});
	});
}(jQuery));
</script>