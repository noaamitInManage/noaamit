<?php

$fbConf = array(
	'appId'=>'189377484465976'
);

$fbPermissions = array('email','user_likes','user_groups','publish_stream');

?>
<div id="fb-root"></div>
<script type="text/javascript">
	window.fbAsyncInit = function() {
		FB.init({ appId:'<?=$fbConf['appId'];?>', status:true, cookie:true, xfbml:true, oauth:true }); 
		
		FB.Event.subscribe('edge.create', function(response) {
			$.post("/_ajax/ajax.index.php",{'file':'user_service','action':'like_url','url':top.location.href,'mdlId':'<?=$mdlID;?>','objId':'<?=$objID;?>'});
		});
		
		FB.Event.subscribe('edge.remove', function(response) {
			$.post("/_ajax/ajax.index.php",{'file':'user_service','action':'unlike_url','url':top.location.href,'mdlId':'<?=$mdlID;?>','objId':'<?=$objID;?>'});
		});
		
	};
	
	function fbConnect(callback) {
		FB.login(function(response) {
			callback.call(FB,response);
    	}, {scope: '<?=implode(",",$fbPermissions);?>'});
	}

	(function(d, s, id) {
		  var js, fjs = d.getElementsByTagName(s)[0];
		  if (d.getElementById(id)) return;
		  js = d.createElement(s); js.id = id;
		  js.src = "//connect.facebook.net/en_US/all.js";
		  fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>