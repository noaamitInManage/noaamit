<?php

function array_wrap($value)
{
    return !is_array($value) ? [$value] : $value;
}

function create_thumb($width, $height, $path)
{
    $thumb_path = $_SERVER['DOCUMENT_ROOT'] . str_replace('.', '_' . $width . 'X' . $height . '.', $path);
    if (file_exists($thumb_path)) {
        return str_replace($_SERVER['DOCUMENT_ROOT'], '', $thumb_path);
    }
    if (!file_exists($path)) {
        return $path;
    }

    $image = new Imagick(realpath($_SERVER['DOCUMENT_ROOT'] . $path));
    $image->thumbnailImage($width, $height, 0);
    $thumb_path = $_SERVER['DOCUMENT_ROOT'] . str_replace('.', '_' . $width . 'X' . $height . '.', $path);
    $image->writeImage($thumb_path);
    $thumb_path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $thumb_path);
    return str_replace($_SERVER['DOCUMENT_ROOT'], '', $thumb_path);
}

// fix urls for new seo structure
function getSEOLink($url)
{
    global $modulesSEOArr;
    $tmp = '/' . $modulesSEOArr[$url];
    return $tmp;
}

// Change the Search Engine Friendly URL, by the given params
function ChangeSEOLink($param, $newval, $str = '')
{
    GLOBAL $_system_url, $qsArr;
    $tmp = '';
    $wasfound = false;
    // build new URL
    foreach ($qsArr as $value) {
        if (substr($value, ((-1) * (strlen($param) + 1))) == "," . $param) {
            $tmp .= "/" . $newval . ',' . $param . "/" . $str;
            next($qsArr); // dont copy the next param because its the name of the previous subcategory
            $wasfound = true;
        } else {
            // include the value, but dont include empty
            if ($value != '') $tmp .= "/" . $value;
        }
    }
    // add new para, if not already found
    if (!$wasfound) {
        $tmp .= "/" . $newval . ',' . $param . "/" . $str;
    }
    return ($tmp);
}

function header_status($status)
{
    // 'cgi', 'cgi-fcgi'
    if (substr(php_sapi_name(), 0, 3) == 'cgi') header('Status: ' . $status, TRUE);
    else header($_SERVER['SERVER_PROTOCOL'] . ' ' . $status);
}

function header_301($url)
{
    header_status("301 Moved Permanently");
    header("location: " . $url);
    exit();
}

function header_404($url = '/404.php')
{
    header_status("HTTP/1.0 404 Not Found");
    //header("location: /404.php");
    exit();
}

function chop_str($string, $limit, $break = " ", $pad = "...")
{
    // return with no change if string is shorter than $limit
    if (strlen($string) <= $limit) return $string;

    // is $break present between $limit and the end of the string?
    if (false !== ($breakpoint = strpos($string, $break, $limit))) {
        if ($breakpoint < strlen($string) - 1) {
            $string = substr($string, 0, $breakpoint) . $pad;
        }
    }

    return $string;
}

function setDate($thedate)
{// 03/07/2007 12:43
    $hebmonth = array('01' => 'ינואר', '02' => 'פברואר', '03' => 'מרץ', '04' => 'אפריל', '05' => 'מאי', '06' => 'יוני', '07' => 'יולי', '08' => 'אוגוסט', '09' => 'ספטמבר', '10' => 'אוקטובר', '11' => 'נובמבר', '12' => 'דצמבר');
    $thedate = date("j", $thedate) . " " . $hebmonth[date("m", $thedate)] . " " . date("Y", $thedate);
    return $thedate;
}

function limit_str($str, $max_len)
{

    if (strlen_utf8($str) > $max_len) {
//		return substr_utf8($str,0,$max_len) . " ...";
        return substr_utf8($str, 0, $max_len - strpos(strrev(substr_utf8($str, 0, $max_len)), ">") - 1) . " ...";
    } else {
        return $str;
    }
}

/**
 * Makes compressed static file for all media files(css/js)
 *
 * @param string $basePath
 * @param array $filesArr
 * @param string $filesExt
 * @param string $destFilePath
 * @param boolean $isCompress
 */
function validate_EmailAddress($email)
{
    return
        is_string($email) &&
        !empty($email) &&
		preg_match("/^[a-z0-9_-]+[a-z0-9_.-]*@[a-z0-9_-]+[a-z0-9_.-]*\.[a-z]{2,5}$/", $email);
}

