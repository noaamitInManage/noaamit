// gal zalait 20/03/2012


function isNumber(n) {
	
	n=$.trim(n);
	//var reg = new RegExp('/^[0-9]{0,}\d{0,}[0-9]$/');
	var strValue = "" + parseInt(n);
	if(parseInt(n)>0 && (n.length==strValue.length)){
		return true;
	}else{

	}
	return parseFloat(isFinite(n));
	//return !isNaN(parseFloat(n)) && isFinite(n);
}

function hide_screener(){
	$("#screener").hide();
}
function loadWidth(){
    return $(window).width();
}

$(window).on("resize load" , function(e){
    windowWidth = loadWidth();
});

function add_loader(){
	var layer = $("#screener > div:first");
	var layer_pos = layer.position();
	layer.parent().append('<div class="my_loader"><div class="center_me"></div></div>');
	var height=layer.height();
	var width=layer.width();
	var top=layer.css('top');
	var left=layer.css('left');
	var margin_left=layer.css('margin-left');
	height+=8;
	width+=8;
	$(".my_loader").height(height).width(width).css({'top':top,'left':left});
	if(margin_left){
		$(".my_loader").css('margin-left',margin_left);
	}
}
function remove_loader(){
	$(".my_loader").remove();
}

function email_validate(email){
	if(!new RegExp("^[\-a-zA-Z0-9\_\.]+@(?:[\-a-z0-9]+[\.])+[a-z\.]{2,10}$").test(email)){
		return false;
	}
	
return true;
}

function clear_combo(){
	var t=setTimeout(function(){
		$('#category_search_choose li[value=\'0\']').trigger('click'); 
	},500);
}

function setCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function getCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function deleteCookie(name) {
    setCookie(name,"",-1);
}

/*
 * Create and active native Select for mobile
 * Remember to re-trigger (again) this function if the element is not onload/ready
 */
function createNativeSelect(Scope){

    var optionList = "",
        dataValue = null,
        value = null,
        selectWrapper = "",
        selected = "",
        selectedVal = Scope.closest(".comboSelect").find("input[type=hidden]").val();

    if(windowWidth <= 960){
        Scope.closest(".comboSelect").find("ul li").each(function(){
            dataValue = $(this).attr("data-value"),
                value = $(this).text();

            if(selectedVal == value){
                selected = 'selected="selected"';
            } else {
                selected = '';
            }

            optionList += '<option value="'+ dataValue +'" ' + selected + '>'+ value +'</option>';
        });

        selectWrapper = '<select>'+ optionList +'</select>';

        Scope.closest(".comboSelect").append('<div class="nativeSelect"></div>');
        Scope.closest(".comboSelect").find(".nativeSelect").html(selectWrapper);
    }
}


function loadScript(script_src) {
	var script = document.createElement("script");
	script.type = "text/javascript";
	script.src = script_src;
	document.body.appendChild(script);
}

$(function(){

    $(document).on("change" , ".nativeSelect select", function(e){
        console.log("TESt");
        var Scope = $(this),
            newValue = Scope.val(),
            newText = Scope.closest(".nativeSelect").find("option[value='" + newValue + "']").text();

        Scope.closest(".comboSelect").find("span").html(newText + ' <i class="comboSelectArrowDown"></i>');

        Scope.closest(".comboSelect").find("input[type='hidden']").val(newValue);

    });

    function fix_ie7_select(){
        $(".comboSelect").each(function(index,item){
            $(item).css('z-index',10000-index);
        });
    }

    ie7=(navigator.appVersion.search('MSIE 7.0')>0)? true : false;

    var reg=/\"/g;

    windowWidth = loadWidth();

    if(windowWidth < 960){
        $(".comboSelect span").each(function(){
            var Scope = $(this);
            createNativeSelect($(this));
        });
        //    return false;
    }
    $(document).on('keydown',".num_only",function(event){
        if ( event.keyCode == 46 || event.keyCode == 8|| event.keyCode == 9 ) {
        } else {
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
    });

    $(document).on('click',".comboSelect span",function(event){
        $(".comboSelect ul").not($(this).parent().find('ul')).hide().css('z-index', 0);

        $(".comboSelect").removeClass("removeDoubleBottomBorder");
        $(this).parent().toggleClass("removeDoubleBottomBorder");
        $(this).parent().find('ul').toggle().css('z-index', 10000);

        $(this).parent().find('.closeComboSelect').css({"display" : "block"});
    });

    $(document).on('click',".comboSelect li",function(event){
        $(this).parent().parent().removeClass("removeDoubleBottomBorder");

        var scope=$(this);
        $(this).parent().parent().find('span').html($(this).text() + '<i class="comboSelectArrowDown"></i>');

        var val= scope.attr("data-value");
        $(this).parent().parent().find('input').val(''+ val);
        $(this).parent().css('display','none');

        $(this).parent().parent().find('.closeComboSelect').css({"display" : "none"});
    });


    $(".comboSelect").append("<div class='closeComboSelect'></span>");

    $(document).on('click',".closeComboSelect",function(event){

        $(".comboSelect ul").css('display','none');
        $(this).parent().find('.closeComboSelect').css({"display" : "none"});
        $(".comboSelect").removeClass("removeDoubleBottomBorder");

    });
});

