<?php
include_once $_SERVER['DOCUMENT_ROOT'].'/_static/homePageImages.inc.php'; //$homePageImagesArr
$cont['tools_title'] = generalContentManager::get_content(10,'title');
$cont['tools_content'] = generalContentManager::get_content(10,'content');
?>
<div class="homeHeader" id="headerWrapper">
	<span id="headerShade"></span>
	<div class="clearfix" id="headerInner">
		<div id="headerTop">
			<a id="logoWrapper" href="/"><img src="/_media/images/general/header/genesisLogo.png" width="194" height="66" alt="Genesis" /></a>
			<ul id="languageFlags">
				<li>
					<a href="#" class="lang_spanish">spanish</a>
				</li>
				<li>
					<a href="#" class="lang_english">english</a>
				</li>
				<li>
					<a href="#" class="lang_russian">russian</a>
				</li>
				<li class="noBorder">
					<a href="#" class="lang_portuguese">portuguese</a>
				</li>
			</ul>
		</div><!-- End #headerTop -->
		<div id="loginRow">
			<ul>
				<li class="signup">
					<a href="<?=$Seo->getStaticUrl(1,15)?>">register</a>
				</li>
				<li class="login" style="display:none;">
					<a href="#">log in</a>
				</li>
			</ul>
		</div><!-- End #loginRow -->
		<div id="headerText">
			<h2>
				<?=$cont['tools_title']?>
			</h2>
			<?=$cont['tools_content']?>
			<a href="<?=$Seo->getStaticUrl(1,15)?>"><?=draw_btn('Get Started','home_getStarted','btns headerGetStarted');?></a>
		</div>
		<div id="homeHeaderPic">
			<div id="homeCyclewrapper">
				<?foreach($homePageImagesArr as $key => $val){
					$slider_image = new mediaManager($val);?>
					<img src="<?=$slider_image->path?>" alt="<?=$slider_image->alt?>" title="<?=$slider_image->title?>" height="283" width="619" />
				<?}?>
			</div>
			<div class="frame"></div>
		</div>
	</div>
</div><!-- End #headerWrapper -->
