<?

/**
 * Resolution Manager with Media ZIP
 */
class resolutionsManager extends BaseManager
{

    public $id = 0;
    public $title = '';
    public $content = '';
    public $media_id = '';
    public $active = '';

    public $domain_site = '';

    /*----------------------------------------------------------------------------------*/

    function __construct($item_id = '', $full = 0)
    {
        parent::__construct();

        if ($item_id) {
            include($_SERVER['DOCUMENT_ROOT'] . "_static/resolutions/" . get_item_dir($item_id) . "/resolution-" . $item_id . ".inc.php"); //$resolutionArr
            $this->id = $item_id;
            foreach ($resolutionArr AS $key => $value) {
                if (property_exists($this, $key)) {
                    $this->{$key} = $value;
                }
            }
        }
        if ($full) {
            include($_SERVER['DOCUMENT_ROOT'] . "_static/resolutions.inc.php"); //$resolutionsArr
            $this->itemsArr = $resolutionsArr;
        }
    }

    /*----------------------------------------------------------------------------------*/

    function __destruct()
    {

    }

    /*----------------------------------------------------------------------------------*/

    public function __set($var, $val)
    {
        $this->$var = $val;

    }

    /*----------------------------------------------------------------------------------*/

    public function __get($var)
    {
        return $this->$var;
    }

    /*----------------------------------------------------------------------------------*/
    public function make_all_resolutions($path)
    {  // make zip files for each resolution
        $queryMedia = "SELECT * FROM `tb_resolutions`";
        $resultMedia = $this->db->query($queryMedia) or die($queryMedia);
        $res_num = 0;
        $flag = 0;
        while ($lineMedia = $this->db->get_stream($resultMedia)) {
            $res_num++;
            $answ = $this->make_all_zips($path, $lineMedia['id']);
            if (!empty($answ) && $answ == 'OK') {
                $flag++;
            }
        }
        if ($flag == $res_num) {
            return 'OK';
        }
    }

    /*----------------------------------------------------------------------------------*/

    public function make_all_zips($path, $res_id = 0)
    {  // also if res_id=0 it makes one zip file of all resolutions
        $ts = time();
        $path_old = $path . 'old_all_' . $ts . '.zip';
        $num_files = 1;
        $catSql = (isset($res_id) && $res_id > 0) ? "AND Main.resolution_id={$res_id}" : "";
        $queryMedia = "SELECT Media.*,Main.resolution_id,Main.last_update as 'update_time',Resolution.title as 'res_title'
						FROM `tb_media_resolutions` AS Main
							LEFT JOIN `tb_media` AS Media
							 	ON (Media.id=Main.media_id)
							 		LEFT JOIN `tb_resolutions` AS Resolution
							 			ON (Main.resolution_id=Resolution.id)
							 				WHERE Media.id>0 AND Resolution.id>0 {$catSql}";
        $resultMedia = $this->db->query($queryMedia);
        $zip = new ZipArchive();
        $resolArr = array();
        $jsonArr = array();
        if ($resultMedia->num_rows) {
            if ($zip->open($path_old, ZipArchive::CREATE) === TRUE) {
                $title = '';
                $sql = "INSERT INTO `tb_media` (`image_path`, `last_update`) VALUES ";
                while ($lineMedia = $this->db->get_stream($resultMedia)) {
                    $title = (isset($res_id) && $res_id > 0) ? $lineMedia['res_title'] : '';
                    $path_media = '/_media/media/' . $lineMedia['category_id'] . '/' . $lineMedia['id'] . '_' . $lineMedia['res_title'] . '.' . $lineMedia['img_ext'];
                    $path_from = $_SERVER['DOCUMENT_ROOT'] . $path_media;
                    //$resolArr[$lineMedia['resolution_id']]=array('res_id'=>$lineMedia['resolution_id'],'res_title'=>$lineMedia['res_title']);
                    if ($num_files == 1 && !empty($lineMedia['category_id']) && $lineMedia['category_id'] > 0) {
                        $sql .= "('" . $path_media . "'," . $lineMedia['update_time'] . ")";
                    } else if (!empty($lineMedia['category_id']) && $lineMedia['category_id'] > 0) {
                        $sql .= ", ('" . $path_media . "'," . $lineMedia['update_time'] . ")";
                    }
                    $jsonArr[$lineMedia['id']]['last_update'] = $lineMedia['update_time'];
                    $jsonArr[$lineMedia['id']]['image_path'] = $path_media;
                    if (($num_files % 100) > 98) {
                        $zip->close();
                        if ($zip->open($path_old, ZipArchive::CREATE) === TRUE) {
                            $this->add_one_file($zip, $path_from, '');
                        } else {
                            return false;
                        }
                    } else {
                        $this->add_one_file($zip, $path_from, '');
                    }
                    $num_files++;
                }
                foreach ($jsonArr as $key => $arrIns) {
                    $arr[$arrIns['image_path']] = $arrIns['last_update'];
                }
                $sql .= ';';
                $this->add_one_file($zip, '', $sql);
                $this->add_one_file($zip, '', json_encode($arr), true);
                $zip->close();
                chmod($path_old, 0777);
                if (isset($res_id) && $res_id > 0) {
                    rename($path_old, $path . $title . '.zip');
                } else {
                    rename($path_old, $path . 'all_resolutions.zip');
                }
            } else {
                return false;
            }
        }
        return 'OK';
    }

