<?

$tb_name='tb_media_category';
header('Content-type: text/plain; charset=utf-8');

$action=(isset($_REQUEST['action'])&&!empty($_REQUEST['action']))?$_REQUEST['action']:"";

$q = (isset($_REQUEST['q'])&&!empty($_REQUEST['q']))?$Db->make_escape(trim($_REQUEST['q'])):'';

switch ($action){

    case "getId":
        $q = $Db->make_escape(trim($_REQUEST['title']));
        $query="SELECT id,title FROM `{$tb_name}` WHERE `title` LIKE '{$q}'";
        $result=$Db->query($query);
        $row1=$Db->get_stream($result);
        echo $row1['id'];
        break;
    case "getItems":
        if(strlen(trim($q))== 0){
            $query="SELECT id,title FROM `{$tb_name}`";
            $result=$Db->query($query);
            while($row1 = $Db->get_stream($result)) {
                echo $row1['title']. "\n";
            }
        }else{
            $query="SELECT id,title FROM `{$tb_name}` WHERE `title` LIKE '{$q}%'";

            $result=$Db->query($query);
            while($row1 = $Db->get_stream($result)) {
                echo $row1['title']. "\n";
            }
        }

        break;
    case "getSelcetItems":

        $answer=array("err"=>"","main_html"=>"","res_html"=>"");
        $count=0;
        $id=$Db->make_escape($_REQUEST['category']);
        // check if it is mobile category..   //$_REQUEST['res_size']   => array!
        if(is_numeric($id)){
            $tipArr=(isset($_REQUEST['typeArr'])&&$_REQUEST['typeArr'])?$_REQUEST['typeArr']:'';

            $query="SELECT Main.id,Main.title,Main.img_ext,Main.category_id
					FROM `tb_media` AS Main
      					WHERE `category_id`='{$id}'";
            $result=$Db->query($query);
            $main_html= '<option value="0" >-- Choose --</option>';
            while($row1 = $Db->get_stream($result)) {  //check without row (not overwrite global row)
                $count++;
                $main_html.= '<option value="'.$row1['category_id'].'_'.$row1['id'].'_'.$row1['img_ext'].'">'.(($row1['title'])? $row1['title'] : 'no name '.$count).'</option>';
            }
            $queryCategory="SELECT mobile FROM `tb_media_category` WHERE id='{$id}'";
            $res_category=$Db->query($queryCategory);
            $line_category=$Db->get_stream($res_category);

            if($line_category['mobile']){
                $box_html='';
                $queryResolution="SELECT * FROM `tb_resolutions` WHERE active=1 AND separate=1";
                $res_resolution=$Db->query($queryResolution);
                if($res_resolution->num_rows){
                    while($row_res = $Db->get_stream($res_resolution)) {
                        $res_html=draw_table_media($_REQUEST['field_name'],$row_res,$tipArr);
                        $box_html.=$res_html;
                    }
                    $answer['res_html']=$box_html;
                }
            }
            $answer['main_html']=$main_html;
            exit(json_encode($answer));
        }

        break;
    case "getSelcetItemsOnly":
        $count=0;
        $id=$Db->make_escape($_REQUEST['category']);
        $query="SELECT Main.id,Main.title,Main.img_ext,Main.category_id
					FROM `tb_media` AS Main
      					WHERE `category_id`='{$id}'";
        $result=$Db->query($query);
        $main_html= '<option value="0" >-- Choose --</option>';
        while($row1 = $Db->get_stream($result)) {  //check without row (not overwrite global row)
            $count++;
            $main_html.= '<option value="'.$row1['category_id'].'_'.$row1['id'].'_'.$row1['img_ext'].'">'.(($row1['title'])? $row1['title'] : 'no name '.$count).'</option>';
        }
        $answer=$main_html;
        echo($answer);
        break;
    case 'drawResolutionBoxes':
        clearstatcache();
        header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        //draw all resolsutions for the selected main picture
        $answer=array("err"=>"","res_html"=>"");
        $count=0;

        $main_id=$Db->make_escape($_REQUEST['main_pic']);
        $box_html='';
        $tipArr=(isset($_REQUEST['typeArr'])&&$_REQUEST['typeArr'])?$_REQUEST['typeArr']:'';
        $queryResolutionM="SELECT Main.*,MediaRes.media_id,MediaRes.resolution_media_id
                            FROM `tb_resolutions` as Main
                                LEFT JOIN `tb_media_resolutions` as MediaRes
                                    ON(MediaRes.resolution_id=Main.id)
										WHERE MediaRes.media_id={$main_id}
											AND Main.separate=1 AND Main.active=1";
        $res_resolutionM=$Db->query($queryResolutionM);
        $rowArr=array();
        $row1=array();  //check without row (not overwrite global row)
        $row_save=array();
        if($res_resolutionM->num_rows){
            while($row_med = $Db->get_stream($res_resolutionM)){
                $rowArr[$row_med['id']]=$row_med['id'];
                $row_save[$row_med['id']]=$row_med;
            }
        }
        $mediaGallQuery="SELECT * FROM `tb_media` as Media
                                LEFT JOIN `tb_media_category` as Gallery
                                   	 ON(Gallery.id=Media.category_id)
                                   	 	WHERE Media.id={$main_id} AND Gallery.mobile=1";
        $mediaGalRes=$Db->query($mediaGallQuery);
        if($mediaGalRes->num_rows>0){
            $queryResolution="SELECT * FROM `tb_resolutions` WHERE active=1 AND separate=1";
            $res_resolution=$Db->query($queryResolution);
            global $row;
            if($res_resolution->num_rows){
                while($row_res = $Db->get_stream($res_resolution)) {
                    //if resolution_id reg = res_id in media -> get picture, otherwise just buttons
                    $row1=$row_save[$row_res['id']]; //check without row (not overwrite global row)
                    $mediaid=(isset($row['id'])&&$row['id'])?'':$row1['media_id'];
                    $res_html=(in_array($row_res['id'],$rowArr))?draw_table_media($_REQUEST['field_name'],$row_res,$tipArr,false,$mediaid):draw_table_media($_REQUEST['field_name'],$row_res,$tipArr);
                    $box_html.=$res_html;
                }
                $answer['res_html']=$box_html;
            }
        }

        exit(json_encode($answer));
        break;
    case 'deleteResolution':
        //get resolution
        $img_ext=explode('.',$_REQUEST['src']);
        $resolArr=explode('_',$img_ext[0]);
        $ext_only=explode('?',$img_ext[1]);
        $ext_only=($ext_only)?$ext_only[0]:$img_ext[1];
        $resolution=array_pop($resolArr);
        //get img_id
        $mediaArr=explode('/',$img_ext[0]);
        $mediaArr=array_pop($mediaArr);
        $mediaArr=explode('_',$mediaArr);
        $media_id=$mediaArr[0];

        $query_res="SELECT id FROM `tb_resolutions` WHERE title='{$resolution}' AND separate=1 AND active=1";
        $result_res=$Db->query($query_res);
        $line_res=$Db->get_stream($result_res);

        $query="DELETE FROM `tb_media_resolutions` WHERE `media_id`={$media_id} AND `resolution_id`={$line_res['id']}";
        $Db->query($query);
        //get gallery_id
        $category=explode('/',$resolArr[1]);
        array_pop($category);
        $category=array_pop($category);

        @unlink($_SERVER['DOCUMENT_ROOT'].'/_media/media/'.$category.'/'.$media_id.'_'.$resolution.'.'.$ext_only);
        clearstatcache();
        break;
	case 'saveResolution':
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
		$flag=0;
		$album_idArr=explode('_',$_REQUEST['category']);
		$main_gallery=$_REQUEST['main_gallery'];

		//CHANGES...=============================
		/*  if(!$main_gallery){
				$queryResTit="SELECT Main.category_id FROM `tb_media` AS Main
									WHERE Main.id={$album_idArr[1]}";
				$resResTitle=$Db->query($queryResTit);
				$lineResTitle=$Db->get_stream($resResTitle);
				$main_gallery=$lineResTitle['category_id'];
			}*/

		//=======================================

		$ext=$album_idArr[2];
		$queryResTit="SELECT Main.title FROM `tb_resolutions` AS Main
								WHERE Main.id={$_REQUEST['resolution']}
								   AND Main.active=1 AND Main.separate=1";
		$resResTitle=$Db->query($queryResTit);
		$lineResTitle=$Db->get_stream($resResTitle);
		$res_old_id[1]=$lineResTitle['title'];
		$media_new=$_REQUEST['main_pic'];
		$extQuery="SELECT `img_ext` FROM `tb_media` WHERE `id`={$media_new}";
		$extResult=$Db->query($extQuery);
		$img_extLine=$Db->get_stream($extResult);
		$oldMedia=array( //
			0=>$_REQUEST['main_pic'],
			1=>$lineResTitle['title'].'.'.$album_idArr[2]
		);
		$small_gallery_id=$album_idArr[0];
		if(substr($_SERVER['DOCUMENT_ROOT'],-1,1)=="/"){
			$targetPath = $_SERVER['DOCUMENT_ROOT']. '_media/media/'.$small_gallery_id.'/'; //the same
			$newTargetPath = $_SERVER['DOCUMENT_ROOT']. '_media/media/'.$main_gallery.'/'; //the same
		}else{
			$targetPath=$_SERVER['DOCUMENT_ROOT']. '/_media/media/'.$small_gallery_id.'/'; //the same
			$newTargetPath=$_SERVER['DOCUMENT_ROOT']. '/_media/media/'.$main_gallery.'/'; //the same
		}

		$oldFile=$targetPath.$album_idArr[1].'.'.$ext;
		if(!file_exists($oldFile)){
			$queryOldCategory="SELECT category_id,img_ext FROM `tb_media` WHERE id={$album_idArr[1]}";
			$resultOldCategory=$Db->query($queryOldCategory);
			$lineOldCategory=$Db->get_stream($resultOldCategory);
			$targetPath=(substr($_SERVER['DOCUMENT_ROOT'],-1,1)=="/")?$_SERVER['DOCUMENT_ROOT']. '_media/media/'.$lineOldCategory['category_id'].'/':$_SERVER['DOCUMENT_ROOT']. '/_media/media/'.$lineOldCategory['category_id'].'/';
			$oldFile=$targetPath.$album_idArr[1].'.'.$lineOldCategory['img_ext'];
		}
		$newFile=$newTargetPath.$media_new.'_'.$lineResTitle['title'].'.'.$img_extLine['img_ext'];  //change from jpg.. this is of small picture($album_idArr[2])
		if(file_exists($newFile)){
			@unlink($newFile);
		}
		if(!is_dir($newTargetPath)){
			mkdir($newTargetPath,0777);
			chmod($newTargetPath,0777);
		}
		move_uploaded_file($oldFile,$newFile); // that is all path for old image; new destination and name..
		list($w, $h) = getimagesize($newFile);	// image validate

		$image = new Imagick($oldFile);
		// $image->setImageFormat($img_extLine['img_ext']);
		$image->writeImage($newFile);
		$image->destroy();
		//	@unlink($targetFile);
		$tb_name='tb_media_resolutions';
		$queryResId="SELECT Main.id FROM `tb_resolutions` AS Main
						LEFT JOIN `tb_media_resolutions` AS ResMedia
							ON (ResMedia.resolution_id=Main.id)
							WHERE Main.title LIKE '{$res_old_id[1]}'
								AND Main.active=1 AND Main.separate=1
									AND ResMedia.media_id={$media_new}";
		$resREsID=$Db->query($queryResId);
		if($resREsID->num_rows > 0){
			$flag=1;
		}
		if($flag){
			//update Date in table
			$db_fields=array(
				'last_update'=>time(),
				"resolution_media_id"=>$album_idArr[1]
			);

			$updateArr=array();
			foreach ($db_fields as $k => $v){
				$v=$Db->make_escape($v);
				$updateArr[] ="`$k` = '{$v}' ";
			}
			$lineREsID=$Db->get_stream($resREsID);
			$query="UPDATE `{$tb_name}` SET  ".implode(',',$updateArr)." WHERE `media_id`='{$media_new}' AND `resolution_id`={$lineREsID['id']}";
			$Db->query($query);
		}else{
			//insert
			$db_fields=array(
				"media_id"=>$media_new,
				"resolution_id"=>$_REQUEST['resolution'],
				"resolution_media_id"=>$album_idArr[1],
				"last_update"=>time()
			);
			foreach($db_fields AS $key=>$value){
				$db_fields[$key]=$Db->make_escape($value);
			}
			$query = "INSERT INTO `tb_media_resolutions` (`".implode("`,`",array_keys($db_fields))."`) VALUES ('".implode("','",array_values($db_fields))."')";
			$res = $Db->query($query);
		}
		flush();
		clearstatcache();
		header("Expires: Tue, 01 Jan 2000 00:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
		break;
}

exit();

?>