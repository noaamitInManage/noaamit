<?
?>
<div id="div_menu" style="height:50px;text-align:center;background-color:<?=$top_color;?>; margin :-8px -8px 12px -8px; " >
<a href="/" target="_blank" style="float:right;margin-right:30px;margin-top:8px;"></a>
<div class="menu_sep">&nbsp;</div>
<? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_static/menus/sys_user-'.$_SESSION['salatUserID'].'.'.$_SESSION['salatLangID'].'.inc.php');?>
<strong style="color:#FCFCFC; font-size:16px; font-weight:bold;"><?php echo ($_SESSION['salatLangID']==1?"Salat - Content Management System":"סביבה לניהול תוכן - " . $_LANG['title_main']);?></strong>
<!--<input type="button" value="<?php echo $_LANG['exit_button'];?>" onclick="logOut();" style=" float:left;margin-left:65px;border:solid 1px <?php echo $_color_dark;?>;color:<?php echo $_color_text;?>;font-size:13px;width:130px;height:20px;font-weight:bold;background-color:<?php echo $_color_normal;?>;">-->

<ul class="user_logout_group">
  <li class="logoutbtn"><p class="logout_text">
    <img src="/salat2/_public/icons/icon_3.png" class="icon_menu"/>&nbsp&nbsp<?php echo $_SESSION['salatUserUName'];?>&nbsp&nbsp
          <img src="/salat2/_public/icons/icon_367.png" class="icon_menu"/></p>
    <ul class="user_logout_menu">
        <a href="#"  title="Logout"  onclick="logOut();">
           <li style="height:23px;padding-top:5px;"><div  style="font-size:14px;text-align:right;padding-right:6px;">
                   <img src="/salat2/_public/icons/icon_63.png" class="icon_menu"/>&nbsp&nbsp Logout</div>
            </li>
        </a>
    </ul>
  </li>
</ul>

<br />
    <script type="text/javascript">
var inmanage_ip= '<?=(in_array($_SERVER['REMOTE_ADDR'],configManager::$familiar_ipsArr) ?'true': 'false') ;?>';
</script>
<script src="/salat2/_public/timer.js" type="text/javascript"></script>
<SCRIPT LANGUAGE="JavaScript">if (window.parent==window) location.href = 'frames.php'; </SCRIPT>
<script>
	function logOut(){
		if (confirm("<?php echo $_LANG['exit_question'];?>")) top.document.location = '/salat2/login.php?logout=yes&skip_auto_login=1';
	}
</script>
    <script language="javascript">
		$(document).ready(function(){
			/*$('#menu-header li > ul').css('display','none');

			$(document).on('mouseenter','#menu-header li > a',function(){
				$(this).parent().find("ul.submenu").css('display','block');
			}).on("mouseleave" , "#menu-header li" , function(){
					$(this).parent().find("ul.submenu").css('display','none');
				});

			$(document).on('mouseenter','#menu-header ul.submenu li',function(){
				$(this).find("ul.side_menu").css('display','block');
			}).on("mouseleave" , "#menu-header li" , function(){
					$(this).parent().find("ul.side_menu").css('display','none');
				});

			$(document).on('mouseenter','ul.submenu li > a',function(){
				$(this).css('backgroundColor','#0088cc');
			}).on("mouseleave" , "ul.submenu li > a" , function(){
					$(this).css('backgroundColor','none');
			});*/

            $('#menu-header li > a').live('mouseenter',function(){
                $(this).parent().find("ul.submenu").css('display','block');
            });

            $('#menu-header li').live('mouseleave',function(){
                $(this).parent().find("ul.submenu").css('display','none');
            })

            $('#menu-header ul.submenu li').live('mouseenter',function(){
                $(this).find("ul.side_menu").css('display','block');
            });

            $('#menu-header ul.side_menu li').live('mouseenter',function(){
                $(this).find("ul.sub_side_menu").css('display','block');
            });

            $('#menu-header li').live("mouseleave"  , function(){
                $(this).parent().find("ul.side_menu").css('display','none');
            });

            $('#menu-header li').live("mouseleave"  , function(){
                $(this).parent().find("ul.sub_side_menu").css('display','none');
            });
            
            $('ul.submenu li > a').live('mouseenter',function(){
                $(this).parent().addClass('blue_menu_backgr');
                $(this).css('font-weight','bold');
            }).live("mouseleave" , function(){
                $(this).parent().removeClass('blue_menu_backgr');
                $(this).css('font-weight','normal');
            });

		});
	</script>
</div>
<?
?>