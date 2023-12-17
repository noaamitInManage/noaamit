
var forms_ENGLISH = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ -";
var forms_HEBREW = "��������������������������� -";
var forms_DIGITS = "0123456789 -";

function IsNumeric(sText){
	return (ChkStrBy(sText,forms_DIGITS));
}

function IsPrice(sText){
	return (ChkStrBy(sText,"0123456789."));
}

function IsHebrew(sText){
	return (ChkStrBy(sText,forms_HEBREW));
}

function IsEnglish(sText){
	return (ChkStrBy(sText,forms_ENGLISH));
}

function ChkStrBy(sText, sValidChars){
	var ret = true;
	if (sText.length==0) return (false);
	for (i = 0 ; i < (sText.length) && (ret==true) ; i++){ 
		if (sValidChars.indexOf(sText.charAt(i)) == -1){
			ret = false;
		}
	}
	return (ret);
}

function IsEmail(sText) {
	var at="@"
	var dot="."
	var lat=sText.indexOf(at)
	var lstr=sText.length
	var ldot=sText.indexOf(dot)
	if (sText=="" || sText==null){ return false }
	if (sText.indexOf(at)==-1 || sText.indexOf(at)==0 || sText.indexOf(at)==lstr){ return false }
	if (sText.indexOf(dot)==-1 || sText.indexOf(dot)==0 || sText.indexOf(dot)==lstr){ return false }
	if (sText.indexOf(at,(lat+1))!=-1){ return false }
	if (sText.substring(lat-1,lat)==dot || sText.substring(lat+1,lat+2)==dot){ return false }
	if (sText.indexOf(dot,(lat+2))==-1){ return false }
	if (sText.indexOf(" ")!=-1){ return false }
 	return true					
}

function IsPassword(sText,min,max){
	if ((min>0)&&(sText.length<min)) return (false);
	if ((max>0)&&(sText.length>max)) return (false);
	return (ChkStrBy(sText,forms_ENGLISH + "0123456789_"));
}

function IsPhone(sText){
	return (ChkStrBy(sText,"0123456789-"));
}

function ClearForm(form){
	var vars = form.elements;
	for(i=0; i < vars.length; i++){
		switch(vars[i].type){
			case 'textarea'   :
			case 'text'       : vars[i].value = ''; break;
			case 'select-multiple':
			case 'select-one' : vars[i].selectedIndex = 0; break;
			case 'checkbox'   :
			case 'radio'      : vars[i].checked = false; break;  			
  		}
	}
}

function ValidateForm(frm){
	for (i=0 ; i<frm.elements.length ; i++){
		elm = frm.elements[i];
		attr = elm.getAttribute("required");
		if ((attr != null) && (attr != "")){
			if (elm.type == "select-one"){ // single selection box
				if ((elm.value == "") || (elm.value == 0)){
					alert(attr);
					elm.focus();
					return false;
				}
			}else if (elm.type == "select-multiple"){ // multiple selection box
				return true;
			}else if ((elm.type != "button") && (elm.type != "submit")){
				if (elm.value == ""){
					alert(attr);
					elm.focus();
					return false;
				}
			}
		}
	}
	return true;
}
