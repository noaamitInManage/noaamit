
<div id="frm_wrapper">
   <form id="contact_frm" action="" method="post">
      <div id="close_btn">x</div>
      <div id="title_contact"><span>contact form</span></div> <br />
      <div id="ck_wrapper">
         <textarea name="contact_text" id="contact_text" cols="90" rows="10">
         
         </textarea>
      </div>
      <br />
      <input type="hidden" name="act" value="send" />
      <input type="hidden" name="email" value="<?=$email;?>" />
   <div id="buttons_holder"><input type="button" class="buttons" onclick="javascript:sendcontact(); return false;"  value="שלח" style="width:180px;border:1px solid black;"/></div>
   </form>
</div>
<script type="text/javascript">
					CKEDITOR.replace( 'contact_text',
                     	{
                     	   toolBar : 'Basic',
                     		language: 'en',
                     		width : '600px'

                     	});



</script>