<?php

function draw_city_and_area($city_id){
	include($_SERVER['DOCUMENT_ROOT'].'/_static/cities/city-'.$city_id.'.inc.php');//$cityArr
	return get_area_name($cityArr['area_id']). ' - '.$cityArr['title'];
}

function get_area_name($area_id){
	global $areasArr;
	return $areasArr[$area_id];
}

function get_area_id($city_id){
	include($_SERVER['DOCUMENT_ROOT'].'/_static/cities/city-'.$city_id.'.inc.php');//$cityArr
	return $cityArr['area_id'];
}

function get_city_name($city_id){
	include($_SERVER['DOCUMENT_ROOT'].'/_static/cities/city-'.$city_id.'.inc.php');//$cityArr
	return $cityArr['title'];
}

function get_user_picture($user_id=0){
/*
Because of time constraints 
in the future save user image with dir split like /media/post

TODO!!!
*/	
	return $picture_link=(file_exists($_SERVER['DOCUMENT_ROOT'].'/_media/users/'.$user_id.'.jpg')) ? '/_media/users/'.$user_id.'.jpg' : '/_media/images/filler/tab_person.jpg';
}

function draw_breadcrumbs($type='category',$objId){
	global $Seo;

	switch($type){
		
		case 'constractor_post':
			include($_SERVER['DOCUMENT_ROOT'].'/_static/constractor_post/post-'.$objId.'.inc.php');//$constractorPostArr
				$items=array();

				$items[]='<a href="/'.getMetaLink(4,40).'">קבלנים</a>';
				$items[]='<span class="breadcrumbsArrow"></span><a href="/'.Seo::getUrl('constructors_post',$objId).'">
				'.$constractorPostArr['title'].'
				</a>';
				$items=implode('',$items);			

			break;
		case 'category':
		case 'post':
			include($_SERVER['DOCUMENT_ROOT'].'/_static/category/category-'.$objId.'.inc.php');//$categoryArr
			$category_bredcrumbsArr=array();
			while(intval($categoryArr['parent_id'])){
				$category_bredcrumbsArr[]=array(
					"id"=>$categoryArr['id'],
					"title"=>$categoryArr['title'],
					"link"=>$Seo->getUrl('category',$categoryArr['id']),
					"level"=>$categoryArr['level']
				);	
				include($_SERVER['DOCUMENT_ROOT'].'/_static/category/category-'.$categoryArr['parent_id'].'.inc.php');//$categoryArr
			}
			
			if($objId!=$categoryArr['id']){
				$category_bredcrumbsArr[]=array(
					"id"=>$categoryArr['id'],
					"title"=>$categoryArr['title'],
					"link"=>$Seo->getUrl('category',$categoryArr['id']),
					"level"=>$categoryArr['level']
				);		
			}
			$category_bredcrumbsArr=array_reverse($category_bredcrumbsArr);

$first_item=array_shift($category_bredcrumbsArr);
			
$last_item=array_pop($category_bredcrumbsArr);		
$items=array();
$items[]='<a  href="/'.$first_item['link'].'">'.$first_item['title'].'</a>';

foreach ($category_bredcrumbsArr AS $key=>$value){
	$items[]="<span class=\"breadcrumbsArrow\"></span><a href=\"/{$value['link']}\">{$value['title']}</a>";
}
$items[]='<span class="breadcrumbsArrow"></span><a href="/'.$last_item['link'].'">'.$last_item['title'].'</a>';

		$items=implode('',$items);	


			break;
			
			
		case 'profile':
			$items=array();
			$userLink=$Seo->getUrl('profile',$objId);
			include($_SERVER['DOCUMENT_ROOT'].'/_static/users/'.get_item_dir($objId).'/user-'.$objId.'.inc.php');//$userArr;
			$items[]='<a> פרופיל </a>';
			$items[]='<span class="breadcrumbsArrow"></span><a href="/'.$userLink.'">'.$userArr['title'].'</a>';			
			
			
		$items=implode('',$items);				
			break;
			
			
			
		case 'constractor':
				$items=array();
				$items[]='<a> מקום לגור </a>';
				$items[]='<span class="breadcrumbsArrow"></span><a href="/'.getMetaLink(4,40).'">קבלנים</a>';
				$items=implode('',$items);					
			break;	
			
		case 'professional_profile':
		case 'buisness_profile':
			$items=array();
			include($_SERVER['DOCUMENT_ROOT'].'/_static/buisness/'.get_item_dir($objId).'/buisness-'.$objId.'.inc.php');//$buisnesArr;
			$items[]='<a> בעל עסק </a>';
			$items[]='<span class="breadcrumbsArrow"></span> <a href="#" onclick="return false;">'.$buisnesArr['title'].'</a>';		
			
			
		$items=implode('',$items);				
			break;			
					
		default:
			
			break;	
	}
	
return <<<HTML
			<div class="click">
				{$items}
			</div>
HTML;

}

function front_pageing($link,$items_per_block,$total_items,$page,$param_name='page',$current_page_class='current',$page_dot=6,$left_arrow_class,$right_arrow_class){
$total_pages=($total_items) ? ceil($total_items/$items_per_block): 0;

$link_prefix = (strstr($link,'?'))? '&': '?';
$start_page=(($l=$page-$page_dot) > 1)? $l: 1;

if($page > 1){
	$a_link=$link_prefix.$param_name.'='.($page-1);
    echo "<a href=\"{$a_link}\" class=\"{$right_arrow_class}\"></a>";
}
$jump_number= (($total_pages-$page) > $page_dot) ? ($page + $page_dot) : 0;
for($i=$start_page;$i<=$total_pages;$i++){
	if($i==$jump_number){
		$i=($total_pages - ceil($page_dot/2));
		echo '<p>...</p>';
		continue;
	}
	$a_link=$link_prefix.$param_name.'='.($i);
	$class=($i==$page) ? $current_page_class : '';
	echo "<a href=\"{$a_link}\" class=\"page page_{$i} {$class}\">{$i}</a>";
}

if($page < $total_pages){
	$a_link=$link_prefix.$param_name.'='.($page+1);
    echo "<a href=\"{$a_link}\" class=\"{$left_arrow_class}\"></a>";
}								
		
}

/**
 * @author : gal zalait
 * @desc : menu
 * @param ((array)||(int)) $compare_ids
 * @param  $current_id
 * @param  $class default is 'on'
 */
function class_is_on($compare_ids,$current_id,$class='on'){
	if(is_array($compare_ids)){
		return (array_search($current_id,$compare_ids)) ? $class : null;
	}else{
		return ($compare_ids==$current_id) ? $class : null;
	}
}

?>