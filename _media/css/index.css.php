<?php
/**
 * @name: 		index.js.php
 * @author: 	Albert Harounian
 * @since:		January 06 2008
 * @desc:		Makes static file for all js files needed for module
 * @revision	November 19 2009
 */


$basicStaticFile = array(
		'filename' => "{$_project_server_path}/_static/css/basic.static.css", 
		'lastUpdate' => '',
);

$moduleStaticFile = array(
		'filename' => "{$_project_server_path}/_static/css/{$mdlName}.static.css", 
		'lastUpdate' => '',
);

if($caching_isOn){
	if(file_exists($basicStaticFile['filename'])){
		$basicStaticFile['lastUpdate'] = filemtime($basicStaticFile['filename']);
	}else{
		$basicStaticFile['lastUpdate'] = 0;
	}
	if(file_exists($moduleStaticFile['filename'])){
		$moduleStaticFile['lastUpdate'] = filemtime($moduleStaticFile['filename']);
	}else{
		$moduleStaticFile['lastUpdate'] = 0;
	}
	if(isset($basicCSSArr)){
		foreach ($basicCSSArr as $cssFile){
			$cssFile_lastUpdate = filemtime($_project_server_path.$_css_path.$cssFile.".css");
			if($basicStaticFile['lastUpdate'] < $cssFile_lastUpdate){
				@unlink($basicStaticFile['filename']);
				makeMediaStaticFile($_project_server_path.$_css_path, $basicCSSArr, "css", $basicStaticFile['filename']);
				break;
			}
		}
	}
	if(isset($modulesCSSArr[$mdlName])){
		foreach ($modulesCSSArr[$mdlName] as $cssFile){
			if(file_exists($_project_server_path.$_css_path.$cssFile.".css")){
				$cssFile_lastUpdate = filemtime($_project_server_path.$_css_path.$cssFile.".css");
			}
			if($modulesCSSArr['lastUpdate'] < $cssFile_lastUpdate){
				@unlink($moduleStaticFile['filename']);
				makeMediaStaticFile($_project_server_path.$_css_path, $modulesCSSArr[$mdlName], "css", $moduleStaticFile['filename']);
				break;
			}
		}
	}
}

?>