function create_password_recovery($email)
{
    global $cityWallEmail;

    $static_path = $_SERVER["DOCUMENT_ROOT"] . '/_static/passwords/';
    file_put_contents($static_path . md5($email . 'hc'), time());
    chmod($static_path . md5($email . 'hc'), 0777);
    $subject = mime_encode("סיטיוול שחזור סיסמא");
    $link = 'http://' . $_SERVER['SERVER_NAME'] . '/p/rec/' . md5($email . 'hc');
    $logo_link = 'http://' . $_SERVER['SERVER_NAME'] . '/_media/images/city_wall.png';
    $html = <<<HTML
<div style="font-family:Arial;direction:rtl;direction: rtl;text-align: right;">
<img src="{$logo_link}" alt="" title="סיטיוול"/>

    <div>
    על מנת לאפס סיסמא לחץ על הלינק הבא:
    <a href="{$link}">לחץ כאן </a>
    
    </div>
<br />
<div style="font-family:Arial;direction:rtl;font-size:10px;">
במידה ולא ביקשת שחזור סיסמא אנא התעלם מהודעה זאת.
</div>

</div>
HTML;

    mail($email, $subject, $html, 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/html; charset=UTF-8' . "\r\n" . "FROM:{$cityWallEmail} \r\n");
}

function makeMediaStaticFile($basePath, $filesArr, $filesExt, $destFilePath, $xCompressArr = array())
{
    $fileContent = "";
    $staticContent = "";
    $destFilename = basename($destFilePath);
    $destFilePath_cdn = str_replace($destFilename, 'cdn.' . $destFilename, $destFilePath);
    foreach ($filesArr as $file) {
        $fileContent = file_get_contents($basePath . $file . '.' . $filesExt);
        $fileContent = preg_replace('/\t/', '', $fileContent);
        if (!in_array($file, $xCompressArr)) {
            $fileContent = preg_replace('/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/', '', $fileContent);
            $fileContent = preg_replace('/[\r\n]/', '', $fileContent);
            $fileContent = str_replace(' : ', ':', $fileContent);
            $fileContent = str_replace(') {', '){', $fileContent);
            $fileContent = str_replace(' || ', '||', $fileContent);
            $fileContent = str_replace(' && ', '&&', $fileContent);
            $fileContent = preg_replace('/ ?([\-\+])\= ?/', '\1=', $fileContent);
            $fileContent = preg_replace('/}\n\}/', '}}', $fileContent);
            if ($filesExt == 'css') {
                $fileContent = str_replace('}', "}\n", $fileContent);
            }
        }
        $staticContent .= $fileContent;
    }
    unset($fileContent);
    @unlink($destFilePath);
    $file = fopen($destFilePath, 'w');
    fwrite($file, $staticContent);
    fclose($file);
    /**
     * Rewrite _media base pathe
     */
    $staticContent = preg_replace('/\/_media\//', _project_media_cdn_path . '/_media/', $staticContent);
    @unlink($destFilePath_cdn);
    $file = fopen($destFilePath_cdn, 'w');
    fwrite($file, $staticContent);
    fclose($file);
}

function CreateThumb_front($srcdir, $srcpic, $srcext, $tmbw, $tmbh, $norw = 0, $norh = 0)
{
    $oldImg = $srcdir . "/" . $srcpic . "." . $srcext;
    $smallImg = $srcdir . "/" . $srcpic . "_thumb." . $srcext;
    $normalImg = $srcdir . "/" . $srcpic . "_preview." . $srcext;

    $picSize = getimagesize($oldImg) or die("error getting uploaded image size"); // W-$picSize[0] , H-$picSize[1], Type (numeric)
    /*	if (($picSize[1]<$picSize[0]) && ($norh==0)){
             $norw+=$norh;
             $norh=$norw-$norh;
             $norw-=$norh;
             $tmbw+=$tmbh;
             $tmbh=$tmbw-$tmbh;
             $tmbw-=$tmbh;
        }elseif (($picSize[1]>$picSize[0]) && ($norw==0)){
             $norw+=$norh;
             $norh=$norw-$norh;
             $norw-=$norh;
             $tmbw+=$tmbh;
             $tmbh=$tmbw-$tmbh;
             $tmbw-=$tmbh;
        }*/
    // do NORMAL image
    if (($picSize[1] < $picSize[0])) {
        $norh = 0;
        $tmbh = 0;
    } else {
        $norw = 0;
        $tmbw = 0;
    }

    if (($norh > 0) || ($norw > 0)) {
        if ($norh > 0) $norw = $norh * $picSize[0] / $picSize[1];
        else if ($norw > 0) $norh = $norw * $picSize[1] / $picSize[0];
        switch ($picSize[2]) {
            case 1: // GIF
                $image = imagecreatefromgif($oldImg);
                $image_p = imagecreatetruecolor($norw, $norh);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $norw, $norh, $picSize[0], $picSize[1]);
                imagegif($image_p, $normalImg);
                break;
            case 2: // JPG
                $image = imagecreatefromjpeg($oldImg);
                $image_p = imagecreatetruecolor($norw, $norh);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $norw, $norh, $picSize[0], $picSize[1]);
                imagejpeg($image_p, $normalImg);
                break;
            case 3: // PNG
                $image = imagecreatefrompng($oldImg);
                $image_p = imagecreatetruecolor($norw, $norh);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $norw, $norh, $picSize[0], $picSize[1]);
                imagepng($image_p, $normalImg);
                break;
        }
    }

    // do SMALL image
    if (($tmbh > 0) || ($tmbw > 0)) {
        if ($tmbh > 0) $tmbw = $tmbh * $picSize[0] / $picSize[1];
        else if ($tmbw > 0) $tmbh = $tmbw * $picSize[1] / $picSize[0];
        switch ($picSize[2]) {
            case 1: // GIF
                $image = imagecreatefromgif($oldImg);
                $image_p = imagecreatetruecolor($tmbw, $tmbh);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $tmbw, $tmbh, $picSize[0], $picSize[1]);
                imagegif($image_p, $smallImg);
                break;
            case 2: // JPG
                $image = imagecreatefromjpeg($oldImg);
                $image_p = imagecreatetruecolor($tmbw, $tmbh);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $tmbw, $tmbh, $picSize[0], $picSize[1]);
                imagejpeg($image_p, $smallImg);
                break;
            case 3: // PNG
                $image = imagecreatefrompng($oldImg);
                $image_p = imagecreatetruecolor($tmbw, $tmbh);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $tmbw, $tmbh, $picSize[0], $picSize[1]);
                imagepng($image_p, $smallImg);
                break;
        }
    }

    return true;
}

