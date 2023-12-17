<?php 

function meta_UpdateTags(){
	GLOBAL $_project_server_path, $_salat_path,$module_lang_id,$_Proccess_Has_MultiLangs;
	// given params
	$Db = Database::getInstance();

	$_REQUEST['module_id']=(int)$_REQUEST['module_id'];	
	$_REQUEST['inner_id']=(int)$_REQUEST['inner_id'];
	
	if($_REQUEST['module_id']>=0){
	
		$_REQUEST['meta_urlalias'] = meta_CreateAlias($_REQUEST['meta_urlalias']);
		$_REQUEST['sm_priority'] = floatval($_REQUEST['sm_priority']);
		if ($_REQUEST['module_id']>0){
			if ($_REQUEST['sm_priority']>=1) $_REQUEST['sm_priority'] = '0.99';
			if ($_REQUEST['sm_priority']<=0) $_REQUEST['sm_priority'] = '0.01';
		}else{
			$_REQUEST['sm_priority'] = '1.00';
		}
		
		// check if meta row exists
		$query = "SELECT meta_urlalias 
			    FROM tb_metatags 
			    WHERE (
			    		(lang_id='".$module_lang_id."') AND
			    		(inner_id='".$_REQUEST['inner_id']."') AND 
			    		(module_id='".$_REQUEST['module_id']."') 
			    ) ";

		$result = $Db->query($query);

		
		// validate if already inside
		if ($result && $result->num_rows>0) {
			// update existing
			$meta_row = $Db->get_stream($result);
			if ($meta_row['meta_urlalias'] != $_REQUEST['meta_urlalias']){
				// the url alias is different
				$query = "INSERT INTO tb_metatag_history (id,meta_urlalias,module_id,inner_id) 
					    VALUES (NULL,'".$meta_row['meta_urlalias']."',".$_REQUEST['module_id'].",".$_REQUEST['inner_id'].") ";
				$result = $Db->query($query);
			}
			// whether the url alias is the same or different, we save the new one
			$_REQUEST['noindex'] =($_REQUEST['noindex']==1)? 1 : 0;
			$_REQUEST['canonical'] =($_REQUEST['canonical']) ?: "";
			$query = "UPDATE tb_metatags 
				    SET 
				    	meta_title='".SQLCheck($_REQUEST['meta_title'])."', 
				    	meta_keywords='".SQLCheck($_REQUEST['meta_keywords'])."', 
				    	meta_description='".SQLCheck(strip_tags(str_replace('<br />',' ',$_REQUEST['meta_description'])))."', 
				    	meta_urlalias='".$_REQUEST['meta_urlalias']."', 
				    	sm_priority='".$_REQUEST['sm_priority']."',
				    	noindex='".$_REQUEST['noindex']."',
				    	canonical='".$_REQUEST['canonical']."'
				    WHERE (
				    		(inner_id=".$_REQUEST['inner_id'].") AND 
				    		(module_id=".$_REQUEST['module_id'].") 
				    ) "; 
			
			if($_Proccess_Has_MultiLangs){
			$query = "UPDATE tb_metatags 
				    SET 
				    	meta_title='".SQLCheck($_REQUEST['meta_title'])."', 
				    	meta_keywords='".SQLCheck($_REQUEST['meta_keywords'])."', 
				    	meta_description='".SQLCheck(strip_tags(str_replace('<br />',' ',$_REQUEST['meta_description'])))."', 
				    	meta_urlalias='".$_REQUEST['meta_urlalias']."', 
				    	sm_priority='".$_REQUEST['sm_priority']."',
				    	noindex='".$_REQUEST['noindex']."',
				    	canonical='".$_REQUEST['canonical']."'
				    WHERE (
				    		(inner_id=".$_REQUEST['inner_id'].") AND 
				    		(module_id=".$_REQUEST['module_id'].") AND
				    		`lang_id`='{$module_lang_id}'
				    ) "; 				
			}
			$result = $Db->query($query);
		}else{
			// insert new
			$query="INSERT INTO tb_metatags 
					(
					module_id,
					inner_id,
					meta_title,
					meta_keywords,
					meta_description,
					meta_urlalias,
					noindex,
					canonical
					) 
				  VALUES 
				  	(
				  	".SQLCheck($_REQUEST['module_id'],'int').",
				  	".SQLCheck($_REQUEST['inner_id'],'int').",
				  	'".SQLCheck($_REQUEST['meta_title'])."',
				  	'".SQLCheck($_REQUEST['meta_keywords'])."',
				  	'".SQLCheck(strip_tags($_REQUEST['meta_description']))."',
				  	'".meta_CreateAlias($_REQUEST['meta_urlalias'])."',
				  	'".SQLCheck($_REQUEST['noindex'])."',
				  	'".SQLCheck($_REQUEST['canonical'])."'
				  	) ";
			
			if($_Proccess_Has_MultiLangs){
			$query="INSERT INTO tb_metatags 
					(
					module_id,
					inner_id,
					meta_title,
					meta_keywords,
					meta_description,
					meta_urlalias,
					noindex,
					canonical,
					lang_id
					) 
				  VALUES 
				  	(
				  	".SQLCheck($_REQUEST['module_id'],'int').",
				  	".SQLCheck($_REQUEST['inner_id'],'int').",
				  	'".SQLCheck($_REQUEST['meta_title'])."',
				  	'".SQLCheck($_REQUEST['meta_keywords'])."',
				  	'".SQLCheck(strip_tags($_REQUEST['meta_description']))."',
				  	'".meta_CreateAlias($_REQUEST['meta_urlalias'])."',
				  	'".SQLCheck($_REQUEST['noindex'])."',
				  	'".SQLCheck($_REQUEST['canonical'])."',
				  	'".$module_lang_id."'
				  	) ";				
				
				
			}
			$result = $Db->query($query);
			//print 'module_id='.$_REQUEST['module_id'].' -  inner_id='.$_REQUEST['inner_id'];
		}
	}
	
	$metaTagsArr = array(
		'keywords' => $_REQUEST['meta_keywords'],
		'description' => $_REQUEST['meta_description'],
		'title' => $_REQUEST['meta_title'],
		'canonical' => $_REQUEST['canonical'],
	);
	
	updateStaticFile($metaTagsArr, '/_static/meta-tags/meta-'.$_REQUEST['module_id'].'-'.$_REQUEST['inner_id'].'.inc.php', 'metaTagsArr'); 
	updateStaticFile("SELECT CONCAT(`module_id`,'-',`inner_id`,'-',`lang_id`)
	                     FROM `tb_metatags` 
	                                WHERE `noindex`=1"
	                                   ,'/_static/meta-tags/noindex.inc.php', 'metaTagsNoIndexArr');
	updateStaticLinks();
}

