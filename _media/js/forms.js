$(function(){
	$('.frm').submit(function(e){
		
		var valid = true;
		$('.err').removeClass('err');
		$('.is_err').removeClass('is_err');
		$('.formErrors').html('');
		
		//mandatory input text
		$.each($('.frm input[type=text].must, .frm textarea.must'), function(key,val){
			if(!$(val).val()){
				//$('.formErrors').html(lan['fill-mandatory']);
				$(val).addClass('err');
				$(val).closest('.err_wrap').addClass('is_err');
				valid = false;
			}
		});
		//mandatory combo select
		$.each($('.frm input[type=hidden].must'), function(key,val){
			if(!$(val).val()){
				//$('.formErrors').html(lan['fill-mandatory']);
				$(val).closest('.comboSelect').addClass('err');
				$(val).closest('.err_wrap').addClass('is_err');
				valid = false;
			}
		});
		//email
		if(valid && !email_validate($('#email').val())){
			//$('.formErrors').html(lan['invalid-email']);
			$('#email').addClass('err');
			$('#email').closest('.err_wrap').addClass('is_err');
			valid = false;
		}
		//agreement
		if(valid && !$('#agreementChk').is(':checked')){
			//$('.formErrors').html(lan['must-agree']);
			$('#agreementChk').addClass('err');
			$('#agreementChk').closest('.err_wrap').addClass('is_err');
			valid = false;
		}
		//submit if valid
		if(valid){
			e.preventDefault();
			$.post("/_ajax/ajax.index.php",$(".frm").serialize()+ '&file=form.service&act=submit',function(result){
				if(result.err!="") {
					var err_fields = '';
					if(typeof result.err_fields != 'undefined') {
						err_fields = result.err_fields.join();
					}
					$('.formErrors').html(result.err+' '+err_fields);
				} 
				else 
				{
					if(result.msg!="") {
						alerti({'msg':result.msg,'height':0,'reload':1});
					}
				}
			},"json");
		}
		else{
			e.preventDefault();
			return false;
		}
	});
	
	$('.submitBtn').click(function(){
		$(this).closest('.frm').submit();
	});
	
});