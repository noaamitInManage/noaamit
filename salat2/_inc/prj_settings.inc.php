<?php

include_once ($_SERVER['DOCUMENT_ROOT'] .'/_inc/class/configManager.class.inc.php');
include($_project_server_path . '_static/mediaCategory.inc.php');//$mediaCategorysArr
date_default_timezone_set('Asia/Jerusalem');
// blue
$_color_dark = "#000000";//"#2660A2";	//"#26A255";
$_color_normal = "#0573C7";    //"#05C74F";
$_color_normal_s = "#fcfcfc";    //"#05C74F";
$_color_normal_sd = "#fafafa";    //"#05C74F";
$_color_normal_d = "silver";        //"#05C74F";
$_color_lite = "#8CB7E7";    //"#8CE799";
$_color_text = "#FFFFFF";
$_color_red = "#FF3333";
$_color_green = "green";
$top_color = configManager::is_dev_mode() ? "#DE3D3A" : "#000";// CMS top header color -
//          for live use #000
// for dev use : #DE3D3A
// for stage use : #ef8522


$dir = $_LANG['salat_dir'] == 'ltr' ? 'left' : 'right';
if ($_LANG['salat_dir'] == 'rtl') {
    $menu_css = <<<Css

.ltr-text { direction:ltr;}
.rtl-text { direction:rtl;}

#div_menu{
	height:60px !important; 
	background-color:#0573C7; 
	/*float:right;*/
	
}
#menu-header{
	height: 30px;
	float: right;
	list-style: none;
/*	background-color:#fcfcfc;*/
	font-familiy:arial;
	font-size:14px;
	margin-top:2px;
}

#menu-header li{
	display: inline;
	position: relative;
	float: right;
	line-height:20px;
	height:30px;
}

#menu-header li a {
	padding: 4px 11px 7px;
/*	border-top: 3px solid rgb(60, 182, 216); */
}


#menu-header > li > a {
	font-weight:bold;
	color:white;
}
#menu-header li a:hover {
/*	border-top: 3px solid rgb(60, 182, 216);*/
/*	background-color: white;	*/
	font-weight:bold;
	color:gray;
}
#menu-header li ul li a:hover {
	color:white;
}
#menu-header li ul li.blue_menu_backgr {
	background-color:#0088cc;

}
/*#menu-header ul  li:hover{
background-color:#0088cc;
}*/

#menu-header li > ul {
	display: none;
	position: absolute;
	top: 20px;
	right: -50px;
	float: right;
	margin-right: 10px;
	width: 124px;
	z-index: 99999;
	border-bottom:2px solid white;
	padding-bottom: 4px;
	background-color:#fcfcfc;
}

#menu-header li > ul > li  ul.side_menu {
    display: none;
	position: absolute;
	top: -4px;
	right: 105px;
	float: right;
	margin-right: 10px;
	width: 124px;
	z-index: 99999;
	border-bottom:2px solid #cccccc;
	padding-bottom: 4px;
	/*	background-color:#fcfcfc;*/
}

#menu-header li > ul > li  ul.sub_side_menu {
    display: none;
	position: absolute;
	top: -1px;
	right: 135;
	float: right;
	margin-right: 10px;
	width: 124px;
	z-index: 99999;
	border-bottom:2px solid #cccccc;
	padding-bottom: 4px;
	/*	background-color:#fcfcfc;*/
}

.side_menu, .sub_side_menu{
  border:1px solid #e5e5e5;
  display:none;
  border-radius:5px;
 }

#menu-header li ul li:hover ul {	display: block;}


#menu-header > li:hover ul {
	display: block;
}
#menu-header li:hover a{
/*	border-top: 3px solid rgb(60, 182, 216);*/
	/*background-color: #fcfcfc;*/
/*	*/
}
#menu-header ul li:hover a:hover{
font-weight:bold;
}
#menu-header .submenu{
border-bottom: 1px solid #e5e5e5;
border-right: 1px solid #e5e5e5;
border-left: 1px solid #e5e5e5;
/*border-top:2px solid rgb(60, 182, 216);*/
border-radius:5px;
}
#menu-header li ul li:first-child {
	padding: 5px 6px 0;
	/*////////////////////////////////*/
  border-bottom: 1px solid #cccccc;
	/*float: right;*/
}

#menu-header li ul li:first-child input{
	float: right;
}
#menu-header li ul li {
	background-color: #fcfcfc;
	padding: 0 6px;
	/*border-top:1px solid black;*/
	background-repeat: no-repeat;
	background-position: 50% 0; 
	float: left;
}


#menu-header li ul a {
	color: #000;
	font-size: 14px;
	height: auto;
	width:152px;
	text-align: right;
	border: 0 !important;
	padding: 0;
	float: left;
	background: none !important;
}

#menu-header li ul a:hover {
	border: 0;
	float: left;
	padding: 0;
	background: none;
	text-decoration: none;