function updateStaticLinks() {
	GLOBAL $_project_server_path, $_salat_path, $_static_path;
	$query = "SELECT CONCAT(module_id,'-', inner_id,'-',`lang_id`) AS arr_key, meta_urlalias FROM tb_metatags ORDER BY module_id";
	updateStaticFile($query, '/_static/links.inc.php', 'urlAliasArr', 'arr_key', true, true, true, false, false ); // Albert style
}

function meta_CreateAlias($str=''){
	$str = str_replace(" ","_",$str);
	$str = preg_replace("/\]\[\~!\\@#\$%\^&\*\)\(\+\}\{|\>\<\?\":\`='/","",$str); // remove all non-valid characters
	return ($str);
}

function meta_isURLAliasDup($urlalias='',$module_id=0,$inner_id=0){
	$Db = Database::getInstance();

	if ($urlalias=='') $urlalias = $_REQUEST['meta_urlalias'];
	if ($module_id==0) $module_id = (int)$_REQUEST['module_id'];
	if ($inner_id==0) $inner_id = (int)$_REQUEST['inner_id'];
	if ($urlalias=='') return (false);
	
	$query = "SELECT id 
		    FROM tb_metatags 
		    WHERE (
		    		(meta_urlalias='".$urlalias."') AND 
		    		(
		    			(inner_id != $inner_id) OR 
			    		(module_id != $module_id) 
			    	)
		    ) ";
	$result = $Db->query($query);
	return $result->num_rows;
}

function meta_DeleteTags(){
	GLOBAL $_project_server_path, $_salat_path, $_ProcessID;
	// given params
	$Db = Database::getInstance();

	$module_id = $_ProcessID;
	$inner_id = (int)$_REQUEST['id'];
	// remove db row
	$query = "DELETE FROM tb_metatags WHERE (module_id = ".$module_id." AND inner_id = ".$inner_id.") ";
	$Db->query($query);

	$query = "DELETE FROM tb_metatag_history WHERE (module_id = ".$module_id." AND inner_id = ".$inner_id.") ";
	$Db->query($query);
	// remove static file
	@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/meta-tags/meta-'.$module_id.'-'.$inner_id.'.inc.php');
	// return result
	return (true);
}
		

?>
