<?

$act=($_REQUEST['action'])? strtolower(trim($_REQUEST['action'])):strtolower(trim($_REQUEST['act']));
$answer=array("err"=>"","msg"=>"","relocation"=>"","html"=>"");

switch ($act){
	// return alt & title for salat media plugin
	case 'get_item_details':
		
		$item_id=isset($_REQUEST['item_id'])? trim($Db->make_escape($_REQUEST['item_id'])): '';
		
		if($item_id){
			$query="SELECT `alt`,`title` FROM `tb_media` WHERE `id`='{$item_id}'";
			$result=$Db->query($query);
			$row=$Db->get_stream($result);
			$answer['alt']=$row['alt'];
			$answer['title']=$row['title'];
			exit(json_encode($answer));
		}else{
			exit(json_encode($answer));
		}
		
		
		break;

	case 'del':
		// media module processid = 16
		$query = "SELECT * FROM tb_sys_user_permissions WHERE ((sysuserid=".$_SESSION['salatUserID'].") AND (processid=16))";
		$result = $Db->query($query);
		if ($result->num_rows==0){
			print "Nice try...";
			exit();
		}

		$status = 'false';

		$mediaId = intval($_POST['id']);

		$staticFileDir = smartDirctory('/_static/media',$mediaId);
		$staticFilePath = $_SERVER['DOCUMENT_ROOT'].$staticFileDir."media-{$mediaId}.inc.php";
		// include static file to retrieve category_id
		include($staticFilePath); // $imgArr
		// delete static file
		$status = unlink($staticFilePath);
		$category_id = $imgArr['category_id'];
		$img_ext = $imgArr['img_ext'];

		//delete fizical files of resolutions
		$queryRes="SELECT Main.*,Resolution.title,Media.img_ext FROM `tb_media_resolutions` as Main
							LEFT JOIN `tb_resolutions` as Resolution
								ON (Resolution.id=Main.resolution_id)
									LEFT JOIN `tb_media` as Media
										ON (Main.resolution_media_id=Media.id)
											WHERE media_id={$mediaId}";
		$resResolution=$Db->query($queryRes);
		if($resResolution->num_rows>0){
			while($lineResolution=$Db->get_stream($resResolution)){
				@unlink($_SERVER['DOCUMENT_ROOT'].'/_media/media/'.$category_id.'/'.$mediaId.'_'.$lineResolution['title'].'.'.$lineResolution['img_ext']);
			}
		}
		// delete from database
		
		$sql = "DELETE FROM `tb_media` WHERE `id`={$mediaId}";
		$res = $Db->query($sql);
		if ($res){
			//delete from resolution
			$sql = "DELETE FROM `tb_media_resolutions` WHERE ( `media_id`={$mediaId} OR `resolution_media_id`={$mediaId})";
			$res = $Db->query($sql);
			// delete physical file
			$fileName = $_SERVER['DOCUMENT_ROOT']."/_media/media/{$category_id}/{$mediaId}.{$img_ext}";
			if(file_exists($fileName)){
				$status = unlink($fileName);
			}else{
				$status = true;
			}
		}
		echo json_encode(array('success'=>$status));
		break;
	default:
		
		break;
		
}	

?>
