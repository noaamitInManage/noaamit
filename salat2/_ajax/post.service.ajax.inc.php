<?
$act=$_REQUEST['act'];
$answer=array(
		"err"=>"",
		"msg"=>"",
		"relocation"=>""
);
$tb_picture='tb_post_pictures';


switch ($act){
	
	case 'del_image':
		$id=$_REQUEST['id'];		

		$row=mysql_fetch_assoc($Db->query($query)("SELECT * FROM `$tb_picture` WHERE id='{$id}' LIMIT 0,1"));
		@unlink($_SERVER['DOCUMENT_ROOT'].'/_media/posts/'.get_item_dir($row['id']).'/'.$row['id'].'.'.$row['img_ext']);
		if($id){mysql_unbuffered_query("DELETE FROM `$tb_picture` WHERE id='{$id}'");}
		$answer['msg']='התמונה נמחקה!';
				
		echo  json_encode($answer);
			exit();
		break;
		
	default:
		
		
		break;	
	
	
}
?>