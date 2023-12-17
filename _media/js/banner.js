//(function($) {
	$(document).on('click',".sliderImgBox",function(event){  //banner_click
		event.preventDefault();
		var target = $(this).children('a').attr('target');
		var href = $(this).children('a').attr('href');
		var id = parseInt($(this).attr("id").replace("banner_",""));

		$.ajax({
			url: "/_ajax/ajax.index.php?file=banner&id=" + id + "&act=add",
			async: false
		}).done(function(){
				if(href != 'undefind'){
					if(target != 'undefind' && target == '_blank'){
						window.open(href, '_blank');
					}
					else window.location = href;
				}
			});
	});

//}(jQuery));