function checkImageThumb($basePath, $imgName, $imExt, $sizeArr)
{
    if (!file_exists($basePath . $imgName . '_thumb.' . $imExt) || !file_exists($basePath . $imgName . '_preview.' . $imExt) || $sizeArr['isForceResize']) {
        @unlink($basePath . $imgName . '_thumb.' . $imExt);
        @unlink($basePath . $imgName . '_preview.' . $imExt);
        CreateThumb_front($basePath, $imgName, $imExt,
            $sizeArr['thumb']['width'], $sizeArr['thumb']['height'],
            $sizeArr['preview']['width'], $sizeArr['preview']['height']
        );
    }
}

function xml_entity_decode($str)
{
    $xmlEntities = array(
        '&#1488;' => 'א',
        '&#1489;' => 'ב',
        '&#1490;' => 'ג',
        '&#1491;' => 'ד',
        '&#1492;' => 'ה',
        '&#1493;' => 'ו',
        '&#1494;' => 'ז',
        '&#1495;' => 'ח',
        '&#1496;' => 'ט',
        '&#1497;' => 'י',
        '&#1498;' => 'ך',
        '&#1499;' => 'כ',
        '&#1500;' => 'ל',
        '&#1501;' => 'ם',
        '&#1502;' => 'מ',
        '&#1503;' => 'ן',
        '&#1504;' => 'נ',
        '&#1505;' => 'ס',
        '&#1506;' => 'ע',
        '&#1507;' => 'ף',
        '&#1508;' => 'פ',
        '&#1509;' => 'ץ',
        '&#1510;' => 'צ',
        '&#1511;' => 'ק',
        '&#1512;' => 'ר',
        '&#1513;' => 'ש',
        '&#1514;' => 'ת'
    );
    return str_replace(array_keys($xmlEntities), array_values($xmlEntities), $str);
}

function time_diff($timestamp, $form = 'second', $is_round)
{
    $current_time = time();
    $difference = $current_time - $timestamp;
    $periods = array("second" => 1, "minute" => 60, "hour" => 3600, "day" => 86400, "week" => 604800, "month" => 2630880, "year" => 31570560, "decade" => 315705600);
    return ($is_round ? floor($difference / $periods[$form]) : $difference / $periods[$form]);
}

function getWorkFileType($filename)
{
    $fileExt = getExtension($filename);
    switch ($fileExt) {
        case 'mp3':
            return 'audio';
        case 'mpg':
        case 'avi':
        case 'flv':
            return 'video';
        case 'txt':
        case 'pdf':
        case 'doc':
            return 'document';
    }
    return 'left_arrow';
}

// mark all childs of $arr which apear in $str

function markQ($arr, $str)
{
    foreach ($arr as $value) {
        if ($value == '') {
            continue;
        }
        $str = str_replace($value, '<span class="bold">' . $value . '</span>', $str);
    }
    return $str;
}

