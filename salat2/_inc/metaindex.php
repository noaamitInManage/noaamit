<?php

/*
Name:		meta_checkURLAlias
Date:		8-4-2008
Author:		Ron Bentata, inManage
Purpose:	The following function should be called from the index.php file at the top of the page
Description:	The function assumes there is a db connection openned.
		It will take the 
Input:		none.
Output:		An array of mdlID and innerID
*/

define("meta_SitePages_ModuleID",	"4");
define("meta_Home_MdlName",			"home");
define("meta_DevMode_URISep", 		"-");
define("meta_DevMode", 				false); // change to false when site is live

function meta_checkURLAlias($uri=''){
	
	// GET URL TITLE ALIAS
	
	
	if($uri=='') {
		if(strpos($_SERVER['REQUEST_URI'],0)==0) {
			
			$uri = substr($_SERVER['REQUEST_URI'],1);
		} else {
			$uri = $_SERVER['REQUEST_URI'];
		}
		
		
	}
	
	if (strpos($uri,"?")>-1) {
		$uri = substr($uri,0,strpos($uri,"?"));
	}
	
	$uri = urldecode($uri);
	
	if ($uri==''){
		$arr['result'] = '200';
		$arr['module_id'] = 0;
		$arr['inner_id'] = 0;
		return ($arr);
	}
	
	if (meta_DevMode){
		$arr = array();
		$tmp = explode(meta_DevMode_URISep,$uri);
		$arr['result'] = '200';
		$arr['module_id'] = $tmp[0];
		$arr['inner_id'] = $tmp[1];
		return ($arr);
	}
	
	/**
	 * Added by Albert A. June, 23 2010
	 * We have the site's urls in a static array, why not use it? :)
	 */
	global $urlAliasArr,$Db;
	$urlArrKey = array_search($uri, $urlAliasArr);
	if ($urlArrKey) {
		$urlArrKey = explode('-', $urlArrKey);
		return array(
			'result' => 200,
			'module_id' => $urlArrKey[0],
			'inner_id' => $urlArrKey[1],
		);
	}
	/**
	 * end modification
	 */
	
	// SEARCH IN NEW DB
	$query = "SELECT module_id, inner_id 
		    FROM  tb_metatags 
		    WHERE (meta_urlalias = '".$Db->make_escape($uri)."') ";
	$result = $Db->query($query);// or die(mysql_error().$query);
	if ($result && ($result->num_rows)>0){
		$arr = array();
		$arr['result'] = '200';
		$arr['module_id'] = mysqli_result($result,0,0);
		$arr['inner_id'] = mysqli_result($result,0,1);
		return ($arr);
	}
	
	// SEARCH IN HISTORY DB
	$query = "SELECT tb_metatag_history.module_id, tb_metatag_history.inner_id, tb_metatags.meta_urlalias 
		    FROM  tb_metatag_history 
		    	INNER JOIN tb_metatags 
	    			ON (tb_metatags.module_id = tb_metatag_history.module_id AND tb_metatags.inner_id = tb_metatag_history.inner_id) 
		    WHERE (tb_metatag_history.meta_urlalias = '".$Db->make_escape($uri)."') ";
	$result = $Db->query($query);// or die(mysql_error().$query);
	if ($result && ($result->num_rows)>0){
		$arr = array();
		$arr['result'] = '301';
		$arr['module_id'] = mysqli_result($result,0,0);
		$arr['inner_id'] = mysqli_result($result,0,1);
		$arr['href'] = mysqli_result($result,0,2);
		return ($arr);
	}
	
	// ERROR, NO ALIAS FOUND
	$arr['result'] = '400';
	$arr['module_id'] = -1;
	$arr['inner_id'] = -1;
	return ($arr);
	
}
/*function meta_checkURLAlias($uri=''){
	
	// GET URL TITLE ALIAS
	if ($uri==''){
		$uri = substr($_SERVER['REQUEST_URI'],1);
		if ($Qpos = strpos($uri,"?")) $uri = substr($uri,0,$Qpos);
		$uri = urldecode($uri); // if non given, get from url
	}
	
	if ($uri==''){
		$arr['result'] = '200';
		$arr['module_id'] = 0;
		$arr['inner_id'] = 0;
		return ($arr);
	}
	
	if (meta_DevMode){
		$arr = array();
		$tmp = explode(meta_DevMode_URISep,$uri);
		$arr['result'] = '200';
		$arr['module_id'] = $tmp[0];
		$arr['inner_id'] = $tmp[1];
		return ($arr);
	}
	

	global $urlAliasArr;
	$urlArrKey = array_search($uri, $urlAliasArr);
	if ($urlArrKey) {
		$urlArrKey = explode('-', $urlArrKey);
		return array(
			'result' => 200,
			'module_id' => $urlArrKey[0],
			'inner_id' => $urlArrKey[1],
		);
	}

	
	// SEARCH IN NEW DB
	$query = "SELECT module_id, inner_id 
		    FROM  tb_metatags 
		    WHERE (meta_urlalias = '".$Db->make_escape($uri)."') ";
	$result = mysql_query($query);// or die(mysql_error().$query);
	if ($result && mysql_num_rows($result)>0){
		$arr = array();
		$arr['result'] = '200';
		$arr['module_id'] = mysqli_result($result,0,0);
		$arr['inner_id'] = mysqli_result($result,0,1);
		return ($arr);
	}
	
	// SEARCH IN HISTORY DB
	$query = "SELECT tb_metatag_history.module_id, tb_metatag_history.inner_id, tb_metatags.meta_urlalias 
		    FROM  tb_metatag_history 
		    	INNER JOIN tb_metatags 
	    			ON (tb_metatags.module_id = tb_metatag_history.module_id AND tb_metatags.inner_id = tb_metatag_history.inner_id) 
		    WHERE (tb_metatag_history.meta_urlalias = '".$Db->make_escape($uri)."') ";
	$result = mysql_query($query);// or die(mysql_error().$query);
	if ($result && mysql_num_rows($result)>0){
		$arr = array();
		$arr['result'] = '301';
		$arr['module_id'] = mysqli_result($result,0,0);
		$arr['inner_id'] = mysqli_result($result,0,1);
		$arr['href'] = mysqli_result($result,0,2);
		return ($arr);
	}
	
	// ERROR, NO ALIAS FOUND
	$arr['result'] = '400';
	$arr['module_id'] = -1;
	$arr['inner_id'] = -1;
	return ($arr);
	
}*/