/*	font-weight: bold;*/
}
#menu-header li  a:hover {

/*	font-weight: bold;*/
}

#menu-header li.child{padding-left:-5px;}
#menu-header a{text-decoration:none;}

html>body #menu-header li ul a:hover{
	font-weight: normal;
}
#menu-header .m_pls{float:left; margin-left:5px;}
/*#menu-header .hidden{display:none !important;}*/

#menu-header .menu_sep{
	height:52px;
	width:2px;
	background-color:white;
	padding:4px 0px;
	float:right;
}

#menu-header .blue_menu_backgr{
  background-color:#0088cc !important;/*rgb(0, 136, 204);#0088cc;*/
}

Css;

    $_salat_style = "
<style type=\"text/css\">
    #session_timer { left: 12px; }
	a img{border: 0px;}
	table{border-spacing: 0px;
    padding: 0px 0px 0px 0px;
    margin-top:30px;}
    td a{color:#333333 !important;}
	*{direction: " . $_LANG['salat_dir'] . ";}

	.liteTxt{ color:" . $_color_dark . "; }
	.liteTxtsel{ color:white;background-color:" . $_color_lite . ";font-weight:bold; }

	.tblTxt{ color:" . $_color_text . "; font-size:12px; }
	.titleTxt{ color:" . $_color_dark . "; font-size: 24px; font-weight:bold; border-bottom: 2px solid silver; }
	.btn{ border:solid 1px " . $_color_dark . ";color:" . $_color_text . ";font-size:15px;height:22px;background-color:" . $_color_normal . ";cursor:pointer }
	input,textarea{ border: 1px solid " . $_color_dark . "; color: $_color_normal; font-family: Arial; }
	label{-moz-user-select:none;}

	.green {
	  color: " . $_color_green . ";
	}
	.bold {
	  font-weight:bold;
	}
	.pointer{
	  cursor:pointer;
	}
	.excel{display:inline;cursor:pointer;width:auto;height:auto;float:left;}
	.act_buttons{
		font-weight: bold;
		width: 100%;
		background: " . $_color_normal_s . ";
		border: 0px;
		color: " . $_color_normal . "; }
		
	.cat_buttons{
		cursor:hand;
		font-weight: bold;
		background: #fff;
		border: 1px solid " . $_color_dark . ";
		color: " . $_color_normal . "; }
		
	.txtlink{
		color: $_color_dark;
		text-decoration: none;
	}
	
	.rtl{
		direction: rtl;
		color: " . $_color_dark . ";
	}
	
	.ltr{
		direction: ltr;
		color: #8bab00;
	}
				/* auto media load */	   
		.selected             { display: block; }
		.show   {display:block;}
		.off   {display:none;}
		#image_con   { height:40px;cursor:pointer;}
		.selected_item{border:1px solid red;}
		/* end of  auto media load */	   
	.TitleActive{ border: solid 1px " . $_color_normal . "; background-color: " . $_color_normal . "; color: " . $_color_text . "; font-weight: bold; }
	.TitleDeActive{ border: dotted 1px " . $_color_normal_d . "; background-color: " . $_color_normal_d . "; color: " . $_color_text . "; font-weight: bold; }
	.solidTbl{ border:solid 1px " . $_color_normal . "; }
	.showpic{ border: 2px solid $_color_normal_d;position: absolute;margin: 10px 15px 0px 0px !important;margin: 31px -223px 0px 0px;width: 120px;height: 120px;		background-color: #FFFFFF;}
	.cursor{cursor:pointer;}
	.citywall{color:#A3D93D;}
	.orange{color:orange !important;}
    .hover{background-color:#DCDCDC; font-weight:bold;}
    .genricTable td:hover{font-weight:bold;}
	.genricTable tr.odd > td{ background:#f7f7f7; }
	.genricTable{ border:1px solid #d1d5d7; margin:20px auto; width:665px; }
	.genricTable th, .genricTable td{ border-left:1px solid #d1d5d7; text-align:center; vertical-align:middle; width:95px; }
	.genricTable th{ background:#3cb6d8; color:#fff; font-size:14px; font-weight:bold; height:30px; }
	.genricTable td{ background:#fff; color:#333; font-size:12px; height:24px; }
	.genricTable td:hover{font-weight:bold;}
	.hover{background-color:#DCDCDC; font-weight:bold;}
	.genricTable tr.odd > td{ background:#f7f7f7; }

	.ltr-text { direction:ltr;}
	.rtl-text { direction:rtl;}
	'.$menu_css.'
	
	body{margin-top:-10px;}
/*=============START==2/05/13========================*/
.user_logout_group a{
  text-decoration:none;
}
.user_logout_group
{
 display: inline-block;
 font-size: 0;
 white-space: nowrap;
 cursor:pointer;
 float:left;
 margin-left:80px;
 margin-top:-14px;
 border-right:1px solid white;
 padding: 0px 6px;

}

.user_logout_menu {
  margin-top:1px;
  font-size:14px;
  position: absolute;
  padding: 10px 0px;
  line-height:10px;
  top:100%;
  left:0px;
  z-index: 1000;
  height:20px;
  display: none;
  float: left;
  min-width: 150px;
  list-style: none;
  background-color: #fdfcfc;
  border: 1px solid #ccc;
  border: 1px solid rgba(0, 0, 0, 0.2);
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
}

.user_logout_menu li:hover{
  background-color:#0088cc;
}

.user_logout_menu a:hover{
  color:white;
}

.user_logout_menu a{
  color:#333333;
}

.logoutbtn {
  display: inline-block;
  padding: 0px 8px 0px 6px;
  line-height: 7px;
  margin-top:7px;
  text-align: center;
  text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
  vertical-align: middle;
  cursor: pointer;
  background-color: #f5f5f5;
  background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));
  background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: -o-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: linear-gradient(to bottom, #ffffff, #e6e6e6);
  filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#ffffff', EndColorStr='#e6e6e6');
  background-repeat: repeat-x;
  border: 1px solid #cccccc;
  border-color: #e6e6e6 #e6e6e6 #bfbfbf;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  border-bottom-color: #b3b3b3;
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
 /* filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#ffe6e6e6', GradientType=0);
  filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);*/
  filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#ffffff', EndColorStr='#e6e6e6');
  -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
  -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
}


.logout_text{
  color:#333333;
  font-size:14px;
  margin:10px 4px 10px 4px;
}

.logoutbtn p:hover {
  color:gray;
}
.user_logout_group li:hover ul {display: block;}

.user_logout_group > .logoutbtn {
   position: relative;
   margin-right:3px;
  -webkit-border-radius: 4px;
  border-radius: 4px;
  -moz-border-radius: 4px;
}

/*================END==2-05-13========================*/
/*============start===========1.5.13============*/
 /*.side_menu li:hover{
   background-color:#0088cc !important;
 }*/
 .side_menu li{
  height:25px !important;
}

.icon_menu{
 width:15px;
 height:15px;
}

#menu-header li ul li:second-child {
  padding: 5px 6px 0;
  height: 1px;
  margin: 9px 1px;
  overflow: hidden;
  background-color: #e5e5e5;
  border-bottom: 1px solid #cccccc;
}

#menu-header ul  li:first-child:hover{
   background-color:#fdfcfc;
 }

input.search_module:hover{
  border-color: #0088cc;
}
input.search_module{
  background-color: #fdfcfc;
  border: 1px solid #cccccc;
  color:#333333;
  border-radius:3px;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  width: 153px;
  height:25px;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
  -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
  -webkit-transition: border linear .2s, box-shadow linear .2s;
  -moz-transition: border linear .2s, box-shadow linear .2s;
  -o-transition: border linear .2s, box-shadow linear .2s;
  transition: border linear .2s, box-shadow linear .2s;
  margin-bottom:5px;
}

/*===========END===1.5.13==================*/
/* =====30.4.13 bootstap===================*/




	.tabs{
        height:35px;
	    margin:5px 5px;
	    padding:3px 7px;
	    cursor:pointer;
	    background:#fff;
	    color:#000;
	    font-size:14px;
		border: 1px solid #84868b;
		border-radius:4px;
		-webkit-border-radius: 4px;
        -moz-border-radius: 4px;
	}
	.table-edit .dottTblS,.dottTblSd{
     border: 1px solid dashed #fcfcfc;
      border-space:0px;
	}
	.dottTblS input,textarea{
	  border-radius:3px;
	  -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
	  border: 1px solid #84868b;
	}

 .tabs.active{
	 background-color:#0573C7;
	 font-weight:bold;
	 color: #ffffff;
     text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
     background-color: #84868b;
     background-image: -moz-linear-gradient(top, #84868b, #000);
     background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#84868b), to(#000));
     background-image: -webkit-linear-gradient(top, #84868b, #000);
     background-image: -o-linear-gradient(top, #84868b, #000);
     background-image: linear-gradient(to bottom, #84868b, #000);
     background-repeat: repeat-x;
     border-color: #84868b #0044cc #000;
     border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
     filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#84868b', EndColorStr='#000');
   }

    .tabs_con input{
       margin:15px 5px;
       padding:5px;
      }

    .buttons{
         border-style:outset;
         border-radius:4px;
         -webkit-border-radius: 4px;
         -moz-border-radius: 4px;
         font-size: 11px;
         vertical-align: middle;
         cursor: pointer;
         color: #333333;
         border-color:#c5c5c5;
         height:25px;
         margin: 2px 0px 2px 0px;
         padding:2px 6px 2px 6px;
         -webkit-box-shadow: inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);
         -moz-box-shadow: inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);
         box-shadow: inset 0 1px 0 rgba(255,255,255,.2), 0 1px 2px rgba(0,0,0,.05);
         background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#FFFFFF), to(#e6e6e6));
         background: -webkit-linear-gradient(top, #FFFFFF, #e6e6e6);
         background: -moz-linear-gradient(top, #FFFFFF, #e6e6e6);
         background: -ms-linear-gradient(top, #FFFFFF, #e6e6e6);
         background: -o-linear-gradient(top, #FFFFFF, #e6e6e6);
         filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#FFFFFF', EndColorStr='#e6e6e6');
	}

	.red{
	   background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#ee5f5b), to(#bd362f));
       background: -webkit-linear-gradient(top, #ee5f5b, #bd362f);
       background: -moz-linear-gradient(top,  #ee5f5b, #bd362f);
       background: -ms-linear-gradient(top,  #ee5f5b, #bd362f);
       background: -o-linear-gradient(top,  #ee5f5b, #bd362f);
       filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#ee5f5b', EndColorStr='#bd362f');
       border-color: #bd362f #bd362f #802420;
       color:white;
	}

	.buttons:hover{
	  border-color: gray;
	  text-shadow: 1px 1px #b8b9bc;
	  color:gray;
	}

   .dottTbl{
        background-color: #eee;
		color: #333333;
		margin: 0px 0px 0px 0px;
        border-spacing: 0px;
        padding: 4px 5px;
        height: 35px;
        text-align: right;
        font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;
        font-size: 14px;
        font-weight: bold;
        direction: rtl;
	}

	.dottTblo{ border:dotted 1px " . $_color_normal . "; }

	.dottTble{
		background: url(../images/title_bg2.gif);
		color: " . $_color_text . ";
	}

	.dottTblS{
		/*background: " . $_color_normal_s . ";*/
		text-align:" . ($_LANG['salat_dir'] == 'ltr' ? 'left' : 'right') . ";
		background: #fcfcfc;
        border: 1px dashed #dddddd;
        margin:0px 0px 0px 0px;
        border-spacing: 0px;
	}

	.dottTblSd{
		/*background: " . $_color_normal_sd . ";*/
		background: #fcfcfc;
		border: 1px solid #dddddd;
 }

.normTxt{ border-bottom: 1px solid #dddddd;
       border-left: 1px solid green;
       border-right: 1px solid red;
       margin: 0px 0px 0px 0px;
       color:#333;
       font-size:12px;
    }
    .normTxtOff{ color:" . $_color_normal_d . ";font-size:12px }
    .normTxt.odd .dottTblS{background-color:#fdfcfc; }

	.normTxt.hover{background-color:red !important ;}

	.normTxt.even .dottTblS{background-color:#ffffff ; }
	.normTxt.hover td     {background:wheat;}

 #search_frm td input{
      border-radius:3px;
      -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
      background-color:#fdfcfc;
      border: 1px solid #84868b;
      height: 27px;
    }

#search_frm  .buttons{
  color: #ffffff;
  text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
  background-color: #006dcc;
  background-image: -moz-linear-gradient(top, #0088cc, #0044cc);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0088cc), to(#0044cc));
  background-image: -webkit-linear-gradient(top, #0088cc, #0044cc);
  background-image: -o-linear-gradient(top, #0088cc, #0044cc);
  background-image: linear-gradient(to bottom, #0088cc, #0044cc);
  background-repeat: repeat-x;
  border-color: #0044cc #0044cc #002a80;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0088cc', endColorstr='#ff0044cc', GradientType=0);
  filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
}

#search_frm  .buttons:hover{
  background-color: #0044cc;
  background-image: -moz-linear-gradient(top, #0044cc, #0044cc);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#0044cc), to(#0044cc));
  background-image: -webkit-linear-gradient(top, #0044cc, #0044cc);
  background-image: -o-linear-gradient(top, #0044cc, #0044cc);
  background-image: linear-gradient(to bottom, #0044cc, #0044cc);
  background-repeat: repeat-x;
  filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ff0044cc', endColorstr='#ff0044cc', GradientType=0);
  filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);
}

	/* 30.4.13 end bootstap */
.box_table{
	float: right;
	text-align: center;
	padding: 0px;
	margin-right: 10px;
	margin-bottom: 15px;
	margin-top: 17px;
}



</style>";
} else {
    $menu_css = <<<Css

.ltr-text { direction:ltr;}
.rtl-text { direction:rtl;}

#div_menu{
	height:60px !important; 
	background-color:#0573C7; 
	/*float:right;*/
	
}
#menu-header{
	height: 30px;
	float: left;
	list-style: none;
/*	background-color:#fcfcfc;*/
	font-family:arial;
	font-size:14px;
	margin-top:2px;
	/*padding: 0;*/
}

#menu-header li{
	display: inline;
	position: relative;
	float: left;
	line-height:20px;
	height:30px;
}

#menu-header li a {
	padding: 4px 11px 7px;
/*	border-top: 3px solid rgb(60, 182, 216); */
}


#menu-header > li > a {
	font-weight:bold;
	color:white;
}
#menu-header li a:hover {
/*	border-top: 3px solid rgb(60, 182, 216);*/
/*	background-color: white;	*/
	font-weight:bold;
	color:gray;
}
#menu-header li ul li a:hover {
	color:white;
}
#menu-header li ul li.blue_menu_backgr {
	background-color:#0088cc;

}
/*#menu-header ul  li:hover{
background-color:#0088cc;
}*/

#menu-header li > ul {
	display: none;
	position: absolute;
	top: 20px;
	left: -50px;
	float: left;
	margin-left: 10px;
	width: 124px;
	z-index: 99999;
	border-bottom:2px solid white;
	padding-bottom: 4px;
	background-color:#fcfcfc;
}

#menu-header li > ul > li  ul.side_menu {
    display: none;
	position: absolute;
	top: -4px;
	left: 153px;
	float: left;
	margin-left: 10px;
	width: 124px;
	z-index: 99999;
	border-bottom:2px solid #cccccc;
	padding-bottom: 4px;
	/*	background-color:#fcfcfc;*/
}
.side_menu{
  border:1px solid #e5e5e5;
  display:none;
  border-radius:5px;
  padding: 0;
 }

#menu-header li ul li:hover ul {	display: block;}


#menu-header > li:hover ul {
	display: block;
}
#menu-header li:hover a{
/*	border-top: 3px solid rgb(60, 182, 216);*/
	/*background-color: #fcfcfc;*/
/*	*/
}
#menu-header ul li:hover a:hover{
font-weight:bold;
}
#menu-header .submenu{
border-bottom: 1px solid #e5e5e5;
border-right: 1px solid #e5e5e5;
border-left: 1px solid #e5e5e5;
/*border-top:2px solid rgb(60, 182, 216);*/
border-radius:5px;
padding: 0;
}
#menu-header li ul li:first-child {
	padding: 5px 6px 0;
	/*////////////////////////////////*/
  border-bottom: 1px solid #cccccc;
	/*float: right;*/
}

#menu-header li ul li:first-child input{
	float: right;
}
#menu-header li ul li {
	background-color: #fcfcfc;
	padding: 0 6px;
	/*border-top:1px solid black;*/
	background-repeat: no-repeat;
	background-position: 50% 0; 
	float: left;
}


#menu-header li ul a {
	color: #000;
	font-size: 14px;
	height: auto;
	width:152px;
	text-align: left;
	border: 0 !important;
	padding: 0;
	float: left;
	background: none !important;
}

#menu-header li ul a:hover {
	border: 0;
	float: left;
	padding: 0;
	background: none;
	text-decoration: none;
/*	font-weight: bold;*/
}
#menu-header li  a:hover {

/*	font-weight: bold;*/
}

#menu-header li.child{padding-left:-5px;}
#menu-header a{text-decoration:none;}

html>body #menu-header li ul a:hover{
	font-weight: normal;
}
#menu-header .m_pls{float:right; margin-left:5px; transform: rotate(180deg);}
/*#menu-header .hidden{display:none !important;}*/

#menu-header .menu_sep{
	height:52px;
	width:2px;
	background-color:white;
	padding:4px 0px;
	float:right;
}

#menu-header .blue_menu_backgr{
  background-color:#0088cc !important;/*rgb(0, 136, 204);#0088cc;*/
}


Css;

    $_salat_style = "
<style type=\"text/css\">
    
    #session_timer { right: 12px; }
	a img{border: 0px;}
	table{border-spacing: 0px;
    padding: 0px 0px 0px 0px;
    margin-top:30px;}
    td a{color:#333333 !important;}
	*{direction: " . $_LANG['salat_dir'] . ";}

	.liteTxt{ color:" . $_color_dark . "; }
	.liteTxtsel{ color:white;background-color:" . $_color_lite . ";font-weight:bold; }

	.tblTxt{ color:" . $_color_text . "; font-size:12px; }
	.titleTxt{ color:" . $_color_dark . "; font-size: 24px; font-weight:bold; border-bottom: 2px solid silver; }
	.btn{ border:solid 1px " . $_color_dark . ";color:" . $_color_text . ";font-size:15px;height:22px;background-color:" . $_color_normal . ";cursor:pointer }
	input,textarea{ border: 1px solid " . $_color_dark . "; color: $_color_normal; font-family: Arial; }
	label{-moz-user-select:none;}

	.green {
	  color: " . $_color_green . ";
	}
	.bold {
	  font-weight:bold;
	}
	.pointer{
	  cursor:pointer;
	}
	.excel{display:inline;cursor:pointer;width:auto;height:auto;float:left;}
	.act_buttons{
		font-weight: bold;
		width: 100%;
		background: " . $_color_normal_s . ";
		border: 0px;
		color: " . $_color_normal . "; }
		
	.cat_buttons{
		cursor:hand;
		font-weight: bold;
		background: #fff;
		border: 1px solid " . $_color_dark . ";
		color: " . $_color_normal . "; }
		
	.txtlink{
		color: $_color_dark;
		text-decoration: none;
	}
	
	.rtl{
		direction: rtl;
		color: " . $_color_dark . ";
	}
	
	.ltr{
		direction: ltr;
		color: #8bab00;
	}
				/* auto media load */	   
		.selected             { display: block; }
		.show   {display:block;}
		.off   {display:none;}
		#image_con   { height:40px;cursor:pointer;}
		.selected_item{border:1px solid red;}
		/* end of  auto media load */	   
	.TitleActive{ border: solid 1px " . $_color_normal . "; background-color: " . $_color_normal . "; color: " . $_color_text . "; font-weight: bold; }
	.TitleDeActive{ border: dotted 1px " . $_color_normal_d . "; background-color: " . $_color_normal_d . "; color: " . $_color_text . "; font-weight: bold; }
	.solidTbl{ border:solid 1px " . $_color_normal . "; }
	.showpic{ border: 2px solid $_color_normal_d;position: absolute;margin: 10px 15px 0px 0px !important;margin: 31px -223px 0px 0px;width: 120px;height: 120px;		background-color: #FFFFFF;}
	.cursor{cursor:pointer;}
	.citywall{color:#A3D93D;}
	.orange{color:orange !important;}
    .hover{background-color:#DCDCDC; font-weight:bold;}
    .genricTable td:hover{font-weight:bold;}
	.genricTable tr.odd > td{ background:#f7f7f7; }
	.genricTable{ border:1px solid #d1d5d7; margin:20px auto; width:800px; }
	.genricTable th, .genricTable td{ border-left:1px solid #d1d5d7; text-align:center; vertical-align:middle; width:95px; }
	.genricTable th{ background:#3cb6d8; color:#fff; font-size:14px; font-weight:bold; height:30px; }
	.genricTable td{ background:#fff; color:#333; font-size:12px; height:24px; }
	.genricTable td:hover{font-weight:bold;}
	.hover{background-color:#DCDCDC; font-weight:bold;}
	.genricTable tr.odd > td{ background:#f7f7f7; }

	.ltr-text { direction:ltr;}
	.rtl-text { direction:rtl;}
	'.$menu_css.'
	
	body{margin-top:-10px;}
/*=============START==2/05/13========================*/
.user_logout_group a{
  text-decoration:none;
}
.user_logout_group
{
 display: inline-block;
 font-size: 0;
 white-space: nowrap;
 cursor:pointer;
 float:right;
 margin-right:100px;
 margin-top:-14px;
 border-left:1px solid white;
 padding: 0px 6px;

}

.user_logout_menu {
  margin-top:1px;
  font-size:14px;
  position: absolute;
  padding: 10px 0px;
  line-height:10px;
  top:100%;
  left:0px;
  z-index: 1000;
  height:20px;
  display: none;
  float: left;
  min-width: 150px;
  list-style: none;
  background-color: #fdfcfc;
  border: 1px solid #ccc;
  border: 1px solid rgba(0, 0, 0, 0.2);
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
}

.user_logout_menu li:hover{
  background-color:#0088cc;
}

.user_logout_menu a:hover{
  color:white;
}

.user_logout_menu a{
  color:#333333;
}

.logoutbtn {
  display: inline-block;
  padding: 0px 8px 0px 6px;
  line-height: 7px;
  margin-top:7px;
  text-align: center;
  text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
  vertical-align: middle;
  cursor: pointer;
  background-color: #f5f5f5;
  background-image: -moz-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#ffffff), to(#e6e6e6));
  background-image: -webkit-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: -o-linear-gradient(top, #ffffff, #e6e6e6);
  background-image: linear-gradient(to bottom, #ffffff, #e6e6e6);
  filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#ffffff', EndColorStr='#e6e6e6');
  background-repeat: repeat-x;
  border: 1px solid #cccccc;
  border-color: #e6e6e6 #e6e6e6 #bfbfbf;
  border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
  border-bottom-color: #b3b3b3;
  -webkit-border-radius: 4px;
  -moz-border-radius: 4px;
  border-radius: 4px;
 /* filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffffff', endColorstr='#ffe6e6e6', GradientType=0);
  filter: progid:DXImageTransform.Microsoft.gradient(enabled=false);*/
  filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#ffffff', EndColorStr='#e6e6e6');
  -webkit-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
  -moz-box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
  box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.05);
}


.logout_text{
  color:#333333;
  font-size:14px;
  margin:10px 4px 10px 4px;
}

.logoutbtn p:hover {
  color:gray;
}
.user_logout_group li:hover ul {display: block;}

.user_logout_group > .logoutbtn {
   position: relative;
   margin-right:3px;
  -webkit-border-radius: 4px;
  border-radius: 4px;
  -moz-border-radius: 4px;
}

/*================END==2-05-13========================*/
/*============start===========1.5.13============*/
 .side_menu li:hover{
   background-color:#0088cc !important;
 }
 .side_menu li{
  height:25px !important;
}

.icon_menu{
 width:15px;
 height:15px;
}

#menu-header li ul li:second-child {
  padding: 5px 6px 0;
  height: 1px;
  margin: 9px 1px;
  overflow: hidden;
  background-color: #e5e5e5;
  border-bottom: 1px solid #cccccc;
}

#menu-header ul  li:first-child:hover{
   background-color:#fdfcfc;
 }

input.search_module:hover{
  border-color: #0088cc;
}
input.search_module{
  background-color: #fdfcfc;
  border: 1px solid #cccccc;
  color:#333333;
  border-radius:3px;
  -webkit-border-radius: 3px;
  -moz-border-radius: 3px;
  width: 153px;
  height:25px;
  -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
  -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
  box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
  -webkit-transition: border linear .2s, box-shadow linear .2s;
  -moz-transition: border linear .2s, box-shadow linear .2s;
  -o-transition: border linear .2s, box-shadow linear .2s;
  transition: border linear .2s, box-shadow linear .2s;
  margin-bottom:5px;
}

</style>";
}

$_salat_style .= <<<SCR

<link rel="stylesheet" type="text/css" href="/salat2/_public/slick/css/slick.css"/>
<link rel="stylesheet" type="text/css" href="/salat2/_public/slick/css/slick-theme.css"/>
<script type="text/javascript" src="/salat2/_public/jquery-migrate-1.2.1.min.js"></script>
<script type="text/javascript" src="/salat2/_public/slick/js/slick.min.js"></script>
<script type="text/javascript">
   var direction = document.getElementsByTagName("html")[0].getAttribute("dir");
   direction === "rtl" ? direction = true : direction = false;
   $(document).ready(function(){
      $('.genric_gallery').slick({
        dots: true,
        rtl:direction,
        infinite:false,
        arrows: true,
        slidesToShow: 6,
        slidesToScroll: 3,
        centerMode: false
      });
   });
    
</script>
<style type="text/css">

/*===========END===1.5.13==================*/
/* =====30.4.13 bootstap===================*/




	.tabs{
        height:35px;
	    margin:5px 5px;
	    padding:3px 7px;
	    cursor:pointer;
	    background:#fff;
	    color:#000;
	    font-size:14px;
		border: 1px solid #84868b;
		border-radius:4px;
		-webkit-border-radius: 4px;
        -moz-border-radius: 4px;
	}
	.table-edit .dottTblS,.dottTblSd{
     border: 1px solid dashed #fcfcfc;
      border-space:0px;
	}
	.dottTblS input,textarea{
	  border-radius:3px;
	  -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
	  border: 1px solid #84868b;
	}

 .tabs.active{
	 background-color:#0573C7;
	 font-weight:bold;
	 color: #ffffff;
     text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
     background-color: #84868b;
     background-image: -moz-linear-gradient(top, #84868b, #000);
     background-image: -webkit-gradient(linear, 0 0, 0 100%, from(#84868b), to(#000));
     background-image: -webkit-linear-gradient(top, #84868b, #000);
     background-image: -o-linear-gradient(top, #84868b, #000);
     background-image: linear-gradient(to bottom, #84868b, #000);
     background-repeat: repeat-x;
     border-color: #84868b #0044cc #000;
     border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
     filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#84868b', EndColorStr='#000');
   }

    .tabs_con input{
       margin:15px 5px;
       padding:5px;
      }


	.red{
	   background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#ee5f5b), to(#bd362f));
       background: -webkit-linear-gradient(top, #ee5f5b, #bd362f);
       background: -moz-linear-gradient(top,  #ee5f5b, #bd362f);
       background: -ms-linear-gradient(top,  #ee5f5b, #bd362f);
       background: -o-linear-gradient(top,  #ee5f5b, #bd362f);
       filter: progid:DXImageTransform.Microsoft.gradient(startColorStr='#ee5f5b', EndColorStr='#bd362f');
       border-color: #bd362f #bd362f #802420;
       color:white;
	}



   .dottTbl{
        background-color: #eee;
		color: #333333;
		margin: 0px 0px 0px 0px;
        border-spacing: 0px;
        padding: 4px 5px;
        height: 35px;
        text-align: left;
        font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;
        font-size: 14px;
        font-weight: bold;
        direction: rtl;
	}

	.dottTblo{ border:dotted 1px {$_color_normal }; }

	.dottTble{
		background: url(../images/title_bg2.gif);
		color: {$_color_text};
	}

	.dottTblS{
		/*background: " . $_color_normal_s . ";*/
		text-align:{$dir};
		background: #fcfcfc;
        border: 1px dashed #dddddd;
        margin:0px 0px 0px 0px;
        border-spacing: 0px;
	}

	.dottTblSd{
		/*background: " . $_color_normal_sd . ";*/
		background: #fcfcfc;
		border: 1px solid #dddddd;
 }

.normTxt{ border-bottom: 1px solid #dddddd;
       border-left: 1px solid green;
       border-right: 1px solid red;
       margin: 0px 0px 0px 0px;
       color:#333;
       font-size:12px;
    }
    .normTxtOff{ color:" . $_color_normal_d . ";font-size:12px }
    .normTxt.odd .dottTblS{background-color:#fdfcfc; }

	.normTxt.hover{background-color:red !important ;}

	.normTxt.even .dottTblS{background-color:#ffffff ; }
	.normTxt.hover td     {background:wheat;}

 #search_frm td input{
      border-radius:3px;
      -webkit-border-radius: 3px;
      -moz-border-radius: 3px;
      background-color:#fdfcfc;
      border: 1px solid #84868b;
      height: 27px;
}

	/* 30.4.13 end bootstap */
.box_table{
	float: right;
	text-align: center;
	padding: 0px;
	margin-right: 10px;
	margin-bottom: 15px;
	margin-top: 17px;
}


/*====== Salat facelift 20/7/2017 ======*/
.maindiv {
    width:100%;
}
.titleTxt {
    border-width:1px;
    margin-bottom:20px;
}
.table-list {
    table-layout: fixed
}
.table-list .buttons{
    margin-top:10px;
}
.table-list .dottTbl td {
    font-size: 14px;
    padding: 5px 5px;
    vertical-align: bottom;
    text-align : {$dir};
}
.table-list .dottTbl td:nth-child(7) {
    width:220px;
}
.table-list .dottTbl td:last-child {
    width: 180px;
}


.dottTbl td {
    font-size: 22px;
    padding: 15px 20px;
    text-align : {$dir};
}
.dottTblS {

}
.dottTbls img {
    display: block;
    margin: 0 auto;
}
.gallery_media_items {
    max-width:1200px;
    margin:0 auto;
}
.gallery_media_items img {
    display:block;
    width:300px;
    height:150px;
    margin: 0;
    padding: 0;
}
.dottTblS input, input[type=text], #search_frm td input, select {
    padding: 7px 10px;
    font-size: 16px;
    border-radius: 4px;
    color:black;
    height:auto;
    margin-right: 10px;
}

select {
//    -webkit-appearance:none;
}
.dottTblS .buttons, .buttons {
    display: inline-block;
    padding: 6px 12px;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
    height: auto;
    color: #333;
    background-color: #fff;
    border-color: #ccc;
    min-width: 70px;
    margin: 0 5px;
    vertical-align: top;
}

.dottTblS .buttons:first-child, .buttons:first-child {
    margin-left:0;
}

.dottTblS .buttons:hover, .buttons:hover {
    color: #333;
    background-color: #e6e6e6;
    border-color: #adadad;
    text-shadow: none;
}
.dottTblS .buttons:active, .buttons:active {
    color: #333;
    background-color: #d4d4d4;
    border-color: #8c8c8c;
    text-shadow: none;
}

.dottTblS .buttons.red {
    color: #fff;
    background-color: #d9534f;
    border-color: #d43f3a;
}
.dottTblS .buttons.red:hover {
    color: #fff;
    background-color: #c9302c;
    border-color: #ac2925;
    text-shadow: none;
}
.dottTblS .buttons.red:active {
    color: #fff;
    background-color: #ac2925;
    border-color: #761c19;
    text-shadow: none;
}

#search_frm {
    padding: 30px 5px;
    background: #F5F5F5;
    border-radius: 5px;
}

#search_frm td input.buttons {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent !important;
    border-radius: 4px;
    height: auto;
    color: #333;
    background-color: #fff;
    border-color: #ccc;
    color: #fff;
    background-color: #337ab7;
    border-color: #2e6da4;
    min-width: 70px;
}
#search_frm td {
    padding: 0;
    vertical-align: top;
    width: 190px;
    padding-right: 20px;
}
#search_frm td:last-child {
        vertical-align: inherit;
}
#search_frm td input, #search_frm td select {
    width:100%;
}
#search_frm td input.buttons:hover {
    color: #fff;
    background-color: #286090;
    border-color: #204d74;
    background-image:none;
    text-shadow: none;
}
#search_frm td input.buttons:active {
    color: #fff;
    background-color: #286090;
    border-color: #204d74;
    background-image:none;
    text-shadow: none;
}

#search_frm img{
    margin: 0 10px;
}
.dottTblS small {
    font-size:14px;
}
input + img.pointer {
    cursor: pointer;
    vertical-align: bottom;
    margin: 0 10px 0 0px;
}


</style>

SCR;

$_salat_icon = "<img src=\"../images/item_" . (int)$_SESSION['salatLangID'] . ".gif\" width=\"4\" height=\"7\" border=\"0\" align=\"absmiddle\" />&nbsp;";

?>
