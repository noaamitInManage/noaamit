<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");

set_time_limit(0);
$act=($_REQUEST['act']) ? $_REQUEST['act'] : 'show' ;
$err='';
$status = 'false';

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
	$languageCases = array();
	foreach ($_POST['data']['values'] as $item) {
		$rawValue = $Db->make_escape(rawurldecode($item['text']));
		$descripation = $Db->make_escape(rawurldecode($item['descripation']));
//		$rawValue = $Db->make_escape(sanitizeText(rawurldecode($item['text'])));
//		$rawValue = $Db->make_escape(rawurldecode($item['text']));
		$languageCases[] = "WHEN {$item['langid']} THEN '".$rawValue."'";


        $lang_id=$Db->make_escape($item['langid']);
        $query="UPDATE `tb_cg_errors` SET `descripation`='{$descripation}' WHERE `error_code`='{$key_code}' ";
        $updateRes = $Db->query($query);
		$query="UPDATE `tb_cg_errors` SET `content`='{$rawValue}'  WHERE `error_code`='{$key_code}' AND `lang_id`='{$lang_id}'";
        $updateRes = $Db->query($query);
	}
        if ($updateRes){
            $status = 'ok';
        }else{
            $db_fields=array(
                "lang_id"=>$lang_id,
                "error_code"=>$key_code,
                "content"=>$rawValue,
            );
            foreach($db_fields AS $key=>$value){
                $db_fields[$key]=$Db->make_escape($value);
            }
            $query = "INSERT INTO `tb_cg_errors` (`".implode("`,`",array_keys($db_fields))."`) VALUES ('".implode("','",array_values($db_fields))."')";
            $res = $Db->query($query);
            if($res){
                $status = 'ok';
            }else{
                $err = $updateSql;
            }
        }


        break;
case "delete":

	$rawValue = $Db->make_escape(sanitizeText(rawurldecode($_POST['key_code'])));
	$deleteSql = "DELETE FROM `tb_cg_errors` WHERE `error_code` = '{$rawValue}'";
	$deleteRes = $Db->query($deleteSql);
	if ($deleteRes){
		$status = 'ok';
	}else{
		$err = $deleteSql;
	}
	
	break;

case "save-new":

	$data = $_POST['data'];
	$data['key_code']=trim($data['key_code']);
	$hasIllegalChars = preg_match('/[^a-zA-Z0-9\-_]/',$data['key_code'],$matches);

	if (!$hasIllegalChars){

		// check if this key already exists
		$key_code = $Db->make_escape(sanitizeText($data['key_code']));
		$keyCodeSql = "SELECT * FROM `tb_cg_errors` WHERE `error_code`='{$key_code}'";
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
				$values[] = "({$value['langid']},'{$key_code}','{$rawValue}')";
			}
			$newKeySql = "INSERT INTO `tb_cg_errors` (lang_id,error_code,content) VALUES ".implode(',',$values);
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
$UpdateStatic = new cg_errorsUpdateStaticFiles();
$UpdateStatic->updateStatics();


echo json_encode(array("err"=>$err,"status"=>$status));


?>