// supply module id and inner id to get the module name from the static module names static array
function getModuleName($mdlID=0, $innerID=0){
	GLOBAL $moduleNameArr; // static array for module names
	if ($mdlID == 0) return meta_Home_MdlName; // main page for given module
	else if ($mdlID == meta_SitePages_ModuleID) return $moduleNameArr[$innerID][1]; // page by inner id
	else {
		if(isset($moduleNameArr[$mdlID])) {
			return isset($moduleNameArr[$mdlID][0]) ?$moduleNameArr[$mdlID][0] : $moduleNameArr[$mdlID][1]; // page by module id‬
		}
	}
}

// supply module id and inner id (array of ids, even when fetching one id) - to get the url alias from the db
function getMetaLink($mdlID=0, $innerIDs=0){
	if (!($innerIDs)) $innerIDs = array(0);
	
	/*defines whether a single link is requested or an list of links	*/
	if (is_array($innerIDs)) {
		$returnArray =true;
	}else{
		$innerIDs = array($innerIDs);
		$returnArray = false;
	}
	
	if (meta_DevMode){
		$rows = array();
		foreach ($innerIDs as $id){
			$rows[$id] = $mdlID.meta_DevMode_URISep.$id; // ie: 13-44
		}
		$rows =($returnArray? $rows : current($rows));
		return ($rows);
	}
	
	// get url alias by module id and inner id
	GLOBAL $urlAliasArr;
		foreach ($innerIDs as $id){
			@$rows[$id] = $urlAliasArr[$mdlID."-".$id];
		}
	
	$rows =($returnArray? $rows : current($rows));
	return ($rows);
	
}

?>