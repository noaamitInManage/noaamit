<?php
include_once("../_inc/config.inc.php");

$db_fullError = true;

$query = "SELECT proc.*,IFNULL(parents.title,'') AS parent_title
			FROM tb_sys_processes AS proc
			LEFT JOIN tb_sys_processes AS parents
			ON (
				(parents.id = proc.parentid)
			)
			ORDER BY proc.section,IF(proc.parentid > 0,proc.parentid,proc.show_order),proc.id
			";
$result = $Db->query($query) ;
$current_parent = 0;
$last_id = 0;
$current_section = 'main';

//Build sysprocess:
$sysprocessArr_string = "<?php\n";
while ($row = $Db->get_stream($result)) {
	if(($row['parentid']!=$current_parent && $last_id !=$row['parentid']) || ($current_section != $row['section'])){
		$sysprocessArr_string .=  "\n";
	}
	$tab = $row['parentid'] > 0 ? "\t" : "";
	$sysprocessArr_string .= $tab.'$sysprocessArr[\''.$row['section'].'\'][\''.$row['tree'].'\'][\''.$row['parentid'].'\'][] = '.$row['id'].'; //'.($row['parent_title'] != '' ? $row['parent_title'].' - ' : '').$row['title']."\n";
	$current_parent = $row['parentid'];
	$current_section = $row['section'];
	$last_id = $row['id'];
}
$sysprocessArr_string .= "\n?>";

$fp = fopen($_SERVER['DOCUMENT_ROOT'].'/salat2/_static/sysprocess.inc.php','w+') or die('can\'t open file in line '.__LINE__);
fwrite($fp,$sysprocessArr_string);
fclose($fp);

##============================================###

$build_section = 'site';
$build_lang = 1;
$query = "SELECT proc.*,IFNULL(parents.title,'') AS parent_title
			FROM tb_sys_processes AS proc
			LEFT JOIN tb_sys_processes AS parents
			ON (
				(parents.id = proc.parentid)
			)
			WHERE proc.section = '{$build_section}'
			ORDER BY IF(proc.parentid=0,proc.id,proc.parentid)
			";
$result = $Db->query($query);


//tree 
//$build_section.main.js.php
 
$tree_string = "<?php\n";
$js_string = "<script type=\"text/javascript\">\n";
$js_string .= "\t/* nodes link details */\n";
$js_string .= "\tvar treeLinksArr = new Array()\n";

$treeKeysArr_string = "\tvar treeKeysArr = new Array();\n";

$current_parent = 0;
while ($row = $Db->get_stream($result)) {
	if($current_parent != $row['parentid']){
		$tree_string .= "\n";
		$js_string .= "\n";
	}
	$parent_title = $row['parent_title'] !='' ? $row['parent_title'].' - ' : '';
	$current_parent = $row['parentid'];
	
	if($row['show_in_tree']== 0) continue;
	//$tree[0][10] = Array('title'=>'General','isactive'=>'yes');
	$tree_string .='$tree['.$row['parentid'].']['.$row['id'].'] = Array(\'title\'=>\''.$row['title'].'\',\'isactive\'=>\'yes\');//'.$parent_title.$row['title']."\n";
	
	
	if($row['page']!=''){
		
		$js_string .= "\t//{$parent_title}{$row['title']}\n";
		$js_string .= "\t treeLinksArr['{$row['id']}-type'] = \"html\";\n";
		$js_string .= "\t treeLinksArr['{$row['id']}-link'] = \"{$row['section']}/{$row['page']}\";\n";
		$js_string .= "\t treeLinksArr['{$row['id']}-target'] = \"framMain\";\n\n";
		
		$treeKeysArr_string .= "\t treeKeysArr['{$parent_title}{$row['title']}']  = {$row['id']};\n";
	}
}

$tree_string .= "\n?>";

$js_string .= "\n\n\n".$treeKeysArr_string;
$js_string .= "\n</script>";

//Write site.main.js.php

$file_name = $_SERVER['DOCUMENT_ROOT'].'/salat2/_trees/'.$build_section.'.main.js.php';
$fp = fopen($file_name,'w') or die("can't open file in line ".__LINE__);
fwrite($fp,$js_string) or die("can't write to file");
fclose($fp);

//Write site.main.tree.1.php
$file_name = $_SERVER['DOCUMENT_ROOT'].'/salat2/_trees/'.$build_section.'.main.tree.'.$build_lang.'.php';
$fp = fopen($file_name,'w') or die("can't open file in line ".__LINE__);
fwrite($fp,$tree_string);
fclose($fp);

if($_SERVER['REMOTE_ADDR']=='62.219.212.139'){
	print "inManage Ip Only:<br/><b><u style=\"font-size:17px;\">{$build_section}</u> Static files were succussfully updated at <br/>".__FILE__.'</b><br/><br/>';
}


?>