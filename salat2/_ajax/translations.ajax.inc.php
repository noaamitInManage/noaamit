<?php

set_time_limit(0);
$act=($_REQUEST['act']) ? $_REQUEST['act'] : 'show' ;
$err='';
$status = 'false';
$Db = Database::getInstance();
switch ($act){

	case "save":

		/* Example SQL

			UPDATE `tb_translate`
				SET `text` = CASE `lang_id`
					WHEN 20 THEN 'a'
					WHEN 30 THEN 'a'
					WHEN 40 THEN 'a'
					WHEN 50 THEN 'a'
				ELSE CONCAT(text,'_more') END
			WHERE `key_code`='test'

		*/

//echo '<pre style="direction: ltr; text-align: left;">';
//	print_r( $_REQUEST );
//echo '</pre>';	


		$key_code = $Db->make_escape(sanitizeText(rawurldecode($_POST['data']['key_code'])));
		$save_in_gd='';
		if(isset($_POST['data']['save_in_gd'])){
			$save_in_gd = $Db->make_escape($_POST['data']['save_in_gd']);
		}
		$languageCases = array();
		foreach ($_POST['data']['values'] as $item) {
			$rawValue = $Db->make_escape(rawurldecode($item['text']));
//		$rawValue = $Db->make_escape(sanitizeText(rawurldecode($item['text'])));
//		$rawValue = $Db->make_escape(rawurldecode($item['text']));
			$languageCases[] = "WHEN {$item['langid']} THEN '".$rawValue."'";

			$lang_id=$Db->make_escape($item['langid']);
			$updateSql="UPDATE `tb_translate` SET `show_in_gd`={$save_in_gd}, `text`='{$rawValue}' WHERE `key_code`='{$key_code}' AND `lang_id`='{$lang_id}'";
			$updateRes = $Db->query($updateSql);
			if ($updateRes){
				$status = 'ok';
			}else{
				$db_fields=array(
					"lang_id"=>$lang_id,
					"key_code"=>$key_code,
					"text"=>$rawValue,
					"show_in_gd"=>$save_in_gd,
				);
				foreach($db_fields AS $key=>$value){
					$db_fields[$key]=$Db->make_escape($value);
				}
				$query = "INSERT INTO `tb_translate` (`".implode("`,`",array_keys($db_fields))."`) VALUES ('".implode("','",array_values($db_fields))."')";
				$res = $Db->query($query);
				if($res){
					$status = 'ok';
				}else{
					$err = $updateSql;
				}
			}
		}
		break;
case "delete":

	$rawValue = $Db->make_escape(sanitizeText(rawurldecode($_POST['key_code'])));
	$deleteSql = "DELETE FROM `tb_translate` WHERE `key_code` = '{$rawValue}'";
	$deleteRes = $Db->query($deleteSql);
	if ($deleteRes){
		$status = 'ok';
	}else{
		$err = $deleteSql;
	}
	
	break;

case "save-new":

	$data = $_POST['data'];

	$hasIllegalChars = preg_match('/[^a-zA-Z0-9\-_]/',$data['key_code'],$matches);

	if (!$hasIllegalChars){

		// check if this key already exists
		$key_code = $Db->make_escape(sanitizeText($data['key_code']));
		$save_in_gd = $Db->make_escape(($data['save_in_gd']));
		$keyCodeSql = "SELECT * FROM `tb_translate` WHERE `key_code`='{$key_code}'";
		$keyCodeRes = $Db->query($keyCodeSql);
		if ($keyCodeRes->num_rows != 0){
			$err = 'מפתח כבר קיים!';
			break;
		}else{
			// doesn't exist! Add it to DB
			$values = array();
			foreach ($data['values'] as $value) {
				$rawValue = $Db->make_escape(rawurldecode($value['text']));
//				$rawValue = $Db->make_escape(sanitizeText(rawurldecode($value['text'])));
				$values[] = "({$value['langid']},'{$key_code}','{$rawValue}','{$save_in_gd}')";
			}
			$newKeySql = "INSERT INTO `tb_translate` (lang_id,key_code,text,show_in_gd) VALUES ".implode(',',$values);
			$newKeyRes = $Db->query($newKeySql);
			$err = $newKeySql;
			if ($newKeyRes){
				$status = 'ok';
			}
		}
	}else{
		$err = 'נא להזין רק מספרים ואותיות באנגלית.';
	}
	break;
default:
	break;
}


//// refresh static files
include_once($_project_server_path.$_includes_path.'site.array.inc.php'); // 15/04/2011
$UpdateStatic = new translateUpdateStaticFiles();

$UpdateStatic->updateStatics();


echo json_encode(array("err"=>$err,"status"=>$status));


?>

