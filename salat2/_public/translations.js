$(function(){

$(".saveTranslation").on('click',function(){
	var thisRow = $(this).closest('tr');
	var translation = {};
	translation.key_code = thisRow.data('keycode');
	translation.values = [];
	translation.save_in_gd = $(".gd_"+translation.key_code).is(':checked') ? 1 : 0;
	thisRow.find('textarea').each(function(index,elem){
		var langid = $(elem).data('langid');
		var value = encodeURIComponent($(elem).val());
		if (langid){
			translation.values.push({'langid':langid,'text':value});
		}
	});

	$.post('/salat2/_ajax/ajax.index.php',
		{'file':'translations','act':'save','data':translation},
		function(response){
			if (response.status == 'ok'){
//				alert("נשמר בהצלחה");
				iflameRefresh();
			}else{
//				alert('הפעולה נכשלה!');
			}
		},'json');
});

$(".deleteTranslation").on('click',function(){
	if (confirm("לא ניתן לבטל את הפעולה. נא לאשר מחיקה.")){
		var thisRow = $(this).closest('tr');
		var key_code = encodeURIComponent(thisRow.data('keycode'));
		$.post('/salat2/_ajax/ajax.index.php',
			{'file':'translations','act':'delete','key_code':key_code},
			function(response){
				if (response.status == 'ok'){
					alert("נמחק בהצלחה");
					thisRow.remove();
				}else{
					alert('הפעולה נכשלה!');
				}
			},'json');
	}
});

$("#saveNewTranslation").on('click',function(){
	var thisRow = $(this).closest('tr');
	var translation = {};
	translation.key_code = $("#newTranslationKey").val();
	if (translation.key_code != '') {
		translation.values = [];
		translation.save_in_gd = $("#newTranslation input:checkbox").is(':checked') ? 1 : 0;
		thisRow.find('textarea').each(function (index, elem) {
			var langid = $(elem).data('langid');
			var value = encodeURIComponent($(elem).val());
			if (langid) {
				translation.values.push({'langid': langid, 'text': value});
			}
		});

		$.post('/salat2/_ajax/ajax.index.php',
			{'file': 'translations', 'act': 'save-new', 'data': translation},
			function (response) {
				if (response.status == 'ok') {
//				alert("נשמר בהצלחה");
					iflameRefresh();
				} else {
//				alert(response.err);
				}
			}, 'json');
	}else{
//		alert('יש להגדיר מפתח באנגלית');
	}

});

$("#cancelNewTranslation").on('click',function(){
	$("#newTranslation textarea").val('');
});

});
