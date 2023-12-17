
function display_as_link() {
	if (document.frmNew.iscat.value == "cat") { 
		document.getElementById('firstField').style.display = "block";
		document.getElementById('link_box').style.display   = "none";
	}else if (document.frmNew.iscat.value == "link" || document.frmNew.disp_as_link[0].value == "yes") {
			  document.getElementById('link_box').style.display = "block";
			  document.getElementById('firstField').style.display = "none";
	}
}

function link_box() {
	if (document.frmNew.disp_as_link[1].checked){
		document.getElementById('link_box').style.display = "none";
}else {	document.getElementById('link_box').style.display = "block";
		}
}


//   ta=document.createElement('input');
//   ta.setAttribute('height','100');
//   ta.setAttribute('width','200');

//ta.name='box';

//   document.getElementById('firstField').appendChild(ta);

// || document.frmNew.disp_as_link[0].value == "yes"