function makeQ($arr, $fld)
{
    $qArr = array();
    foreach ($arr as $value) {
        $qArr[] = "(`" . $fld . "` LIKE '%" . $Db->make_escape($value) . "%')";
    }
    return implode(' OR ', $qArr);
}

function getBanner($areaID, $limit = 1)
{
    $Db = Database::getInstance();
    $sql = " SELECT tb_banners.*
				FROM `tb_banners` 
					INNER JOIN tb_banners_areas 
						ON (
							( tb_banners.id = tb_banners_areas.banner_id )  AND
							( tb_banners_areas.area_id = {$areaID} )
						)
				WHERE (
					( tb_banners.isactive = 'yes' )
				)
				ORDER BY RAND()
				LIMIT 0, {$limit}";
    //die($sql);
    $result_topBanner = $Db->query($sql);
    $bannerHTML = '';
    if (($result_topBanner->num_rows) > 0) {
        for (; $myrow = $Db->get_stream($result_topBanner);) {
            $tmp = '';
            if ($myrow['banner_type'] == 'code') {
                $tmp = $myrow['banner_code'];
            } else {
                if ($myrow['content'] != '') {
                    if (strtolower($myrow['extfile']) != "") {
                        $hrefContent = "<img src=\"/_media/userfiles/banners/{$myrow['id']}.{$myrow['extfile']}\" alt=\"{$myrow['title']}\" title=\"{$myrow['title']}\" />";
                    } else {
                        $hrefContent = $myrow['title'];
                    }
                    $tmp = "<a href=\"{$myrow['content']}\" target=\"_blank\">{$hrefContent}</a>";
                }
                if (strtolower($myrow['extfile']) == "swf") {
                    $tmp = "<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\" width=\"100%\" height=\"60\" id=\"banner\" align=\"middle\">
							<param name=\"allowScriptAccess\" value=\"sameDomain\" />
							<param name=\"movie\" value=\"/_media/banners/{$myrow['id']}_big.{$myrow['extfile']}\" /><param name=\"quality\" value=\"high\" />
							<embed src=\"/_media/banners/{$myrow['id']}_big.{$myrow['extfile']}\" quality=\"high\" width=\"100%\" height=\"60\" id=\"banner\" name=\"banner\" align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" pluginspage=\"http://www.macromedia.com/go/getflashplayer\" />
							</object>";
                }
            }
            $bannerHTML .= $tmp . ' ';
        }
    }
    return $bannerHTML;
}

function urlencode_fixed($url)
{
    $url = str_replace("/", "", $url);
//	$url = str_replace("\\","",$url);
    $url = str_replace(" ", "-", $url);
    $url = urlencode($url);
    $arrSearch = array(
        '%23' => '%2523',         // #
        '%5E' => '%255E',         // ^
        '%26' => '%2526',         // &
        '%22' => '%2522',         // "
        '%5C' => '%255C',         // \
        //'%2B' => '%252B',		 // +
        '%2B' => '_',         // _
        '+' => '_',
        '%3F' => '%253F',         // ?
        '%40' => '%2540',         // @
        '%2F' => '%252F',         // /
    );
    $url = str_replace(array_keys($arrSearch), array_values($arrSearch), $url);
    return ($url);
}

function array_msort($array, $cols)
{
    $colarr = array();
    foreach ($cols as $col => $order) {
        $colarr[$col] = array();
        foreach ($array as $k => $row) {
            $colarr[$col]['_' . $k] = strtolower($row[$col]);
        }
    }
    $eval = 'array_multisort(';
    foreach ($cols as $col => $order) {
        $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
    }
    $eval = substr($eval, 0, -1) . ');';
    eval($eval);
    $ret = array();
    foreach ($colarr as $col => $arr) {
        foreach ($arr as $k => $v) {
            $k = substr($k, 1);
            if (!isset($ret[$k])) $ret[$k] = $array[$k];
            $ret[$k][$col] = $array[$k][$col];
        }
    }
    return $ret;

}

function array_mixed_search($needle, $haystack)
{
    if (empty($needle) || empty($haystack)) {
        return false;
    }

    foreach ($haystack as $key => $value) {
        $exists = 0;
        foreach ($needle as $nkey => $nvalue) {
            if (!empty($value[$nkey]) && $value[$nkey] == $nvalue) {
                $exists = 1;
            } else {
                $exists = 0;
            }
        }
        if ($exists) return $key;
    }

    return false;
}

function get_gps_cor($full_address)
{
    $full_address = str_replace(' ', '+', $full_address);
    $httpRequest = "http://maps.googleapis.com/maps/api/geocode/json?address={$full_address}&sensor=true";
    $locationLatLng = file_get_contents($httpRequest);
    $locationLatLng = json_decode($locationLatLng, true);
    $locationLatLng = $locationLatLng['results'][0]['geometry']['location'];
    return '[' . $locationLatLng['lat'] . ',' . $locationLatLng['lng'] . ']';
}

