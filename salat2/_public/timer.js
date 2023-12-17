function strstr(haystack, needle, bool) {
var pos = 0;

haystack += "";
pos = haystack.indexOf(needle); if (pos == -1) {
return false;
} else {
if (bool) {
    return haystack.substr(0, pos);
} else {
    return haystack.slice(pos);
}
}
}
var session_time=1800;
var session_timerID=0;
(function(){
	if(document.location.href.indexOf("navigation.php")>-1||document.location.href.indexOf("index.php")>-1){return false;}
	var div=document.createElement("div");
	div.style.position="absolute";
	div.style.top="9px";
	// div.style.left="12px";
	div.style.border="2px dotted black";
	div.style.padding="6px 15px";
	div.style.width="30px";
	div.style.textAlign="center";
	div.style.backgroundColor="#FFFFFF";
	div.style.fontWeight="bold";
	div.id="session_timer";
	document.body.appendChild(div);
	var min = Math.floor(session_time/60).toString();
	var sec = Math.floor(session_time%60).toString();
	document.getElementById("session_timer").innerHTML = (min.length<2?"0"+min:min)+":"+(sec.length<2?"0"+sec:sec);
	session_time--;
	session_timerID=window.setInterval(function(){
		if( (session_time==10) && inmanage_ip){
			document.location.reload();
		}
		if(session_time==0){
		 //  document.location.reload();
			alert("*******************************************\n\nפג תוקף זמן החיבור!\n\n*******************************************");
			window.clearTimeout(session_timerID);
		}
		min = Math.floor(session_time/60).toString();
		sec = Math.floor(session_time%60).toString();
		document.getElementById("session_timer").innerHTML = (min.length<2?"0"+min:min)+":"+(sec.length<2?"0"+sec:sec);
		session_time--;
	}, 1000);
	

    $('.submenu').prepend('<li><input type="text" class="search_module" value="Search..." /></li>');
    //console.log($('.search_module').live('keyUp').next('li').text());
    $('.search_module').live('focusin',function(){$(this).val('')}).live('focusout',function(){ $(this).val('חפש...');});
    $('.search_module').live('keyup',function(event){
        var search = $(this);
        var key = event.keyCode;
        var modNiddle = search.val();
        
        if(key==13){
           var link = search.parent('li').next('li').children('a').attr('href');
           window.location = link;
        }
        
        search.parent().parent().find('li>a').each(function(i,index){
            if(strstr($(this).text(),modNiddle)){
                $(this).show();
                
            }else{
                $(this).hide();
            }
            if(modNiddle==""){
                $('.submenu').parent().find('li').fadeIn();
            }
        });
        
    });
          
                
})();