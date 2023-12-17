
var letters = "\]\[\~!\\@#\$%\^&\*\)\(\+\}\{|\>\<\?\":\`='"; // remove all non-valid characters
var meta_CreateChars = "["+letters+"]+";
var meta_MatchChars = "[^"+letters+"]+";

// check given url alias by regexp
function meta_CheckAlias(str){
	// check given STR
	var regExp = new RegExp(meta_MatchChars,"g");
	return !str.match(regExp);
	if (str.match(regExp) == "" || str.match(regExp) == null) return (window.event?false:true);
	else return (window.event?true:false);
}


/* create url alias from given STR using regexp */
function meta_CreateAlias(str){
	// remove unwanted chars by the regexp
	str = str.replace(/ /g,"_"); // replace SPACES by -
	var regExp = new RegExp(meta_CreateChars,"g");
	str = str.replace(regExp,"");
	return (str);
}

// prevent Right Mouse Click in the text input field
function noRightButton(event,msg) {
	if(event.button==2) {
		event.returnValue = false;
		alert("נא להזין את ה" + msg + " באופן ידני");
	}
	if (document.layers) {
		document.captureEvents(Event.MOUSEDOWN);
		document.onmousedown=clickNS4;
	}
	else if (document.all && !document.getElementById) {
		document.onmousedown=clickIE4;
	}
	return true;
}
// prevent usage of CTRL+V or Shift+Insert keys combination
function noPaste(e,msg) {
	if (window.event) { // IE
		if ((e.ctrlKey == true && e.keyCode == 86) || // Ctrl+V
		   (e.shiftKey == true && e.keyCode == 45)) { // Shift+Insert
			alert("נא להזין את " + msg + " באופן ידני");
			return false;
		}
	}else if (e.which) { // Netscape/Firefox/Opera
		if ((e.ctrlKey == true) ||				  // Ctrl only
		   (e.shiftKey == true && e.which == 45)) { // Shift+Insert
			alert("נא להזין את " + msg + " באופן ידני");
			return false;
		}
	}
	return true;
}
