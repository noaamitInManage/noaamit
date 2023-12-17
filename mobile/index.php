<?
include($_SERVER['DOCUMENT_ROOT'].'/mobile/_inc/Mobile_Detect_Viewport.php');
include($_SERVER['DOCUMENT_ROOT'].'/mobile/restrict.php'); 
include($_SERVER['DOCUMENT_ROOT'].'/mobile/_inc/modules.array.inc.php'); 
include($_SERVER['DOCUMENT_ROOT'].'/mobile/_inc/mobile.config.inc.php');
error_reporting(E_ALL);
ini_set('display_errors', '0');
$page_url='http://'.$_SERVER['HTTP_HOST'].urldecode($_SERVER['REQUEST_URI']);
?>
<!DOCTYPE html> 
<html> 
	<head> 
	<title><?=$_SERVER['HTTP_HOST'];?> - קפה לנדוור </title> 
	<?=$viewport?>
	<? if($ua = strtolower($_SERVER['HTTP_USER_AGENT'])){
		if(stripos($ua,'android') !== false) { // && stripos($ua,'mobile') !== false) {
//		  exit();
		}
	}
?> 
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />	
	<meta content="width=device-width, minimum-scale=1, maximum-scale=1" name="viewport">
<!--	<link rel="stylesheet" href="http://code.jquery.com/mobile/<?=$jquery_mobile_version;?>/jquery.mobile-<?=$jquery_mobile_version;?>.min.css" />-->
    <!--<link rel="stylesheet" href="http://code.jquery.com/mobile/1.3.0/jquery.mobile.structure-1.3.0.min.css" />-->
	<link rel="stylesheet" href="http://code.jquery.com/mobile/<?=$jquery_mobile_version;?>/jquery.mobile-<?=$jquery_mobile_version;?>.css" />	
    <link rel="stylesheet" href="/mobile/media/css/custom.css?v=<?=$version;?>" />	
    <link rel="stylesheet" href="/mobile/media/css/images.css?v=<?=$version;?>" />	
<!--	<link rel="stylesheet" href="/mobile/media/css/jquery_mobile.rtl.css" />-->
	<script type="text/javascript" src="http://code.jquery.com/jquery-<?=$jquery_version;?>.min.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/mobile/<?=$jquery_mobile_version;?>/jquery.mobile-<?=$jquery_mobile_version;?>.min.js"></script>	
<!--	<link rel="shortcut icon" href="/_media/images/favicon.ico">	-->
	<script src="/mobile/media/js/custom.js?v=<?=time();?>"></script>
	<? include($_SERVER['DOCUMENT_ROOT'].'/_inc/google_analytics.inc.php');?>
<script type="text/javascript">

// jqm.page.params.js - version 0.1
// Copyright (c) 2011, Kin Blas
// All rights reserved.
// 
// Redistribution and use in source and binary forms, with or without
// modification, are permitted provided that the following conditions are met:
//     * Redistributions of source code must retain the above copyright
//       notice, this list of conditions and the following disclaimer.
//     * Redistributions in binary form must reproduce the above copyright
//       notice, this list of conditions and the following disclaimer in the
//       documentation and/or other materials provided with the distribution.
//     * Neither the name of the <organization> nor the
//       names of its contributors may be used to endorse or promote products
//       derived from this software without specific prior written permission.
// 
// THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
// ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
// WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
// DISCLAIMED. IN NO EVENT SHALL <COPYRIGHT HOLDER> BE LIABLE FOR ANY
// DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
// (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
// LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
// ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
// (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
// SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

(function( $, window, undefined ) {

// Given a query string, convert all the name/value pairs
// into a property/value object. If a name appears more than
// once in a query string, the value is automatically turned
// into an array.
function queryStringToObject( qstr )
{
	var result = {},
		nvPairs = ( ( qstr || "" ).replace( /^\?/, "" ).split( /&/ ) ),
		i, pair, n, v;

	for ( i = 0; i < nvPairs.length; i++ ) {
		var pstr = nvPairs[ i ];
		if ( pstr ) {
			pair = pstr.split( /=/ );
			n = pair[ 0 ];
			v = pair[ 1 ];
			if ( result[ n ] === undefined ) {
				result[ n ] = v;
			} else {
				if ( typeof result[ n ] !== "object" ) {
					result[ n ] = [ result[ n ] ];
				}
				result[ n ].push( v );
			}
		}
	}
	return result;
}

// The idea here is to listen for any pagebeforechange notifications from
// jQuery Mobile, and then muck with the toPage and options so that query
// params can be passed to embedded/internal pages. So for example, if a
// changePage() request for a URL like:
//
//    http://mycompany.com/myapp/#page-1?foo=1&bar=2
//
// is made, the page that will actually get shown is:
//
//    http://mycompany.com/myapp/#page-1
//
// The browser's location will still be updated to show the original URL.
// The query params for the embedded page are also added as a property/value
// object on the options object. You can access it from your page notifications
// via data.options.pageData.
$( document ).bind( "pagebeforechange", function( e, data ) {

	// We only want to handle the case where we are being asked
	// to go to a page by URL, and only if that URL is referring
	// to an internal page by id.

	if ( typeof data.toPage === "string" ) {
		
		//window.location.hash(decodeURIComponent(window.location.href));
		var u = $.mobile.path.parseUrl( decodeURIComponent(data.toPage) );
		
		if ( $.mobile.path.isEmbeddedPage( u ) ) {

			// The request is for an internal page, if the hash
			// contains query (search) params, strip them off the
			// toPage URL and then set options.dataUrl appropriately
			// so the location.hash shows the originally requested URL
			// that hash the query params in the hash.

			var u2 = $.mobile.path.parseUrl( u.hash.replace( /^#/, "" ) );
			
			if ( u2.search ) {
				if ( !data.options.dataUrl ) {
					data.options.dataUrl = data.toPage;
				}
				data.options.pageData = queryStringToObject( u2.search );
				data.toPage = u.hrefNoHash + "#" + u2.pathname;
			}
		}
	}

});

})( jQuery, window );

$(document).bind("mobileinit", function(){

    $.mobile.pageLoadErrorMessage = '';
   // $.mobile.showPageLoadingMsg=function(){return false;}
 	$.extend($.mobile, {
        pageLoadErrorMessage: ''
    })
    $.mobile.touchOverflowEnabled = true;


    $.mobile.page.prototype.options.domCache = false;
    
/*	$(".city_ul li").live('tap',function(event){
			window.location.href=''+ $(this).find('a').first().attr('href');
			//return false;
	});    
    */
});
</script>
</head> 
<body> 
<script type="text/javascript">
/*<[CDATA[*/
var mdlName='<?=$mdlName;?>';
var mdlID='<?=$mdlID;?>';
var objID='<?=$objID;?>';

/*]]>*/
</script>

	<?include($mobile_path.'modules/'.$mdlName.'.mobile.mdl.php');?>
</body>
</html>