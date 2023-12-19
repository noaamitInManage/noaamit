<?
$answerArr=array('err'=>'','msg'=>'');
$act= $_REQUEST['act'];

$query="UPDATE `tb_products` SET order_num={$_REQUEST['order_num']} WHERE `id`={$_REQUEST['id']}";
$result=$Db->query($query);
$answerArr['msg']='OK';

exit(json_encode($answerArr));
?>