<?
require_once($_project_server_path . $_salat_path . "modules_fields/fields.functions.inc.php");

$responseArr = array("err"=>'',"status"=>0,'html'=>'');
$err='';
$msg='';
$html='';
$status = false;
$act= $_REQUEST['act'] ? $_REQUEST['act'] : $_REQUEST['action'];

switch ($act){

	case 'add_company_industry':

		if(isset($_REQUEST['company_id']) && $_REQUEST['company_id'] && isset($_REQUEST['industry_id']) && $_REQUEST['industry_id']){
			$query = "SELECT *
                        FROM `tb_companies__industries`
                            WHERE `company_id` = {$_REQUEST['company_id']} AND `industry_id` = {$_REQUEST['industry_id']}";
			$res = $Db->query($query);
			if(!$res->num_rows){
				$db_fields = array(
					'company_id' => $_REQUEST['company_id'],
					'industry_id' => $_REQUEST['industry_id'],
					'last_update' => time()
				);
				foreach ($db_fields AS $key => $value) {
					$db_fields[$key] = $Db->make_escape($value);
				}
				$query = "INSERT INTO `tb_companies__industries` (`" . implode("`,`", array_keys($db_fields)) . "`) VALUES ('" . implode("','", array_values($db_fields)) . "')";
				$Db->query($query);
				$html = draw_connected_company_industries($_REQUEST['company_id']);
				$status = 1;
			}else{
				$status = 0;
				$err = 'תגית זו כבר משויכת';
			}
		}
		break;


	case 'delete_company_industry':

		if(isset($_REQUEST['company_id']) && $_REQUEST['company_id'] && isset($_REQUEST['industry_id']) && $_REQUEST['industry_id']){
			$query = "DELETE FROM `tb_companies__industries` WHERE `company_id` = {$_REQUEST['company_id']} AND `industry_id` = {$_REQUEST['industry_id']}";
			$Db->query($query);
			$html = draw_connected_company_industries($_REQUEST['company_id']);
			$status = 1;
		}

		break;
}
$responseArr['html'] = $html;
$responseArr['err'] = $err;
$responseArr['msg'] = $msg;
$responseArr['status'] = $status;
exit(json_encode($responseArr));
?>