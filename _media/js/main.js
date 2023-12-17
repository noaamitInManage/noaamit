/* Message Layer */
	
//params={msg:html,[width:400 default],[height: 400 default],[reload: 1]}
//if height or width = 0, whould use auto width/height
//
function alerti(params){
	def = {'width':400,'height':200};
	if(typeof params == 'undefined') {
		return;
	}
	//height..
	if(typeof params.height != 'undefined'){
		if(params.height==0){
			$('.layerContWrap').css('height','auto');
		}
		else{
			$('.layerContWrap').css('height',params.height);
		}
	}
	else{
		$('.layerContWrap').css('height',def.height);
	}
	//width..
	if(typeof params.width != 'undefined'){
		if(params.width==0){
			$('.layerContWrap').css('width','auto');
		}
		else{
			$('.layerContWrap').css('width',params.width);
		}
	}
	else{
		$('.layerContWrap').css('width',def.width);
	}
	//if reload=1 adding a reload class to X button to reload page on close
	if(typeof params['reload'] != 'undefined'){
		if(params['reload']==1){
			$('.layers .closeLayer').addClass('reload');
		}
	}
	//content
	if(typeof params.msg != 'undefined'){
		$('#layerCont').html(params.msg);
		$('#screener').css('display','block');
		//positioning
		var absVerFix = parseInt($('.layers').height() / 2);
		$('.layers').css('margin-top', -absVerFix);
		var absHorFix = parseInt($('.layers').width() / 2);
		$('.layers').css('margin-left', -absHorFix);
	}
}

/* /Message Layer */

$(function(){

	$('.numOnly').keypress(function(event) {
		if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 ) return;
	    if(event.which < 48 || event.which > 57) {
			event.preventDefault();
		} // prevent if not number/dot
	});
	

	/* tabs */
	//onload display active tab content
	$.each($('.tabs'),function(key,val){
		$(val).closest('.tabsWrap').find('.tabContent').html($(val).find('li.active > div').html());
	});
	
	//switch tab on click
	$('.tabs>li').live('click',function(){
		$(this).closest('.tabs').find('.active').removeClass('active');
		$(this).addClass('active');
		$(this).closest('.tabsWrap').find('.tabContent').html($(this).find('div').first().html());
	});
	/* /tabs */
	
	/* Message Layer */
	//close layer
	$('.closeLayer').click(function(){
		$('#screener').css('display','none');
		if($('.layers .closeLayer').hasClass('reload')){
			$('.layers .closeLayer').removeClass('reload');
			window.location.reload()
		}
	});
	$('#screener').click(function(){
		$('#screener').css('display','none');
		if($('.layers .closeLayer').hasClass('reload')){
			$('.layers .closeLayer').removeClass('reload');
			window.location.reload()
		}
	});
	$('.layers').click(function(event){
		event.stopPropagation();
	});
	// /close layer
	
	/* /Message Layer */
	
	$('#pageNavigation li').mouseenter(function(){
		var linklClass = $(this).attr('class');
		$(this).find('a').addClass(linklClass).css('font-weight', 'bold');
	});
	$('#pageNavigation li').mouseout(function(){
		$(this).find('a').removeClass().css('font-weight', 'normal');;
	});

	$('.autoclear').autoclear();
	
	function showThankYou(){
		var msg = '<div class="ty-wrap">';
		msg = msg + '<p class="title colorSchema_8">'+lan['ty-title']+'</p>';
		msg = msg + '<hr />';
		msg = msg + '<p>'+lan['ty-we-contact']+'</p>';
		msg = msg + '<hr />';
		msg = msg + '<p>'+lan['ty-check-email']+' <a href="mailto:'+lan['contact-email']+'">'+lan['contact-us']+'</a></p>';
		msg = msg + '</div>';
		alerti({'msg':msg,'width':400,'height':310});
	}

}); // End Document ready