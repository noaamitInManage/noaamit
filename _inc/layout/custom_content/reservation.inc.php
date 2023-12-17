<?
$cont['post1_title'] = generalContentManager::get_content(11,'title');
$cont['post1_content'] = generalContentManager::get_content(11,'content');
$cont['post1_images'][0] = generalContentManager::get_content(12,'media');
$cont['post1_images'][1] = generalContentManager::get_content(13,'media');
$cont['post1_images'][2] = generalContentManager::get_content(14,'media');

$cont['post2_title'] = generalContentManager::get_content(15,'title');
$cont['post2_content'] = generalContentManager::get_content(15,'content');
$cont['post2_image'] = generalContentManager::get_content(15,'media');
$cont['post2_tabs'][0]['tab_title'] = lang('tab_1_1');
$cont['post2_tabs'][0]['title'] = generalContentManager::get_content(16,'title');
$cont['post2_tabs'][0]['content'] = generalContentManager::get_content(16,'content');
$cont['post2_tabs'][0]['image'] = generalContentManager::get_content(16,'media');
$cont['post2_tabs'][1]['tab_title'] = lang('tab_1_2');
$cont['post2_tabs'][1]['title'] = generalContentManager::get_content(17,'title');
$cont['post2_tabs'][1]['content'] = generalContentManager::get_content(17,'content');
$cont['post2_tabs'][1]['image'] = generalContentManager::get_content(17,'media');
$cont['post2_tabs'][2]['tab_title'] = lang('tab_1_3');
$cont['post2_tabs'][2]['title'] = generalContentManager::get_content(18,'title');
$cont['post2_tabs'][2]['content'] = generalContentManager::get_content(18,'content');
$cont['post2_tabs'][2]['image'] = generalContentManager::get_content(18,'media');
$cont['post2_tabs'][3]['tab_title'] = lang('tab_1_4');
$cont['post2_tabs'][3]['title'] = generalContentManager::get_content(19,'title');
$cont['post2_tabs'][3]['content'] = generalContentManager::get_content(19,'content');
$cont['post2_tabs'][3]['image'] = generalContentManager::get_content(19,'media');

$cont['post3_title'] = generalContentManager::get_content(20,'title');
$cont['post3_content'] = generalContentManager::get_content(20,'content');
$cont['post3_image'] = generalContentManager::get_content(20,'media');
$cont['post3_tabs'][0]['tab_title'] = lang('tab_2_1');
$cont['post3_tabs'][0]['title'] = generalContentManager::get_content(21,'title');
$cont['post3_tabs'][0]['content'] = generalContentManager::get_content(21,'content');
$cont['post3_tabs'][0]['image'] = generalContentManager::get_content(21,'media');
$cont['post3_tabs'][1]['tab_title'] = lang('tab_2_2');
$cont['post3_tabs'][1]['title'] = generalContentManager::get_content(22,'title');
$cont['post3_tabs'][1]['content'] = generalContentManager::get_content(22,'content');
$cont['post3_tabs'][1]['image'] = generalContentManager::get_content(22,'media');
$cont['post3_tabs'][2]['tab_title'] = lang('tab_2_3');
$cont['post3_tabs'][2]['title'] = generalContentManager::get_content(23,'title');
$cont['post3_tabs'][2]['content'] = generalContentManager::get_content(23,'content');
$cont['post3_tabs'][2]['image'] = generalContentManager::get_content(23,'media');
$cont['post3_tabs'][3]['tab_title'] = lang('tab_2_4');
$cont['post3_tabs'][3]['title'] = generalContentManager::get_content(24,'title');
$cont['post3_tabs'][3]['content'] = generalContentManager::get_content(24,'content');
$cont['post3_tabs'][3]['image'] = generalContentManager::get_content(24,'media');

$cont['post4_title'] = generalContentManager::get_content(25,'title');
$cont['post4_content'] = generalContentManager::get_content(25,'content');
$cont['post4_images'][0] = generalContentManager::get_content(26,'media');
$cont['post4_images'][1] = generalContentManager::get_content(27,'media');
$cont['post4_images'][2] = generalContentManager::get_content(28,'media');

$cont['post5_title'] = generalContentManager::get_content(29,'title');
$cont['post5_content'] = generalContentManager::get_content(29,'content');
$cont['post5_images'][0] = generalContentManager::get_content(30,'media');
$cont['post5_images'][1] = generalContentManager::get_content(31,'media');
$cont['post5_images'][2] = generalContentManager::get_content(32,'media');

