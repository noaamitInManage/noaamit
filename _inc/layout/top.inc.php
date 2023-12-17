<?php
?>
<div id="headerWrapper">
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
			<a href="<?=$Seo->getStaticUrl(1,15)?>"><?=draw_btn('Get Started','systemHeader_getStarted','btns generalgetStartedBtn');?></a>
		</div><!-- End #loginRow -->
	</div>
</div><!-- End #headerWrapper -->