function get_user_data($user_id)
{
    $Db= Database::getInstance();
    $query = "SELECT * FROM `tb_users` WHERE `id`='{$user_id}' LIMIT 1";
    $result = $Db->query($query);
    return $Db->get_stream($result);
}

function count_words($word_str, $full_text)
{
    $wordsArr = explode(' ', $word_str);
    $count = 0;
    foreach ($wordsArr AS $k => $v) {
        $count += substr_count($full_text, $v);
    }

    return $count;
}

function throw_to_home()
{
    echo $html = <<<HTML
		<script type="text/javascript">
			window.location.href='/';
		</script>
HTML;
    exit();
}

function draw_player_video($file_link, $width, $height, $thumb_link = '')
{

    $id = ($id) ? $id : 'video_' . rand(0, 10000);
    $controller = ($width > 200) ? 'bottom' : 'none';
    $thumb_link = (!$thumb_link) ? ('/_media/posts/1/0.jpg') : $thumb_link;
    $image = ($thumb_link) ? '"image": "' . $thumb_link . '",' : '';
    return $videoContent =
        '<div id=' . $id . ' class="video_m">&nbsp;</div>
	<script type="text/javascript">
		jwplayer(\'' . $id . '\').setup({
			"flashplayer": "/_media/js/plugins/player.swf",
			"file": "' . $file_link . '",
			"controlbar": "' . $controller . '",
			"stretching": "fill",
			' . $image . '
			"width": "' . $width . '",
			"height": "' . $height . '",
			
		});
	</script>';

}

function _niceTime($time)
{
    $delta = time() - $time;
    if ($delta < 60) {
        return 'לפני פחות מדקה';
    } elseif ($delta < 120) {
        return 'לפני כדקה';
    } elseif ($delta < (45 * 60)) {
        return 'לפני ' . floor($delta / 60) . ' דקות';
    } elseif ($delta < (90 * 60)) {
        return 'לפני כשעה';
    } elseif ($delta < (24 * 60 * 60)) {
        if (floor($delta / 3600) == 2) {
            return 'לפני כשעתיים';
        }
        return 'לפני ' . floor($delta / 3600) . ' שעות';
    } elseif ($delta < (48 * 60 * 60)) {
        return 'אתמול';
    } else {
        if (floor($delta / 86400) == 2) {
            return 'לפני כיומיים';
        }
        return 'לפני ' . floor($delta / 86400) . ' ימים';
    }
}

function make_insrt_sql($table_name, $table_fields)
{
    foreach ($table_fields AS $key => $value) {
        $table_fields[$key] = $Db->make_escape($value);
    }
    $query = "INSERT INTO `{$table_name}` (`" . implode("`,`", array_keys($table_fields)) . "`) VALUES ('" . implode("','", array_values($table_fields)) . "')";
    return $query;
}

function addPendingVideo($vid_youtube_id, $obj_id, $mdl_id, $wall_id, $wall_post_id, $user_id, $temp_file_path)
{
    $db_fields = array(
        'obj_id' => $obj_id,
        'mdl_id' => $mdl_id,
        'wall_id' => $wall_id,
        'wall_post_id' => $wall_post_id,
        'video_youtube_id' => $vid_youtube_id,
        'user_id' => $user_id,
        'upload_ts' => time(),
        'temp_path' => $temp_file_path,
        'status' => 0,
    );
    $Db =  Database::getInstance();
    $query = make_insrt_sql('tb_pending_video', $db_fields);
    $res = $Db->query($query) or error_log("SQL error: " . mysql_error() . " - " . $query);
    return $Db->get_insert_id();
}

