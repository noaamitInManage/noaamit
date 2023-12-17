<?php
/**
 * @name: 		index.js.php
 * @author: 	Albert Harounian
 * @since:		January 06 2008
 * @desc:		Makes static file for all js files needed for module
 * @revision	November 19 2009
 */

$basicStaticFile = array(
		'filename' => "{$_project_server_path}/_static/js/basic.static.js", 
		'lastUpdate' => '',
);

$moduleStaticFile = array(
		'filename' => "{$_project_server_path}/_static/js/{$mdlName}.static.js", 
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
	if(isset($basicJSArr)) {
		foreach ($basicJSArr as $jsFile) {
			$jsFile_lastUpdate = filemtime($_project_server_path . $_js_path . $jsFile . ".js");
			if ($basicStaticFile['lastUpdate'] < $jsFile_lastUpdate) {
				@unlink($basicStaticFile['filename']);
				makeMediaStaticFile($_project_server_path . $_js_path, $basicJSArr, "js", $basicStaticFile['filename'], $xCompressedJSArr);
				break;
			}
		}
	}
	if(isset($modulesJSArr[$mdlName])) {
		foreach ($modulesJSArr[$mdlName] as $jsFile) {
			$jsFile_lastUpdate = filemtime($_project_server_path . $_js_path . $jsFile . ".js");
			if ($moduleStaticFile['lastUpdate'] < $jsFile_lastUpdate) {
				@unlink($moduleStaticFile['filename']);
				makeMediaStaticFile($_project_server_path . $_js_path, $modulesJSArr[$mdlName], "js", $moduleStaticFile['filename'], $xCompressedJSArr);
				break;
			}
		}
	}
}

?>