<?
$answerArr=array('err'=>'','msg'=>'');
$act= $_REQUEST['act'];

switch (strtolower($act)){
    case 'delete_rows':
        $query="DELETE FROM `tb_stores_link` WHERE id={$_REQUEST['id']}";
        $res=$Db->query($query);
        $answerArr['msg']='OK';
        break;
}
exit(json_encode($answerArr));
?>
