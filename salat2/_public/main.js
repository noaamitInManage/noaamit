$(function(){
	$(".only-numbers").live('keydown',function(event){
		if ( event.keyCode == 46 || event.keyCode == 8|| event.keyCode == 9 ) {
		} else {
			if (event.keyCode == 110) {
			}else{
				if (event.keyCode < 95) {
					if (event.keyCode < 48 || event.keyCode > 57 ) {
						event.preventDefault();
					}
				} else {
					if (event.keyCode < 96 || event.keyCode > 105 ) {
						event.preventDefault();
					}
				}
			}
		}
	});
});