function send_csv_mail($PostObj, $filename, $path)
{
    global $cityWallEmail, $categoriesFlatArr;

    $http = 'http://' . $_SERVER['HTTP_HOST'];
    $subject = mime_encode('קורות חיים -' . $PostObj->title);
    $message = "קורות חיים נשלחו";
    $category_main_link = $http . '/' . Seo::getUrl('category', $PostObj->category_id);
    $category_main_name = $categoriesFlatArr[$PostObj->category_id]['title'];
    $category_link = $http . '/' . Seo::getUrl('category', $PostObj->sub_category_id);
    $category_link_name = $categoriesFlatArr[$PostObj->sub_category_id]['title'];
    $user_link = $http . '/' . Seo::getUrl('profile', $User->id);
    $date = date('j-m-y [H:i]');
    $post_link = $http . '/' . Seo::getUrl('post', $User->id);
    if ($PostObj->buisness_id) {
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/buisness/' . get_item_dir($PostObj->buisness_id) . '/buisness-' . $PostObj->buisness_id . '.inc.php');//$buisnesArr

    }
    $message .= <<<TABLE
			<br />
			<table>
				<tr><td>שם ההודעה</td><td>{$PostObj->title}</td></tr>
				<tr><td>קטגוריה ראשית</td><td><a href="{$category_main_link}">{$category_main_name}</a></td></tr>
				<tr><td>קטגוריה</td><td><a href="{$category_link}">{$category_link_name}</a></td></tr>
				<tr><td>שם מלא</td><td>{$_REQUEST['csv_full_name']}</td></tr>
				<tr><td>טלפון</td><td>{$_REQUEST['csv_cellPhone']}</a></td></tr>
				<tr><td>אימייל</td><td>{$_REQUEST['csv_email']}</a></td></tr>
				<tr><td>תפקיד אחרון</td><td>{$_REQUEST['csv_lastPosition']}</a></td></tr>
				<tr><td>תאריך שליחה</td><td>{$date}</td></tr>
				<tr><td colspn="2"><a href="{$post_link}">להודעה</a></td></tr>
			</table>
TABLE;
    $message = <<<HTML
		<div style="direction:rtl; font-family:Arial; font-size:12px;">
		<div><img src="http://citywall.co.il/_media/images/city_wall.png" alt="citywall" title="citywall" /></div>
		<h3>קורות חיים</h3>
		<br />
		</div>
		<div style="direction:rtl">
	
		{$message}
		
		<strong></strong><br />
		
		לשאלות, פנו אלינו בדוא"ל office@citywall.co.il<br />
		או בטלפון: 09-23123123
		</div>
HTML;

    if (isset($buisnesArr['email']) && $buisnesArr['email']) {
        $PostObj->email = trim($buisnesArr['email']);
    }
    if ($filename && $path) {
        mail_attachment($filename, $path, $PostObj->email, $_REQUEST['csv_email'], mime_encode($_REQUEST['csv_fullName']), $cityWallEmail, $subject, $message);
    } else {
        mail($PostObj->email, $subject, $message, 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/html; charset=UTF-8' . "\r\n From: " . $cityWallEmail . " <" . $cityWallEmail . ">\r\n");
    }

}

function mail_attachment($filename, $path, $mailto, $from_mail, $from_name, $replyto, $subject, $message)
{
//	$mailto='gal@inmanage.co.il';
    $file = $path . $filename;
    $file_size = filesize($file);
    $handle = fopen($file, "r");
    $content = fread($handle, $file_size);
    fclose($handle);
    $content = chunk_split(base64_encode($content));
    $uid = md5(uniqid(time()));
    $name = basename($file);
    $header = "From: " . $from_name . " <" . $from_mail . ">\r\n";
    $header .= "Reply-To: " . $replyto . "\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: multipart/mixed; boundary=\"" . $uid . "\"\r\n\r\n";
    $header .= "This is a multi-part message in MIME format.\r\n";
    $header .= "--" . $uid . "\r\n";
    $header .= "Content-type:text/html; charset=UTF-8\r\n";
    $header .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $header .= $message . "\r\n\r\n";
    $header .= "--" . $uid . "\r\n";
    $header .= "Content-Type: application/octet-stream; name=\"" . $filename . "\"\r\n"; // use different content types here
    $header .= "Content-Transfer-Encoding: base64\r\n";
    $header .= "Content-Disposition: attachment; filename=\"" . $filename . "\"\r\n\r\n";
    $header .= $content . "\r\n\r\n";
    $header .= "--" . $uid . "--";
    if (mail($mailto, $subject, "", $header)) {
        return true;
    } else {
        return false;
        //die("0");
    }
}

if (!function_exists('mime_content_type')) {
    function mime_content_type($filename)
    {
        $file_info = trim(exec('file -bi ' . escapeshellarg($filename)));
        return current(explode("; ", $file_info));
    }
}
function youTubeUpload($local_file, $vid_title, $vid_description, $vid_category = 'People')
{

    $file_mime = mime_content_type($local_file);

    require_once 'Zend/Loader.php';
    Zend_Loader::loadClass('Zend_Gdata_AuthSub');
    Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
    Zend_Loader::loadClass('Zend_Gdata_YouTube');

    $authenticationURL = 'https://www.google.com/accounts/ClientLogin';
    $httpClient =
        Zend_Gdata_ClientLogin::getHttpClient(
            $username = 'social.inmanage@gmail.com',
            $password = 'salat2012',
            $service = 'youtube',
            $client = null,
            $source = 'CityWall',
            $loginToken = null,
            $loginCaptcha = null,
            $authenticationURL
        );

    $developerKey = 'AI39si6osbw6p_jk8nvLzNWkEadE_DBaV8m9FBY2VE7swDMBXJbUi3BvJu7chc-VSj_dS2cvVRObezMF5g6lOtacSrn1CzRqAw';
    $applicationId = "CityWall";
    $clientId = "370102128244.apps.googleusercontent.com";

    $yt = new Zend_Gdata_YouTube($httpClient, $applicationId, $clientId, $developerKey);
    $yt->setMajorProtocolVersion(2);
    $myVideoEntry = new Zend_Gdata_YouTube_VideoEntry();
    $filesource = $yt->newMediaFileSource($local_file);
    $filesource->setContentType($file_mime);
    $filesource->setSlug(basename($local_file));
    $myVideoEntry->setMediaSource($filesource);
    $myVideoEntry->setVideoTitle($vid_title);
    $myVideoEntry->setVideoDescription($vid_description);
    $myVideoEntry->setVideoCategory($vid_category);
    // $myVideoEntry->SetVideoTags('drop');
    $myVideoEntry->setVideoPrivate();
    $uploadUrl = 'http://uploads.gdata.youtube.com/feeds/api/users/default/uploads';

    $uploaded = false;
    $youTubeId = '';

    try {
        $newEntry = $yt->insertEntry($myVideoEntry, $uploadUrl, 'Zend_Gdata_YouTube_VideoEntry');

        $editUrl = $newEntry->getEditLink()->getHref();
        // $editUrl = 'http://gdata.youtube.com/feeds/api/users/inmanagesocial/uploads/fSfOpQnacTQ?client=370102128244.apps.googleusercontent.com';

        $urlObj = parse_url($editUrl);
        $youTubeId = end(explode("/", $urlObj['path']));

        $uploaded = true;

    } catch (Zend_Gdata_App_HttpException $httpException) {
        error_log($httpException->getRawResponseBody());
    } catch (Zend_Gdata_App_Exception $e) {
        error_log($e->getMessage());
    }
    return array('uploaded' => $uploaded, 'youTubeId' => $youTubeId);
}


function write_404_error()
{

    $dont_writeArr = array("salat2", ".png", ".jpg", ".jpeg", ".bmp", ".gif", ".msi", ".exe", ".js", ".css", ".css", ".xml", ".icon", ".txt", "_pages", "_modules", "_inc", "_ajax", ".ico", "searchQuery", "_media", "images", ".rar", ".zip", ".tooltip",);

    $db_fields = array(
        "referer" => (isset($_SERVER['HTTP_REFERER'])) ? urldecode($_SERVER['HTTP_REFERER']) : '',
        "to" => urldecode($_SERVER['REQUEST_URI']),
        "ip" => $_SERVER['REMOTE_ADDR'],
        "last_update" => time()
    );

    foreach ($dont_writeArr AS $key => $ex) {
        if (strstr($db_fields['referer'], $ex)) {
            return true;
        }
        foreach ($db_fields AS $key => $value) {
            $db_fields[$key] = $Db->make_escape($value);
        }
        $query = "INSERT INTO `tb_404` (`" . implode("`,`", array_keys($db_fields)) . "`) VALUES ('" . implode("','", array_values($db_fields)) . "')";
        mysql_unbuffered_query($query);

    }
}

function lang($key, $replace_this = "", $replace_with = "")
{
    global $translationsArr;
    //if lang array loaded
    if (!empty($translationsArr)) {
        if (empty($translationsArr[$key])) {
            return '';
        }
        $tran = (!empty($translationsArr[$key]['text']) ? stripslashes($translationsArr[$key]['text']) : '');
        if ($tran && $replace_this && $replace_with) {
            $tran = str_replace($replace_this, $replace_with, $tran);
        }

        return $tran;
    }
    //if lang array isn't loaded
    $lang = (!empty($_SESSION['lang']) ? $_SESSION['lang'] : default_lang);
    include($_SERVER['DOCUMENT_ROOT'] . "/_static/translations.{$lang}.inc.php");//$translationsArr

    if (empty($translationsArr[$key])) {
        return '';
    }
    $tran = (!empty($translationsArr[$key]['text']) ? stripslashes($translationsArr[$key]['text']) : '');
    if ($tran && $replace_this && $replace_with) {
        $tran = str_replace($replace_this, $replace_with, $tran);
    }

    return $tran;
}

function send_form_to_admins($fields, $data)
{
    global $adminEmailArr;
    foreach ($adminEmailArr as $val) {
        send_form($fields, $data, $val);
    }
}

function send_form($fields, $data, $to)
{
    global $website_url, $adminEmail;
    $subject = 'A request from "' . $data['page_title'] . '" form - ' . $website_url;
    $html = '<div style="padding:5px;font-weight:bold;">' . $subject . '</div>';
    foreach ($fields as $key => $val) {
        $html .= "<div><span style=\"width:250px;display:inline-block;padding:5px;float:left;\">{$val}:</span> <span style=\"width:700px;display:inline-block;padding:5px;\">" . (!is_array($data[$key]) ? $data[$key] : implode(',', $data[$key])) . "</span></div>";
    }
    $html = '<div>' . $html . '</div>';

    $subject = 'A request from "' . $data['page_title'] . '" form - ' . $website_url;
    // message
    $message = '
	<html dir="ltr">
	<head>
	  <title>' . $subject . '</title>
	</head>
	<body>
	  ' . $html . '
	</body>
	</html>
	';
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
    //$headers .= 'To: '.$to.' <'.$to.'>' . "\r\n";
    $headers .= 'From: ' . $website_url . ' <' . $adminEmail . '>' . "\r\n";

    // Mail it
    mail($to, $subject, $message, $headers);
}

function getCountryNameById($country_id)
{
	$Db= Database::getInstance();
    $countrySql = "SELECT country.id,countryData.title 
								FROM `tb_countries` AS country 
								LEFT JOIN `tb_countries_lang` AS countryData ON country.id=countryData.obj_id
								WHERE country.id={$country_id}";
    $countryRes = $Db->query($countrySql);
    $country = $Db->get_stream($countryRes);
    return $country['title'];
}

function getCityNameById($city_id)
{
	$Db= Database::getInstance();

	$citySql = "SELECT city.id,cityData.city_name 
								FROM `tb_cities` AS city 
								LEFT JOIN `tb_cities_lang` AS cityData ON city.id=cityData.obj_id
								WHERE city.id={$city_id}";
    $cityRes = $Db->query($citySql);
    $city = $Db->get_stream($cityRes);
    return $city['city_name'];
}


function apply301()
{
    global $Seo;

    include_once $_SERVER['DOCUMENT_ROOT'] . '/_static/redirect_301.inc.php'; //$redirects301Arr

    $url_from_encoded = 'http://' . rtrim($_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
    $url_from_decoded = urldecode($url_from_encoded);

    if (!empty($redirects301Arr[$url_from_encoded])) {
        goto301($redirects301Arr[$url_from_encoded]);
    }

    if (!empty($redirects301Arr[$url_from_decoded])) {
        goto301($redirects301Arr[$url_from_decoded]);
    }

    $url_from_decoded = $url_from_decoded . '/';
    $url_from_encoded = $url_from_encoded . '/';

    if (!empty($redirects301Arr[$url_from_encoded])) {
        goto301($redirects301Arr[$url_from_encoded]);
    }

    if (!empty($redirects301Arr[$url_from_decoded])) {
        goto301($redirects301Arr[$url_from_decoded]);
    }

    return '';
}

function goto301($url)
{
    header('HTTP/1.1 301 Moved Permanently');
    header("location: " . $url);

    exit();
}

function param($key)
{
    global $parametersArr;

    //if lang array loaded
    if (!empty($parametersArr)) {
        if (empty($parametersArr[$key])) {
            return '';
        }

        return $parametersArr[$key];
    }

    //if lang array isn't loaded
    include($_SERVER['DOCUMENT_ROOT'] . "/_static/parameters.inc.php");//$parametersArr

    if (empty($parametersArr[$key])) {
        return '';
    }

    return $parametersArr[$key];
}

function media($media_id) {
    return mediaManager::get_path($media_id);
}

/**
 * Get the get_methodsArr from the database
 *
 * @return array
 */
function get_methods_db_fallback()
{
    $returnArr = array();
    $Db = Database::getInstance();
    $query = "SELECT `method_name` FROM `tb_get_methods` where `active` = 1";
    $result = $Db->query($query);
    while($row = $Db->get_stream($result)){
        $returnArr[] = $row['method_name'];
    }

    return $returnArr;
}

/**
 * Generate a random string.
 *
 * @param int $length
 * @return string|null
 */
function str_random($length = 16)
{
    $string = '';

    while ($len = strlen($string) < $length) {
        $size = $length - $len;

        try {
            $bytes = random_bytes($size);
        } catch (Exception $e) {
            return null;
        }

        $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
    }

    return $string;
}

/**
 * Draw / return CSRF field.
 *
 * @param bool $return
 * @return string|void
 */
function csrf_field($return = false)
{
    $html = '
        <input type="hidden" name="' . Csrf::REQUEST_KEY . '" value="' . Csrf::token() . '"/>
    ';

    if ($return) {
        return $html;
    }

    echo $html;
}
