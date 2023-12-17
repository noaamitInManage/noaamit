<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);
include("_inc/config.inc.php");

// load lang texts
include($_project_server_path.$_salat_path."_static/langs/".$_SESSION['salatLangID'] .".inc.php");
if($_salat_new_show){
	include($_SERVER['DOCUMENT_ROOT'].'/salat2/_static/new_menus/sys_user_array-'.$_SESSION['salatUserID'].'.inc.php');
}

?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_LANG['salat_dir'];?>">
<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
	<meta name="robots" content="noindex,nofollow">
    <link rel="StyleSheet" href="_public/main.css" type="text/css">
    <link rel="StyleSheet" href="_public/main_page.css" type="text/css"> <!--homepage style-->
	<script type="text/javascript" src="_public/jquery1.8.min.js"></script>
	<SCRIPT LANGUAGE="JavaScript">if (window.parent==window) location.href = 'frames.php'; </SCRIPT>
	<?php echo $_salat_style;?>
</head>
<body>
    <? include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/module_menu.inc.php');?>
	<?if($_salat_new_show){?>
			<div style="color:<?php echo $_color_text;?>;" align="center">
				<br><br><br>
				<span style="color:<?php echo $_color_dark;?>;font-size:26px;font-weight:bold;"><?php echo $_LANG['main_welcome'];?> <?php echo $_SESSION['salatUserUName'];?></span>
				<br><br><br>
					<span style="color:black;font-size:19px;font-weight:bold;"><?php echo $_LANG['main_options'];?></span>
			</div>
			<div id="container">
				<div id="cssmenu">
					<div id="search">
						<img src="_public/main_page_images/icons/search_search.png" alt=""/>
						<input type="text" name="search" placeholder="חפש" />
					</div>
					<ul>
						<? foreach($modulesArr as $key=>$value): ?>
							<li><a href=""><span><?echo $_LANG["tree_{$key}"]; ?></span></a>
								<? if(is_array($value)){ ?>
									<ul>
										<? foreach($value as $module_id=>$moduleArr) { ?>
											<?
											$filter = array('.php','/','.');
											//$icon_class="icon_".($module_id%420).'.png';
											$href=(($moduleArr['page']) ? 'href="/salat2/'.$key.'/'.$moduleArr['page'].'"' : 'href="#" onmouseover="this.style.cursor=\'default\';" onclick="javascript:  return false;"');
											?>

											<? if(isset($moduleArr['items']) && is_array($moduleArr['items'])){ ?>
												<li class="has-sub"><a <? echo $href; ?>><?echo $moduleArr['title']; ?></a>
													<ul>
														<? foreach($moduleArr['items'] as $subkey=>$subvalue): ?>
															<? $href=(($moduleArr['page']) ? 'href="/salat2/'.$key.'/'.$subvalue['page'].'"' : 'href="#" onmouseover="this.style.cursor=\'default\';" onclick="javascript:  return false;"'); ?>
															<li class=""><a <?echo $href; ?>><?echo $subvalue['title']; ?></a></li>
														<? endforeach; ?>
													</ul>
												</li>
											<? }else{ ?>
												<li><a <? echo $href; ?>><?echo $moduleArr['title']; ?></a></li>
											<? } ?>
										<? } ?>
									</ul>
								<? } ?>
							</li>
						<? endforeach; ?>
					</ul>
				</div>

				<div id="main_content">
					<? foreach($modulesArr as $key=>$value): ?>
						<ul class="category">
							<div class="category_title">
								<img class="title_arrow_down" src="_public/main_page_images/title-arrow-down.png" alt=""/>
								<?echo $_LANG["tree_{$key}"]; ?>
							</div>

							<? if(is_array($value)){ ?>
								<? foreach($value as $module_id=>$moduleArr): ?>
									<?
									$filter = array('.php','/','.');
									// $icon_class = str_replace($filter,'', $moduleArr['page']).'_70x67.png';
									$icon_class="icon_".($module_id%62).'.png';
									//$icon_class = str_replace('/','',str_replace('.php','',$moduleArr['page'])).'_70x67.png';
									if(isset($moduleArr['icon_id'])&&$moduleArr['icon_id']){
										if(!class_exists('mediaManager')){
											include($_SERVER['DOCUMENT_ROOT'].'/_inc/class/module/mediaManager.class.inc.php');
										}
										$Media=new mediaManager($moduleArr['icon_id']);
										$icon_src=$Media->path;
									}else{
										$icon_class="icon_".($module_id%62).'.png';
										//	$icon_src='/salat2/_public/icons/'.$icon_class;
										$icon_src='/salat2/images/large/'.$icon_class;
									}

									$href=(($moduleArr['page']) ? 'href="/salat2/'.$key.'/'.$moduleArr['page'].'"' : 'href="#" onmouseover="this.style.cursor=\'default\';" onclick="javascript:  return false;"');
									?>
									<li><a <? echo $href; ?>><img src="<?echo $icon_src;?>" class="icon_menu"/><div class="menu_item_title"> <?echo $moduleArr['title']; ?></div></a></li>
									<? if(isset($moduleArr['items']) && is_array($moduleArr['items'])){ ?>
										<? foreach($moduleArr['items'] as $subkey=>$subvalue): ?>

                                            <?if(isset($subvalue['icon_id'])&&$subvalue['icon_id']){
                                                if(!class_exists('mediaManager')){
                                                    include($_SERVER['DOCUMENT_ROOT'].'/_inc/class/module/mediaManager.class.inc.php');
                                                }
                                                $Media=new mediaManager($subvalue['icon_id']);
                                                $icon_src=$Media->path;
                                            }else{
                                                $icon_class="icon_".($module_id%62).'.png';
                                                $icon_src='/salat2/images/large/'.$icon_class;
                                            }?>

											<? $href=(($moduleArr['page']) ? 'href="/salat2/'.$key.'/'.$subvalue['page'].'"' : 'href="#" onmouseover="this.style.cursor=\'default\';" onclick="javascript:  return false;"'); ?>
											<li><a <?echo $href; ?>><img src="<?echo $icon_src;?>" class="icon_menu"/><div class="menu_item_title"> <?echo $subvalue['title']; ?></div></a></li>
										<? endforeach; ?>
									<? } ?>
								<? endforeach; ?>
							<? } ?>
						</ul>
					<? endforeach; ?>
				</div>
			</div>
			<div id="footer">
				<br><br><br>
				<img src="<?=_project_logo_big;?>" alt="" title="" />
				<br><br><br>
			</div>
		</body>
		</html>
		<script type="text/javascript">
			$(document).ready(function(){
				$('#cssmenu > ul > li:has(ul)').addClass("has-sub");

				$('#cssmenu > ul > li > a').click(function() {
					var checkElement = $(this).next();
					$('#cssmenu li').removeClass('active');
					$(this).closest('li').addClass('active');

					if((checkElement.is('ul')) && (checkElement.is(':visible'))) {
						$(this).closest('li').removeClass('active');
						checkElement.slideUp('normal');
					}

					if((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
						$('#cssmenu ul ul:visible').slideUp('normal');
						checkElement.slideDown('normal');
					}

					if (checkElement.is('ul')) {
						return false;
					} else {
						return true;
					}
				});

				// Filter search results
				$(document).on('keyup','#search input[name="search"]',function(){
					var search_string = $(this).val();

					delay(function(){
						if(search_string == '')
						{
							$('.menu_item_title').closest('li').fadeIn();
							$('#cssmenu ul ul li a').closest('li').fadeIn();
						}
						else
						{
							$('.menu_item_title').closest('li').fadeOut();
							$('#cssmenu ul ul li a').closest('li').fadeOut();
							$('.menu_item_title:contains('+search_string+')').closest('li').fadeIn();
							$('#cssmenu ul ul li a:contains('+search_string+')').closest('li').fadeIn();
						}
					}, 500 );
				});

				var delay = (function(){
					var timer = 0;
					return function(callback, ms){
						clearTimeout (timer);
						timer = setTimeout(callback, ms);
					};
				})();

			});
		</script>
		<?}else{?>
			<div style="color:<?php echo $_color_text;?>;" align="center">
				<br><br><br>
				<span style="color:<?php echo $_color_dark;?>;font-size:26px;font-weight:bold;"><?php echo $_LANG['main_welcome'];?> <?php echo $_SESSION['salatUserUName'];?></span>
				<br><br><br>
				<img src="<?=_project_logo_big;?>" alt="" title="" />
				<br><br><br>
				<span style="color:black;font-size:19px;font-weight:bold;"><?php echo $_LANG['main_options'];?></span>
			</div>
		</body>
		</html>
		<?}?>

