<?

  class  mobileHtml{
	
	public static function draw_category_item($category_id,$post_id,$valueArr,$iterator,$search_posts){
		 global  $$data_transition;
	     $class_name=($search_posts==0) ? 'postManager' : 'searchPostManager';$distance='';
		 $Post = new $class_name($post_id);

		 if(!$Post->id){
		 	return null;
		 }
		 
		 if(get_top_parent_cat($Post->sub_category_id)==19){
		  	$Post->content=reset(unserialize(base64_decode($Post->content)));
		 }
		 foreach ($Post->picturesArr AS $k=>$v){ 
			 $pictureLink='http://www.citywall.co.il/_media/posts/'.get_item_dir($v).'/'.$v.'_thumb.jpg';
			 list($w,$h)=getimagesize($_SERVER['DOCUMENT_ROOT'].$pictureLink);	
		     $pictureLink=file_exists($_SERVER['DOCUMENT_ROOT'].'/_media/posts/'.get_item_dir($v).'/'.$v.'_thumb.jpg') ? $pictureLink : 'http://www.citywall.co.il/_media/posts/1/0_thumb.jpg';
		     break;
		}
		$pic_width=86;
		$pic_height=56;
		if($search_posts){
			$pictureLink=$Post->user_picture;
			$pic_width=54;
		} 
		$mdl=$search_posts ? 'search_post' :'post';
		$post_link=Seo::getUrl( $mdl,$Post->id);

		switch ($Post->msg_type){
			case 3: //
				$theme='orange';
				break;
			
			case 2:// pink
				$theme='e';
				break;
			case 1: //reg
			default:
				global $data_theme;
				$theme=$data_theme;
				break;
			
		}
		$valueArr['title']= (isset($valueArr['title']) && ($valueArr['title'])) ?htmlspecialchars($valueArr['title']) : $Post->title;
		$Post->content=strip_tags($Post->content);
		if($search_posts){
			$price =NIS.$Post->price_from .' - '.NIS.$Post->price_to;
		}else{
			$price= ($Post->price) ? NIS.$Post->price : '';
		 	if(get_top_parent_cat($Post->sub_category_id)==19){
		 		$price=get_category_title($Post->sub_category_id);
			}
		}
		 if( (isset($Post->no_middle_man)) && ($Post->no_middle_man)){
			$middle_man='&nbsp;'.'<span class="redred">'.'לא מעוניין בתיווך'.'</span>';
		 }
		 else {
		 	$middle_man='';
		 }
		if (isset($valueArr['distance']) && ($valueArr['distance'])){
			
			$distance = (number_format($valueArr['distance'],2)*1000) ." מ'" ;
		}  

		return $html=<<<HTML
		
			<li type="{$mdl}" data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="div" data-icon="false" data-iconpos="right" data-theme="c" class="full_li ui-btn ui-btn-icon-right ui-li ui-li-has-alt ui-li-has-thumb ui-first-child ui-btn-up-{$theme} category_li" rel="{$iterator}" post_id="{$Post->id}">
				<a href="#/{$post_link}" class="abs_link"  data-transition="{$data_transition}"></a>
				<div class="ui-btn-inner ui-li ui-li-has-alt z-master">
					<div class="ui-btn-text item_text">
						<a href="#/{$post_link}" class="ui-link-inherit link1 pic_wrapper"  data-transition="{$data_transition}">
							<img src="{$pictureLink}" class=" r_img {$class_name}" width="{$pic_width}" height="{$pic_height}"  />
						</a>
							<div class="text_wrapper" >						
								<h3 class="ui-li-heading"><a href="#/{$post_link}">{$Post->title}</a></h3>
								<p class="ui-li-desc">{$Post->content} </p>
								<p class="ui-li-price">{$price} {$middle_man}</p>
							</div>	
					
					</div>
				</div>							
				<div class="distnace">
					{$distance}
				</div>
				<a href="#/{$post_link}" data-rel="popup" data-position-to="window" data-transition="{$data_transition}" title="{$valueArr['title']}>" class="ui-li-link-alt-left ui-btn z-master2 ui-btn-up-{$theme} ui-btn-icon-notext post_link_holder" data-corners="false" data-shadow="false" data-iconshadow="true" data-wrapperels="span" data-icon="false" data-iconpos="notext" data-theme="c" aria-haspopup="true" aria-owns="#/{$post_link}">
					<span class="ui-btn-inner">
						<span class="ui-btn-text"></span>
					</span>
				</a>
				<div class="eraserBtn" onClick="javascript: delete_item('{$Post->id}',this); return false;" ></div>
				<div class="deletItemBtn x-button" onClick="javascript: del('{$Post->id}',{$search_posts}); return false; " ><span>מחק</span></div>
			</li>	
HTML;

}
	  

    public static function draw_more_items_btn($objID,$type='offers'){
		global $total_items;
		
		return <<<HTML
			<li data-corners="false" data-ajax="false" data-shadow="true" data-iconshadow="true" data-wrapperels="div" cat_id="{$objID}" data-icon="false" data-iconpos="right" data-theme="c" class="full_li ui-btn ui-btn-icon-right ui-li ui-li-has-alt ui-li-has-thumb ui-first-child ui-btn-up-c category_li more_item_btn pointer " rel="0" href="#" onclick="javascript:load_more_items('{$objID}',$(this)); event.preventDefault(); return false;" rel2="{$type}" id="more_btn" total="{$total_items}" current="0" >
				<div class="ui-btn-inner ui-li ui-li-has-alt">
					<div class="ui-btn-text item_text">
							<div class="text_wrapper center">						
								<h3 class="ui-li-heading">לחץ כאן לטעינת מודעות נוספות</h3>
								<p class="ui-li-desc"></p>
							</div>						
					</div>
				</div>
			</li>	
	
	
HTML;

	}

}




?>