?>

<?=$Cont->content?>
<hr />

<h3><?=$cont['post1_title']?></h3>
<?=$cont['post1_content']?>
<div class="thumbRow">
	<?foreach($cont['post1_images'] as $key => $val){?>
		<?if(!empty($val->id)){?>
			<div class="thumbImage <?=((($key+1)%3==0)?'last':'')?>">
				<div class="frame"></div>
				<img src="<?=$val->path?>" alt="<?=$val->alt?>" title="<?=$val->title?>" height="98" width="163" />
			</div>
		<?}
	}?>
</div>
<div class="clearAll"></div>
<hr />

<h3><?=$cont['post2_title']?></h3>
<?=$cont['post2_content']?>
<div class="topImage">
	<div class="frame"></div>
	<img src="<?=$cont['post2_image']->path?>" alt="<?=$cont['post2_image']->alt?>" title="<?=$cont['post2_image']->title?>" height="183" width="536" />
</div>

<? 
$tabs = array();
foreach($cont['post2_tabs'] as $key => $val){
	$tabs[] = array('title'=>$val['tab_title'],
		'content'=>($val['image']->id?
		'<div class="thumbImage inText">
			<div class="frame"></div>
			<img src="'.$val['image']->path.'" alt="'.$val['image']->alt.'" title="'.$val['image']->title.'" height="98" width="163" />
		</div>':'').'<div class="tog-wrap">
						<h4>'.$val['title'].'</h4>'
						//.'<p class="tog-close">Close details</p>'
						.'<div class="tog-cont-short"><p>'.chop_str(strip_tags($val['content']),300).'</p></div>
						<div class="tog-cont">'.$val['content'].'</div>
						<p class="tog-more">See more details</p>
						<p class="tog-close">Close details</p>
					</div>');
}
echo draw_tabs($tabs,'schema_1');
?>
<div class="clearAll"></div>
<hr />

<h3><?=$cont['post3_title']?></h3>
<?=$cont['post3_content']?>
<?if($cont['post3_image']->id){?>
<div class="topImage">
	<div class="frame"></div>
	<img src="<?=$cont['post3_image']->path?>" alt="<?=$cont['post3_image']->alt?>" title="<?=$cont['post3_image']->title?>" height="183" width="536" />
</div>
<?}?>
<?/*
$tabs = array();
foreach($cont['post3_tabs'] as $key => $val){
	$tabs[] = array('title'=>$val['tab_title'],
		'content'=>($val['image']->id?
		'<div class="thumbImage inText">
			<div class="frame"></div>
			<img src="'.$val['image']->path.'" alt="'.$val['image']->alt.'" title="'.$val['image']->title.'" height="98" width="163" />
		</div>':'').'<div class="tog-wrap">
						<h4>'.$val['title'].'</h4>
						<p class="tog-close">Close details</p>
						<div class="tog-cont-short"><p>'.chop_str(strip_tags($val['content']),300).'</p></div>
						<div class="tog-cont">'.$val['content'].'</div>
						<p class="tog-more">See more details</p>
						<p class="tog-close">Close details</p>
}
echo draw_tabs($tabs,'schema_1');
*/?>
<div class="clearAll"></div>
<hr />

<h3><?=$cont['post4_title']?></h3>
<?=$cont['post4_content']?>
<div class="thumbRow">
	<?foreach($cont['post4_images'] as $key => $val){?>
		<?if(!empty($val->id)){?>
			<div class="thumbImage <?=((($key+1)%3==0)?'last':'')?>">
				<div class="frame"></div>
				<img src="<?=$val->path?>" alt="<?=$val->alt?>" title="<?=$val->title?>" height="98" width="163" />
			</div>
		<?}
	}?>
</div>
<div class="clearAll"></div>
<hr />

<h3><?=$cont['post5_title']?></h3>
<?=$cont['post5_content']?>
<div class="thumbRow">
	<?foreach($cont['post5_images'] as $key => $val){?>
		<?if(!empty($val->id)){?>
			<div class="thumbImage <?=((($key+1)%3==0)?'last':'')?>">
				<div class="frame"></div>
				<img src="<?=$val->path?>" alt="<?=$val->alt?>" title="<?=$val->title?>" height="98" width="163" />
			</div>
		<?}
	}?>
</div>

<a href="<?=$Seo->getStaticUrl(1,15)?>"><?=draw_btn('Get Started','getStartedRes','btns mediumBtn floatRight bold');?></a>