<?
$answerArr=array('err'=>'','msg'=>'');
$act= $_REQUEST['act'];

switch (strtolower($act)){
   case 'delete_rows':
	   $query="DELETE FROM `tb_salat_examples_link` WHERE id={$_REQUEST['id']}";
	   $res=$Db->query($query);
	   $answerArr['msg']='OK';
   break;
	case 'new_order':
		$query="UPDATE `tb_salat_examples` SET order_num={$_REQUEST['order_num']} WHERE `id`={$_REQUEST['id']}";
		$result=$Db->query($query);
		$answerArr['msg']='OK';
	break;
}
exit(json_encode($answerArr));
?>