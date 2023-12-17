<div id="overlay">	
	<h2 class="dottTbl"></h2>
	
	
	
	
	
	<button id="htmlbox-save" class="buttons">סגור ושמור</button>&nbsp;&nbsp;
	<button id="htmlbox-close" class="buttons">סגור</button>
</div>
<script type="text/javascript">

function addCkbox(itemID){
   var editor, html = '';
   if ( editor )
		return;
	// Create a new editor inside the <div id="editor">, setting its value to html
	var config = {};
	editor = CKEDITOR.appendTo( itemID, config, html );
	CKEDITOR.replace( itemID );
}
</script>