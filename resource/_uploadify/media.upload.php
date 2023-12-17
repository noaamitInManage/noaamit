<?
include_once($_SERVER['DOCUMENT_ROOT'] . "/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");

include_once($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/functions.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/site.array.inc.php');
set_time_limit(0);
ini_set('MEMORY_LIMIT','256MB');
$Db = Database::getInstance();

set_time_limit(0);
ini_set('MEMORY_LIMIT', '256MB');
$image_types_arr = array('jpg', 'png', 'gif', 'jpeg', 'bmp');
$image_typesDot_arr = array('.jpg', '.png', '.gif', '.jpeg', '.bmp');

$album_id = isset($_REQUEST['album_id']) ? $_REQUEST['album_id'] : '';
$resolution_id = isset($_REQUEST['resolution_id']) ? $_REQUEST['resolution_id'] : '';
$media_id = isset($_REQUEST['media_id']) ? $_REQUEST['media_id'] : '';
$ext = explode('.', $_REQUEST['Filename']);
$img_ext = array_pop($ext);
$file_name = str_replace('.' . $img_ext, '', $_REQUEST['Filename']);
if (in_array(strtolower($img_ext), $image_types_arr)) {
    //save what you need..
    $img_ext = strtolower($img_ext);
    if ((!empty($_FILES))) {
        $tempFile = $_FILES['Filedata']['tmp_name'];
        $targetPath = $_SERVER['DOCUMENT_ROOT'] . '/_media/media/' . $album_id . '/'; //the same
        $targetFile = $targetPath . $_FILES['Filedata']['name'];
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0777);
            chmod($targetPath, 0777);
        }

        move_uploaded_file($tempFile, $targetFile);
        list($w, $h) = getimagesize($targetFile);    // image validate
        $query = "SELECT id,title,mobile,
						(SELECT COUNT(`id`) FROM `tb_media` WHERE `category_id`='{$album_id}') AS 'total_items'
						 FROM `tb_media_category` WHERE `id`='{$album_id}'
			   ";
        $result = $Db->query($query);
        $row = $Db->get_stream($result);
        // on multi upload give picture auto title & alt
        $pic_name = $file_name;
        $db_fields = array(
            "title" => $pic_name,
            "alt" => $pic_name,
            "category_id" => $album_id,
            "img_ext" => $img_ext, // work only with jpg save time in the production
            "last_update" => time()

        );
        if ($w) {
            foreach ($db_fields AS $key => $value) {
                $db_fields[$key] = $Db->make_escape($value);
            }

            //Upload of resolution: save to the tb_media_resolution if resolution
            if ($row['mobile'] && $resolution_id && $media_id) {
                $tempTarget = $_SERVER['DOCUMENT_ROOT'] . '/_media/media/' . $album_id . '/';

                $queryRes = "SELECT id,title  FROM `tb_resolutions` WHERE id={$resolution_id} ";
                $resRes = $Db->query($queryRes);
                $line = $Db->get_stream($resRes);
                $resol_mediaQuery = "SELECT * FROM `tb_media_resolutions` WHERE `media_id`={$media_id} AND `resolution_id`={$resolution_id}";
                $resQery = $Db->query($resol_mediaQuery);
                if ($resQery->num_rows > 0) {    // make update
                    $line_update = $Db->get_stream($resQery);
                    $db_fields = array(
                        "media_id" => $media_id,  // do not insert new media, take old MAIN media
                        "resolution_id" => $resolution_id,
                        "last_update" => time()
                    );
                    $updateArr = array();
                    foreach ($db_fields as $k => $v) {
                        $v = $Db->make_escape($v);
                        $updateArr[] = "`$k` = '{$v}' ";
                    }
                    $query = "UPDATE `tb_media_resolutions` SET  " . implode(',', $updateArr) . " WHERE `id`='{$line_update['id']}'";
                    $Db->query($query);
                    @unlink($tempTarget . $media_id . '_' . $line['title'] . '.' . $img_ext);
                } else {                        //make insert
                    $db_fields = array(
                        "media_id" => $media_id,
                        "resolution_id" => $resolution_id,
                        "last_update" => time()
                    );
                    foreach ($db_fields AS $key => $value) {
                        $db_fields[$key] = $Db->make_escape($value);
                    }
                    $query = "INSERT INTO `tb_media_resolutions` (`" . implode("`,`", array_keys($db_fields)) . "`) VALUES ('" . implode("','", array_values($db_fields)) . "')";
                    $res = $Db->query($query);
                }
                $item_media_res_id = $Db->get_insert_id();
                //chmod($tempTarget,0777);
                $image = new Imagick($targetFile);
                //$image->setImageFormat("jpg");

                $image->writeImage($tempTarget . $media_id . '_' . $line['title'] . '.' . $img_ext);
            } else {  //regular Upload
                $query = "INSERT INTO `tb_media` (`" . implode("`,`", array_keys($db_fields)) . "`) VALUES ('" . implode("','", array_values($db_fields)) . "')";
                $res = $Db->query($query);
                $item_media_id = $Db->get_insert_id();
                $image = new Imagick($targetFile);
                //$image->setImageFormat("jpg");
                $image->writeImage($targetPath . $item_media_id . '.' . $img_ext);

            }
            //update resolution_media set old_media_id
            $item_media_id = ($item_media_id) ? $item_media_id : $media_id;
            $db_fields = array(
                "old_media_id" => $item_media_id,
                "last_update" => time()
            );
            $updateArr = array();
            foreach ($db_fields as $k => $v) {
                $v = $Db->make_escape($v);
                $updateArr[] = "`$k` = '{$v}' ";
            }

            if (isset($line_update['id']) && $line_update['id']) {
                $query = "UPDATE `tb_media_resolutions` SET  " . implode(',', $updateArr) . " WHERE `id`='{$line_update['id']}'";
                $Db->query($query);
            }
            $image->destroy();
            @unlink($targetFile);
            usleep(2);
            $UpdateStatic = new mediaUpdateStaticFiles();
            $_REQUEST['inner_id'] = ($row['mobile'] && $resolution_id && $media_id && isset($line['title'])) ? $media_id : $item_media_id;
            $UpdateStatic->updateStatics();
            usleep(2);
            exit(json_encode(array('album_id' => $album_id, "item_id" => ($row['mobile'] && $resolution_id && $media_id && isset($line['title'])) ? $media_id : $item_media_id)));
        }
    }
}
exit(json_encode(array('err' => $errors)));
?>