    /*----------------------------------------------------------------------------------*/
    public function add_one_file($zip, $path_from = '', $txt = '', $json = false)
    { //add files to zip
        if (!empty($path_from)) {
            $path_fromArr = explode('/', $path_from);
            $media = array_pop($path_fromArr);
            $zip->addFile($path_from, '_media/media/' . array_pop($path_fromArr) . '/' . $media);//
        } else if (!empty($txt) && !$json) {
            $zip->addFromString('media.sql', $txt);
        } else if ($json) {
            $zip->addFromString('media.json', $txt);
        }
    }

    public function make_pc_zip($path)
    {  //make one big file for all PC (regular) medias
        $ts = time();
        $path_old = $path . 'old_all_' . $ts . '.zip';
        $num_files = 1;
        $queryMedia = "SELECT * FROM `tb_media` AS Main";
        $resultMedia = $this->db->query($queryMedia) or die($queryMedia);
        $zip = new ZipArchive();
        $jsonArr = array();
        if ($zip->open($path_old, ZipArchive::CREATE) === TRUE) {
            $sql = "INSERT INTO `tb_media` (`image_path`, `last_update`) VALUES ";
            while ($lineMedia = $this->db->get_stream($resultMedia)) {
                $path_media = '/_media/media/' . $lineMedia['category_id'] . '/' . $lineMedia['id'] . '.' . $lineMedia['img_ext'];
                $path_from = $_SERVER['DOCUMENT_ROOT'] . $path_media;
                if ($num_files == 1 && isset($lineMedia['category_id']) && $lineMedia['category_id'] > 0) {
                    $sql .= "('" . $path_media . "'," . $lineMedia['last_update'] . ")";
                } else if (isset($lineMedia['category_id']) && $lineMedia['category_id'] > 0) {
                    $sql .= ", ('" . $path_media . "'," . $lineMedia['last_update'] . ")";
                }
                $jsonArr[$lineMedia['id']]['last_update'] = $lineMedia['update_time'];
                $jsonArr[$lineMedia['id']]['image_path'] = $path_media;
                if (($num_files % 100) > 98) {
                    $zip->close();
                    if ($zip->open($path_old, ZipArchive::CREATE) === TRUE) {
                        $this->add_one_file($zip, $path_from, '');
                    } else {
                        return false;
                    }
                } else {
                    $this->add_one_file($zip, $path_from, '');
                }
                $num_files++;
            }
            $sql .= ';';
            foreach ($jsonArr as $key => $arrIns) {
                $arr[$arrIns['image_path']] = $arrIns['last_update'];
            }
            $this->add_one_file($zip, '', $sql);
            $this->add_one_file($zip, '', json_encode($arr), true);
            $zip->close();
            chmod($path_old, 0777);
            rename($path_old, $path . 'media_pc.zip');
        } else {
            return false;
        }
        return 'OK';
    }
}

?>