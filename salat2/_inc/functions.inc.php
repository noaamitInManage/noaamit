<?php

/**
 * function BuildCombo_V2($arr,$idIndxName,$valIndxName,$defval,$exval='-999')
 * function BuildCombo_V3($arr,$idIndxName,$valIndxName,$defval,$exval='-999')
 * function BuildRadio
 * function BuildMultiCombo
 * function BuildImgTag
 * function DateFormat
 * function GiveImageSize
 * function my_array_splice
 * function GetTopLines
 * function dateNameHeb
 * function substr_utf8
 * function strlen_utf8
 * function mkFirstShowOrder
 * function getSQLPagingArr
 * function str_to_time
 * function CreateResizedImage
 * function get_num_combo
 * function getNodeValue
 * function make_insert_sql
 * function make_update_sql
 * function draw_module_tabs [19/11/2012]
 *
 *
 **/


if (isset($_SESSION['salatLangID']) && $_SESSION['salatLangID'] == 2) {
    $yesnoArr = Array('yes' => 'כן', 'no' => 'לא');
    $yesNoArr = Array('1' => 'כן', '0' => 'לא');
} else {
    $yesnoArr = Array('yes' => 'Yes', 'no' => 'No');
    $yesNoArr = Array('1' => 'Yes', '0' => 'No');
}

$thisPage = substr($_SERVER['PHP_SELF'], strrpos($_SERVER['PHP_SELF'], "/") + 1);

//define("_PAGING_NumOfItems"					,9);	// number of rows in page
//define("_PAGING_NumOfLinks"					,3);	// number of links in page (before and after current pagenum)
//define("_PAGING_Defualt_Template"			,'<a href="?pagenum={PAGENUM}&pages={PAGES}">{CONTENT}</a>');
define("_PAGING_Next", "Next >");
define("_PAGING_Prev", "< Prev");
define("_PAGING_First", "|< First");
define("_PAGING_Last", "Last >|");

// print 'options' for selectbox
function BuildCombo($arr, $defval, $exval = '-999')
{
    if (count($arr) > 0) {
        foreach ($arr as $key => $val) {
            if ($key != $exval) {
                if (is_array($val)) {
                    // if deleted or 'is_deleted' dont exist
                    if ($val['is_deleted'] != "yes" && $val['isactive'] != 'no')
                        print "<option value=\"" . $key . "\" " . ($key == $defval ? 'selected' : '') . ">" . stripslashes($val['title']) . "</option>";
                } else {
                    print "<option value=\"" . $key . "\" " . ($key == $defval ? 'selected' : '') . ">" . stripslashes($val) . "</option>";
                }
            }
        }
    }
}

function BuildCombo_V2($arr, $idIndxName, $valIndxName, $defval, $exval = '-999')
{
    print BuildCombo_V3($arr, $idIndxName, $valIndxName, $defval, $exval);
}

function authenticate()
{
    header('WWW-Authenticate: Basic realm="CityWall.Co.IL"');
    header('HTTP/1.0 401 Unauthorized');
    // echo "You must enter a valid login ID and password to access this resource\n";
    exit;
}

function draw_image_preview($image_id)
{
    $Db = Database::getInstance();
    $query = "SELECT * FROM `tb_media` WHERE `id`='{$image_id}'";
    $result = $Db->query($query);
    $t = time();
    if ($result->num_rows) {
        $row = $Db->get_stream($result);
        return <<<HTML
	<div  style="text-align:center;">
		<img src="/_media/media/{$row['category_id']}/{$row['id']}.{$row['img_ext']}?v={$t}"  width="120">
	</div>
HTML;
    } else {
        return null;
    }


}

function BuildCombo_V3($arr, $idIndxName, $valIndxName, $defval, $exval = '-999')
{
    $output = "";
    if (count($arr) > 0) {
        foreach ($arr as $key => $val) {
            if (!is_array($val) || $val[$idIndxName] != $exval) {
                if (is_array($val)) {
                    $output .= "<option value=\"" . $val[$idIndxName] . "\" " . ($val[$idIndxName] == $defval ? 'selected' : '') . ">" . stripslashes($val[$valIndxName]) . "</option>";
                } else {
                    $output .= "<option value=\"" . $key . "\" " . ($key == $defval ? 'selected' : '') . ">" . stripslashes($val) . "</option>";
                }
            }
        }
    }
    return $output;
}

// print radio button (must have a name and checked)
function BuildRadio($arr, $name, $checked)
{
    foreach ($arr as $key => $val) {
        $val = strtolower($val);
        echo '<label for="' . $val . $name . '">' . $val . '</label><input id="' . $val . $name . '" style="vertical-align:middle" type="radio" ' . (strtolower($val) == $checked ? "checked='checked'" : '') . ' name="' . $name . '" value="' . $val . '">&nbsp;&nbsp;&nbsp;';
    }
}

// print 'options' for selectbox - from TWO LEVELS array
function BuildMultiCombo($arr, $defval, $exval)
{
    if (count($arr) > 0) {
        foreach ($arr as $indexkey => $indexval) {
            foreach ($indexval as $key => $val) {
                if ($key != $exval) {
                    if (is_array($val)) {
                        // if deleted or 'is_deleted' dont exist
                        if ($val['is_deleted'] != "yes")
                            print "<option value=\"" . $key . "\" " . ($key == $defval ? 'selected' : '') . ">" . stripslashes($val['title']) . "</option>";
                    } else {
                        print "<option value=\"" . $key . "\" " . ($key == $defval ? 'selected' : '') . ">" . stripslashes($val) . "</option>";
                    }
                }
            }
        }
    }
}

// return fully SEO Driver <img> tag
function BuildImgTag($src = '', $title = '', $extra = '')
{
    GLOBAL $_project_server_path;
    GLOBAL $_system_url;
    $tag = "<img ";
    if ((is_file($_project_server_path . $src)) && ($size = getimagesize($_project_server_path . $src))) {
        $tag .= "src='" . $_system_url . $src . "' " . $size[3] . " ";
    } else {
        $tag .= " src='' width=0 height=0 ";
    }
    $tag .= " title=\"" . htmlspecialchars($title) . "\" alt=\"" . htmlspecialchars($title) . "\" ";
    if ($extra != '') $tag .= $extra . " ";
    //$tag .= " border=0  />";
    $tag .= " />";
    return ($tag);
}

// date formating
function DateFormat($date, $type)
{
    $date = explode("-", $date);
    return ($date[2] . "-" . $date[1] . "-" . $date[0]);
}

// return error desc by error id
function printError($where = 'sys')
{
    // display error
    switch ($_GET['err']) {

        case "[EXT]":
            $thiserr = "ישנה בעייה עם הקובץ";
            break;
        case "[GEN]":
            $thiserr = "שגיאה כללית";
            break;
        default:
            $thiserr = "";
            break;
        case "[MOVE]":
            $thiserr = "לא ניתן להזיז את הקובץ הנבחר";
            break;
        case "[SIZE]":
            $thiserr = "משקל הקובץ גדול מידי";
            break;
        case "[VALIDUSER]":
            $thiserr = "תודה, נשלח אליך אימייל";
            break;
        case "[INVALIDUSER]":
            $thiserr = "כתובת אימייל נמצאת במערכת, נסה לקבל תזכורת לסיסמא";
            break;
        case "[UPDATE]":
            $thiserr = "הפרטים עודבנו בהצלחה";
            break;
        case "[MAIL]":
            $thiserr = "לא נמצא משתמש";
            break;
        case "[REMINDFAILED]":
            $thiserr = "לא נמצא משתמש <br>(<b onclick='doRemind()'><u style=cursor:pointer>Remind me my password</u></b>)";
            break;
        case "[PASS]":
            $thiserr = "הסיסמא שונתה בהצלחה";
            break;
        case "[REMINDOK]":
            $thiserr = "הסיסמא נשלחה לתיבת המייל שלך";
            break;
        case "[BUYSUB]":
            $thiserr = "בשלב זה אין ברשותך מנוי. יש לרכוש מנוי על מנת להשתמש בשירותי האתר";
            break;
        case "[EXTUP]":
            $thiserr = "סוג הקובץ אינו תואם את דרישות המערכת אבל הפרטים הטקסטואליים עודכנו בהצלחה";
            break;
        case "[SERVER]":
            $thiserr = "שגיאה כללית";
            break;
        case "[EXTART]":
            $thiserr = "המאמר עובדן ללא הקובץ";
            break;
        case "[ART]":
            $thiserr = "כדי להוסיף תמונה למאמר, יש צורך להיכנס דרך עמוד המאמר עצמו<br /> בחר קטגוריה היכנס לתמונות דרך המאמר הרצוי";
            break;
        case "[REGOK]":
            $thiserr = "ברוך הבא, הפרטים נשלחו אליך במייל";
            break;
        case "[MAILADD]":
            $thiserr = "נרשמת בהצלחה למערכת העדכונים";
            break;
        case "[MAILEXIST]":
            $thiserr = "לא נמצא משתמש";
            break;
        case "[MAILREMOVE]":
            $thiserr = "כתובת המייל הוסרה בהצלחה";
            break;

    }
    if ($thiserr != '') print '<span style="color:red; font-size:14px"><b>' . $thiserr . "</b></font>";
}

// check if only one email address was entered (by number of @)
function CheckOneEmail($adr)
{
    return (strpos($adr, "@", strpos($adr, "@") + 1) === FALSE);
}

// give img with size
function GiveImageSize($src)
{
    if ((is_file($src)) && ($size = getimagesize($src))) $tmp = " src='" . $src . "' " . $size[3] . " ";
    else $tmp = " src=''  width=0  height=0 ";
    return ($tmp);
}

// return slice of the given arr from start and by len + preserves keys
function my_array_splice($arr, $start, $len, $expkey = '', $expval = '')
{
    $tmparr = Array();
    $keys = array_keys($arr);
    $index = 0;
    foreach ($arr as $curkey => $val) {
        if ($expkey != '') { // if should check key
            if ($val[$expkey] == $expval) { // check if key == val
                if ($index >= $start) $tmparr[$curkey] = $val; // save current row (key)
                $index++; // goto next row (key)
                if ($index >= ($start + $len)) break; // end the loop
            }
        } else {
            if ($index >= $start) $tmparr[$curkey] = $val; // save current row (key)
            $index++; // goto next row (key)
            if ($index >= ($start + $len)) break; // end the loop
        }
    }
    return ($tmparr);
}

function SendMailRaw($msg, $to, $from, $subject, $vals, $chrset = 'utf-8')
{
    if (CheckOneEmail($to) && CheckOneEmail($from))
        if (mail($to, mime_encode($subject), $msg, "Content-type: text/html; charset=" . $chrset . "\r\nFrom: " . $from)) return (true);
        else return (false);
    else return (false);
}

// send mail by template
function SendMail($mailtemppath, $to, $from, $subject, $vals, $chrset = 'utf-8')
{

    if (CheckOneEmail($to) && CheckOneEmail($from)) {
        $msgbody = file_get_contents($mailtemppath);
        if ($msgbody == '') return (false);
        foreach ($vals as $parm => $value) {
            $msgbody = str_replace("[" . $parm . "]", $value, $msgbody);
        }
        if (mail($to, mime_encode($subject), $msgbody, "Content-type: text/html; charset=" . $chrset . "\r\nFrom: " . $from)) return (true);
        else return (false);
    } else return (false);
    //mail($to,$subject,$msgbody,"Content-type: text/html; charset=".$chrset."\r\nFrom: ".$from)or die("err");
}

// send mail by template - MultiByte
function MBSendMail($mailtemppath, $to, $from, $subject, $vals, $chrset = 'utf-8')
{
    if (CheckOneEmail($to) && CheckOneEmail($from)) {
        $msgbody = file_get_contents($mailtemppath);
        if ($msgbody == '') return (false);
        foreach ($vals as $parm => $value) {
            $msgbody = str_replace("[" . $parm . "]", $value, $msgbody);
        }
        mb_http_input($chrset);
        mb_http_output($chrset);
        mb_internal_encoding($chrset);
        mb_send_mail($to, $subject, $msgbody, "Content-type: text/html; charset=" . $chrset . "\r\nFrom: " . $from) or die("err");
    } else return (false);
}

function mime_encode($in_str, $chrset = 'UTF-8')
{
    $out_str = $in_str;
    if ($out_str && $chrset) {
        // define start delimimter, end delimiter and spacer
        $end = "?=";
        $start = "=?$chrset?B?";
        $spacer = "$end\n $start";
        // determine length of encoded text within chunks
        // and ensure length is even
        $length = 75 - strlen($start) - strlen($end);
        $length = floor($length / 2) * 2;
        // encode the string and split it into chunks
        // with spacers after each chunk
        $out_str = base64_encode($out_str);
        $out_str = chunk_split($out_str, $length, $spacer);
        // remove trailing spacer and
        // add start and end delimiters
        $spacer = preg_quote($spacer);
        $out_str = preg_replace("/$spacer\$/", '', $out_str);
        $out_str = $start . $out_str . $end;
    }
    return $out_str;
}

// returns top X lines from STR (by N chars per line)
function GetTopLines($str, $firstline = 0, $lines = 0, $chars = 30, &$outlines)
{
    if ($chars > 0) {
        $tmp = wordwrap($str, $chars, ' [MLBR]');
        $arr = explode("[MLBR]", $tmp);
    } else {
        //
    }
    $tmp = "";
    if ($lines == 0) $lines = COUNT($arr); // return all lines
    for ($i = $firstline; $i < $lines; $i++) {
        $tmp .= $arr[$i];
    }
    $outlines = COUNT($arr);
    return ($tmp);
}

// return Month Name in Hebrew (by number)
function dateNameHeb($num)
{
    // set dates arr
    $dates[0] = "";
    $dates[1] = "ינואר";
    $dates[2] = "פברואר";
    $dates[3] = "מרץ";
    $dates[4] = "אפריל";
    $dates[5] = "מאי";
    $dates[6] = "יוני";
    $dates[7] = "יולי";
    $dates[8] = "אוגוסט";
    $dates[9] = "ספטמבר";
    $dates[10] = "אוקטובר";
    $dates[11] = "נובמבר";
    $dates[12] = "דצמבר";
    // check given num
    $num = (int)$num;
    if (($num < 1) || ($num > 12)) $num = 0;
    // return value
    return ($dates[$num]);
}

// Returns the error with the line and file
function lErr($line, $file, $queryStr = '')
{
    die("<div align=left style=color:#0066FF;font-family:arial> sql Error:<strong>" . mysqli_error() . '</strong> <br>At File:<strong>' . $file . '</strong> <br> In Line:<strong> ' . $line . '</strong><br>Query Sent: <strong style="color:#0066FF">' . $queryStr . '</strong></div>');
    //die("error at line: ".$line);
}

// Make the query string shorter
function re($str)
{
    return $Db->make_escape($str);
}

// check and prepare variable for sql query
function SQLCheck($var, $type = 'str')
{ // 'int' , 'str' , 'yn'
    global $Db;
    if ($type == 'int') {
        return (int)$var;
    } elseif ($type == 'yn') {
        if ($var != 'yes') $var = 'no';
        return $var;
    } else {
        return $Db->make_escape($var);
    }
}

function substr_utf8($str, $from, $len)
{
    return preg_replace('#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $from . '}' . '((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,' . $len . '}).*#s', '$1', $str);
}

function strlen_utf8($str)
{
    $i = 0;
    $count = 0;
    $len = strlen($str);
    while ($i < $len) {
        $chr = ord($str[$i]);
        $count++;
        $i++;
        if ($i >= $len)
            break;

        if ($chr & 0x80) {
            $chr <<= 1;
            while ($chr & 0x80) {
                $i++;
                $chr <<= 1;
            }
        }
    }
    return $count;
}

function outputOrderingArrows($totalRows, $rowNum, $idFieldName, $rowID, $curOrderNum, $extraURL = '')
{
    $extraURL = ($extraURL == '' ? '' : "&" . $extraURL);
    $out = '<table cellpadding="0" cellspacing="0" border="0" align="center">';
    if ($rowNum > 0) {
        $out .= "<tr>
					<td align=\"center\">
						<a href=\"?act=move&dir=up&{$idFieldName}={$rowID}&curOrderNum={$curOrderNum}{$extraURL}\">
							<img src=\"../images/ordering_up.gif\" border=\"0\" alt=\"למעלה\" title=\"למעלה\">
						</a>
					</td>";
    }
    if ($rowNum < ($totalRows - 1)) {
        $out .= "<td align=\"center\">
					<a href=\"?act=move&dir=down&{$idFieldName}={$rowID}&curOrderNum={$curOrderNum}{$extraURL}\">
						<img src=\"../images/ordering_down.gif\" border=\"0\" alt=\"למטה\" title=\"למטה\">
					</a>
				</td>
			</tr>";
    }
    $out .= '</table>';
    return $out;
}

function reOrderRows($tblName, $orderFieldName, $idFieldName, $rowID, $extraSql = '', $with_fix = false)
{
    $Db = Database::getInstance();

    $itemsArr = array();
    $extraSql = ($extraSql == '' ? '' : " AND " . $extraSql);
    $newOrderNum = ($_REQUEST['dir'] == 'up' ? $_REQUEST['curOrderNum'] - 1 : $_REQUEST['curOrderNum'] + 1);
    $sql_1 = "UPDATE `{$tblName}` SET `{$orderFieldName}`='{$_REQUEST['curOrderNum']}' WHERE `{$orderFieldName}`='{$newOrderNum}'{$extraSql}";
    $sql_2 = "UPDATE `{$tblName}` SET `{$orderFieldName}`=" . $newOrderNum . " WHERE `{$idFieldName}`='{$rowID}'{$extraSql}";

    $Db->query($sql_1);
    $Db->query($sql_2);

    if ($with_fix) { // 19/04/2012
        $extraSql = str_replace('AND', '', $extraSql);
        $query = "SELECT $idFieldName,$orderFieldName FROM `{$tblName}` WHERE $extraSql ORDER BY  $orderFieldName ASC";
        $result = $Db->query($query);
        if ($result) {
            $last = array();
            $needfix = false;
            while ($row = $Db->get_stream($result)) {
                $itemsArr[] = $row;
                $needfix = (($last) && ($row[$orderFieldName] != ($last[$orderFieldName] + 1))) ? true : $needfix;
                $last = $row;
            }

            if ($needfix) {
                $i = 1;
                foreach ($itemsArr AS $key => $value) {
                    $query = "UPDATE $tblName SET $orderFieldName='{$i}'  WHERE $extraSql AND `$idFieldName`='{$value[$idFieldName]}'";
                    $Db->query($query);
                    $i++;
                }
            }
        }

    }


}

function setMaxShowOrder($tblName, $orderFieldName, $idFieldName, $rowID, $extraSelectSql = '', $extraUpdateSql = '')
{
    $Db = Database::getInstance();

    $extraSelectSql = ($extraSelectSql == '' ? '' : ' WHERE ' . $extraSelectSql);
    $extraUpdateSql = ($extraUpdateSql == '' ? '' : ' AND ' . $extraUpdateSql);
    $query = "SELECT MAX({$orderFieldName}) as `max_order_num` FROM {$tblName}{$extraSelectSql}";
    $result = $Db->query($query);

    //$result = mysql_query($query) or db_showError(__FILE__,__LINE__,$query);
    $maxOrderNum = $result->fetch_assoc()['max_order_num'] + 1;
    $query = "UPDATE `{$tblName}` SET `{$orderFieldName}`={$maxOrderNum} WHERE `{$idFieldName}`='{$rowID}'{$extraUpdateSql}";
    $result = $Db->query($query);
}

function mkFirstShowOrder($tblName, $orderFieldName, $idFieldName, $rowID, $extraCurSql = '', $extraOtherSql = '')
{
    $Db = Database::getInstance();

    $extraCurSql = ($extraCurSql == '' ? '' : ' AND ' . $extraCurSql);
    $extraOtherSql = ($extraOtherSql == '' ? '' : ' AND ' . $extraOtherSql);
    $query = "UPDATE `{$tblName}` SET `{$orderFieldName}`=0 WHERE `{$idFieldName}`='{$rowID}'{$extraCurSql}";
    $result = $Db->query($query);
    $query = "UPDATE `{$tblName}` SET `{$orderFieldName}`=`{$orderFieldName}`+1 WHERE `{$idFieldName}`!='{$rowID}'{$extraOtherSql}";
    $result = $Db->query($query);
}

function fixOrderNum($tblName, $thisOrderID, $orderFieldName = "order_num", $extraSQL = "1")
{
    $Db = Database::getInstance();
    $query = "UPDATE {$tblName} SET {$orderFieldName}={$orderFieldName}-1 WHERE (({$orderFieldName} > {$thisOrderID}) AND ({$extraSQL}))";
    $result = $Db->query($query);
}

function fixOrderNum_dynamic($tblName, $thisOrderID, $orderFieldName = "order_num", $extraSQL = "1", $catgory_del)
{
    $Db = Database::getInstance();
    $query = "UPDATE {$tblName} SET {$orderFieldName}={$orderFieldName}-1 WHERE (({$orderFieldName} > {$thisOrderID}) AND ({$extraSQL}))";
    $result = $Db->query($query);
}

function getExtension($filename)
{
    return strtolower(end(explode(".", $filename)));
}

function getSQLPagingArr($query, $tmpl = _PAGING_Defualt_Template, $first = '', $last = '', $nextPH = '', $prevPH = '', $next = _PAGING_Next, $prev = _PAGING_Prev)
{
    function _make_paging_link($tmpl, $pagenum, $pages, $content)
    {
        return (str_replace(array('{PAGENUM}', '{PAGES}', '{CONTENT}'), array($pagenum, $pages, $content), $tmpl));
    }

    $Db = Database::getInstance();

    // set RESULT
    unset($_SESSION['pages']);
    if (isset($_REQUEST['pagenum'])) {
        $_SESSION['pagenum'] = $_REQUEST['pagenum'];
        $_SERVER['REQUEST_URI'] = preg_replace("/\??\&?pagenum=[0-9]*/", "", $_SERVER['REQUEST_URI']);
    }
    if (isset($_SESSION['REQUEST_URI']) && $_SESSION['REQUEST_URI'] != $_SERVER['REQUEST_URI'] && $_SESSION['pagenum'] != $_REQUEST['pagenum']) {
        $_SESSION['pages'] = 0;
        $_SESSION['pagenum'] = 0;
        $_SESSION['REQUEST_URI'] = $_SERVER['REQUEST_URI'];
    }
    $pagenum = isset($_SESSION['pagenum']) ? $_SESSION['pagenum'] : false;
    $pages = isset($_SESSION['pages']) ? $_SESSION['pages'] : 0;
    if ($pagenum <= 0) {
        $pagenum = 1;
        $firstPageLink = 1;
        $pages = 0;
    }
    if ($pagenum > _PAGING_NumOfLinks) $firstPageLink = $pagenum - _PAGING_NumOfLinks;
    else $firstPageLink = 1;
    // calc total pages, if needed
    if ($pages <= 0) {
        /**
         * slice out the clause - from the last `FROM` before the first `JOIN` until the end...
         */
//		$position_of_first_join = (strpos(strtolower($query),'join'));
//		$query_clouse = substr($query,strrpos(substr(strtolower($query),0,$position_of_first_join),"from"));
        $tmp_query = strtolower(trim($query));
        $selectCounter = 1;
        $selectPos = 0;
        $fromPos = 0;
        $offsetPos = 1;
        while ($selectCounter > 0) {
            $selectPos = strpos($tmp_query, 'select ', $offsetPos + 1);
            $fromPos = strpos($tmp_query, 'from ', $offsetPos + 1);
            if ($selectPos === false) {
                $offsetPos = $fromPos;
                $fromPos = strpos($tmp_query, 'from ', $fromPos + 1);
                if ($fromPos !== false) {
                    $offsetPos = $fromPos;
                }
                break;
            }
            if ($selectPos < $fromPos) {
                $selectCounter++;
                $offsetPos = $selectPos;
            } else {
                $selectCounter--;
                $offsetPos = $fromPos;
            }
        }
        $query_clouse = substr($query, $offsetPos);
        // exception of UNION
        if (strpos(strtolower($query), ' union ') > 0) {
            $query_clouse = substr($query, strpos(strtolower($query), "from"));
        }
        $query_paging = "SELECT COUNT(*) as `Rows` " . $query_clouse;


        $result_paging = $Db->query($query_paging);
        if ($result_paging && $result_paging->num_rows) {
            if (strpos(strtolower($query_paging), "group by") === false)
                $rows = $result_paging->fetch_assoc()['Rows'];
            else
                $rows = $result_paging->num_rows;
        } else $rows = 0;
        if ($rows > _PAGING_NumOfItems) $pages = ceil($rows / _PAGING_NumOfItems);
        else $pages = 1;
        if ($pagenum > $pages) $pagenum = $pages;
        $lastPageLink = 0;
    }
    $_SESSION['pages'] = $pages;
    if ($pages > _PAGING_NumOfLinks && ($pagenum + _PAGING_NumOfLinks) < $pages) $lastPageLink = $pagenum + _PAGING_NumOfLinks;
    else $lastPageLink = $pages;
    if ($pagenum > 0 && $pagenum <= $pages) $start_at_page = ($pagenum - 1) * _PAGING_NumOfItems;
    else $start_at_page = 0;
    $query_limit = " LIMIT " . $start_at_page . "," . _PAGING_NumOfItems;
    $query_limited = $query . ' ' . $query_limit;
    $result = $Db->query($query_limited);

    // set PAGING
    $paging = "";
    if ($pages > 1) {
        if ($first != '' && $pages > 1 && $pagenum > 1) $paging .= _make_paging_link($tmpl, 1, $pages, $first) . ' ';
        if ($pagenum > 1) $paging .= _make_paging_link($tmpl, $pagenum - 1, $pages, $prev) . ' ';
        elseif ($prevPH != '' && $pages > 1) $paging .= _make_paging_link($tmpl, $pagenum - 1, $pages, $nextPH) . ' ';
        if ($pagenum > _PAGING_NumOfLinks + 1 && $first == '') $paging .= ' ' . _make_paging_link($tmpl, 1, $pages, 1) . ' ... ';
        for ($i = $firstPageLink; $i <= $lastPageLink; $i++) {
            if ($i == $pagenum) $paging .= " <b>{$i}</b>";
            else $paging .= ' ' . _make_paging_link($tmpl, $i, $pages, $i) . ' ';
        }
        if ($pages > 0 && $pagenum < $pages - _PAGING_NumOfLinks && $last == '') $paging .= ' ... ' . _make_paging_link($tmpl, $pages, $pages, $pages) . ' ';
        if ($pagenum < $pages) $paging .= ' ' . _make_paging_link($tmpl, $pagenum + 1, $pages, $next);
        elseif ($nextPH != '' && $pages > 1) $paging .= ' ' . _make_paging_link($tmpl, $pagenum + 1, $pages, $nextPH) . ' ';
        if ($last === true) $last = $pages;
        if ($last != '' && $pages > 1 && $pagenum < $pages) $paging .= ' ' . _make_paging_link($tmpl, $pages, $pages, $last) . ' ';
    }

    // return RESULT and PAGING
    return (array(
        'result' => $result,
        'paging' => $paging
    ));

}

function str_to_time($strTime)
{
    $strTime = explode(" ", $strTime);
    $arrLength = count($strTime);
    if ($arrLength > 0) {
        $strTime[0] = explode("-", $strTime[0]);
        if ($arrLength == 1) {
            $strTime[1][0] = $strTime[1][1] = $strTime[1][2] = 0;
        } else {
            $strTime[1] = explode(":", $strTime[1]);
        }
        return mktime($strTime[1][0], $strTime[1][1], $strTime[1][2], $strTime[0][1], $strTime[0][0], $strTime[0][2]);
    } else {
        return -1;
    }
}

function CreateResizedImage($srcdir, $trgdir, $width, $height, $quality = 98)
{
    if (!is_file($srcdir)) {
        return false;
    }

    $oldImg = $srcdir;
    $newImg = $trgdir;

    $picSize = getimagesize($oldImg); // W-$picSize[0] , H-$picSize[1], Type (numeric)

    // create new image
    if (($height > 0) || ($width > 0)) { //Keep proportion
        /*
		if($picSize[1] < $picSize[0]){
			$height = $width * $picSize[1] / $picSize[0];
		}else{
			$width = $height * $picSize[0] / $picSize[1];
		}
		*/
        // Modified by Albert on 26.04.2010 @ 17:25
        // fix for thumb by width OR height
        if (!$height) {
            $height = $width * $picSize[1] / $picSize[0];
        }
        if (!$width) {
            $width = $height * $picSize[0] / $picSize[1];
        }
        switch ($picSize[2]) {
            case 1: // GIF
                $image = imagecreatefromgif($oldImg) or db_showError(__FILE__, __LINE__);
                $image_p = imagecreatetruecolor($width, $height);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $picSize[0], $picSize[1]);
                imagegif($image_p, $newImg) or db_showError(__FILE__, __LINE__);
                break;
            case 2: // JPG
                $image = imagecreatefromjpeg($oldImg) or db_showError(__FILE__, __LINE__);
                $image_p = imagecreatetruecolor($width, $height);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $picSize[0], $picSize[1]);
                imagejpeg($image_p, $newImg, $quality) or db_showError(__FILE__, __LINE__);
                break;
            case 3: // PNG
                $image = imagecreatefrompng($oldImg) or db_showError(__FILE__, __LINE__);
                $image_p = imagecreatetruecolor($width, $height);
                imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $picSize[0], $picSize[1]);
                imagepng($image_p, $newImg) or db_showError(__FILE__, __LINE__);
                break;
        }
    }
    return true;
}

function updateStaticFile($query_array, $fullPath_fromRoot, $arrayName = 'arr', $field_to_be_key = '', $isFieldKeyAC = false, $isRemoveFieldToBeKey = false, $isNullBlank = true, $isSkipMergeSingleRow = false, $isSkipMergeSingleField = false)
{
    $Db = Database::getInstance();

    if (is_string($query_array)) {
        //$result = mysql_query($query_array) or db_showError(__FILE__,__LINE__,$query_array);
        $result = $Db->query($query_array);
        $outArray = array();
        //$cells = mysql_num_fields($result);
        $cells = $result->field_count;
        $cellsCount = $cells - ($isRemoveFieldToBeKey ? 1 : 0);
        //$rows = mysql_num_rows($result);
        $rows = $result->num_rows;
        for ($j = 0; $row = $Db->get_stream($result); $j++) {
            for ($i = 0; $i < $cells; $i++) {
                $field = mysqli_fetch_field_direct($result, $i);
                $fieldName = $field->name;

                if ($fieldName == $field_to_be_key && $isRemoveFieldToBeKey) {
                    continue;
                }
                if ($isNullBlank) {
                    $value = (is_null($row[$fieldName]) ? '' : $row[$fieldName]);
                } else {
                    $value = $row[$fieldName];
                }
                if ($field_to_be_key == '') {
                    if (!$isSkipMergeSingleField && $cellsCount == 1) {
                        $outArray[$j] = $value;
                    } else {
                        $outArray[$j][$fieldName] = $value;
                    }
                } else if ($isFieldKeyAC) {
                    if (!$isSkipMergeSingleField && $cellsCount == 1) {
                        $outArray[$row[$field_to_be_key]] = $value;
                    } else {
                        $outArray[$row[$field_to_be_key]][$fieldName] = $value;
                    }
                } else {
                    if (!$isSkipMergeSingleField && $cellsCount == 1) {
                        $outArray[$row[$field_to_be_key]][$j] = $value;
                    } else {
                        $outArray[$row[$field_to_be_key]][$j][$fieldName] = $value;
                    }
                }
            }
        }
    } elseif (is_array($query_array)) {
        $outArray = $query_array;
        $rows = count($outArray);
    }
    $fileOutput = "<" . "?\n\n";
    $fileOutput .= '$' . $arrayName . '=';
    $fileOutput .= var_export(($rows == 1 && $field_to_be_key == '' && !$isSkipMergeSingleRow ? $outArray[0] : $outArray), true);
    $fileOutput .= ";\n\n?" . ">";
    $handle = fopen($_SERVER['DOCUMENT_ROOT'] . $fullPath_fromRoot, 'w+');
    fwrite($handle, $fileOutput);
    fclose($handle);
    return $outArray;
}

function get_num_combo($end_at, $start_at = 1, $current = 0)
{
    $out_string = '';
    for ($i = $start_at; $i <= $end_at; $i++) {
        $selected = $i == $current ? 'selected="selected"' : '';
        $out_string .= '<option value="' . $i . '" ' . $selected . '>' . $i . '</option>';
    }
    return $out_string;
}

function getNodeValue($node, $str)
{
    preg_match("/<{$node}>([^<\/]+)<\/{$node}>/", $str, $arr);
    return $arr[1];
}

function make_insert_sql($table, $array)
{
    foreach ($array AS $key => $value) {
        $array[$key] = $Db->make_escape($value);
    }
    $query = "INSERT INTO `{$table}` (`" . implode('`,`', array_keys($array)) . "`) VALUES ('" . implode("','", array_values($array)) . "');";
    return $query;
}

function make_update_sql($table, $array, $where)
{
    $items = array();
    foreach ($array AS $key => $value) {
        $items[] = "`{$key}`='" . $Db->make_escape($value) . "'";
    }
    $query = "UPDATE `{$table}` SET " . implode(",", $items) . " {$where}";
    return $query;
}

function get_module_id($moduleName, $moduleDir)
{
    include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_static/moduleProcesses.inc.php');//$moduleProcessesArr
    return array_search($moduleName . '_' . $moduleDir, $moduleProcessesArr);
}


/**
 *
 * choose img from tb_media
 * @author :gal zalait
 * @param (db filed name) $filed_name; $tipsArr sizes of resolution as text in resolution box; $recursive = array( [0] => 0=with recursive drawing, 1=without recursive drawing (call function just once),[1] =>title of resolution to draw,[2]=>picture or only buttons)
 * @return html
 */
function mediaSelector($filed_name = 'main_media', $tipsArr = array(), $recursive = array(), $mediaid = '', $resolutions = true)
{
    global $row, $supplier_user;
    $Db = Database::getInstance();

    $supplier_id = ($supplier_user) ? $_SESSION["salatUserID"] : 0;

    $output = '';
    //array_unshift($mediaCategorysArr,'-- בחר --');
    if (!$row['media']) {
        $row['media'] = '';
    }
    $item_gallery_id = 0;
    $media_id = 0;
    if (isset($row[$filed_name]) && $row[$filed_name] && $recursive[2] != 'none') {  // last condition need to be checked
        $mediaid = $row[$filed_name];
    }

    if ($mediaid) {
        $query_category_id = "SELECT `category_id`,`img_ext` FROM `tb_media` WHERE `id`='{$mediaid}'";
        $row_category = $Db->get_stream($Db->query($query_category_id));
        $item_gallery_id = $row_category['category_id'];
        if (isset($recursive[1]) && !empty($recursive[1])) {
            $queryRow = "SELECT Main.category_id,Resolution_Media.resolution_media_id,Main.img_ext
									FROM `tb_media` as Main
										LEFT JOIN `tb_media_resolutions` AS Resolution_Media
											ON (Resolution_Media.resolution_media_id=Main.id)
												LEFT JOIN `tb_resolutions` AS Resolution
													ON (Resolution_Media.resolution_id=Resolution.id)
														WHERE Resolution_Media.media_id={$mediaid}
															AND Resolution.title='{$recursive[1]}'";
            $resultRow = $Db->query($queryRow);
            $lineRow = $Db->get_stream($resultRow);
            $item_resolution_gallery_id = $lineRow['category_id'];
            $resolution_media_id = $lineRow['resolution_media_id'];
        }
    }
    if (isset($recursive[2]) && ($recursive[2] == "img" || $recursive[2] == "none")) { // recursion pictures
        $name_field = $filed_name . '_recursion';
    } else {
        $name_field = $filed_name;
    }
    $but_cl = ((isset($recursive[0]) && $recursive[0]) || (isset($recursive[3]) && $recursive[3])) ? 'pic_r' : '';
    $output .= '<input type="button" value="הוסף תמונה"  class="add-image buttons ' . $but_cl . '" />
				<input type="button" value="בטל בחירה"  class="err remove-image buttons" />
	';
    $output .= '<div class="upload_div"></div>';
    $output .= <<<EOF
	&nbsp;&nbsp;
	<input type="hidden" name="{$name_field}" class="main_media"  value="{$mediaid}" />
EOF;


    $class = ($mediaid > 0) ? 'show' : 'off';

    $galleryId = ($item_gallery_id) ? $item_gallery_id : 0;
    $resol_class = (isset($recursive[1]) && !empty($recursive[1])) ? 'style="margin: 0 auto;"' : '';
    $class_res = (isset($recursive[0]) && $recursive[0]) ? 'resolution' : '';
    $output .= '<select class=" media_category_sel ' . $class . ' ' . $class_res . '" ' . $resol_class . ' field_name="' . $filed_name . '">
 				<option>-- בחר --</option>';
    /*if(isset($recursive[0])&&$recursive[0]){
        include($_SERVER['DOCUMENT_ROOT'].'/_static/m_mediaCategory.inc.php');//$mediaCategorysMArr
        foreach($mediaCategorysMArr as $galleryId => $media){
            $selected = ($galleryId == $item_resolution_gallery_id) ? 'selected="selected"' : '';
            $output .= '<option value="' . $galleryId . '"' . $selected . '>' . $media . '</option>';
        }
    }else{*/
    include($_SERVER['DOCUMENT_ROOT'] . '/_static/mediaCategory.inc.php');//$mediaCategorysArr
    if (isset($resolution_media_id) && $resolution_media_id) {
        $queryCatResolution = "SELECT category_id FROM `tb_media`
      				    	WHERE `id`='{$resolution_media_id}'";
        $resulCatResolution = $Db->query($queryCatResolution);
        $lineCatResolution = $Db->get_stream($resulCatResolution);
        $item_gallery_id_resolution = $lineCatResolution['category_id'];
    }
    if ($supplier_user == 0) {
        foreach ($mediaCategorysArr as $galleryId => $media) {
            if (isset($item_gallery_id_resolution) && $item_gallery_id_resolution) {
                $selected = ($galleryId == $item_gallery_id_resolution) ? 'selected="selected"' : '';
            } else {
                $selected = ($galleryId == $item_gallery_id) ? 'selected="selected"' : '';
            }
            $output .= '<option value="' . $galleryId . '"' . $selected . '>' . $media . '</option>';
        }
    } else {
        $querySupplier = "SELECT * FROM `tb_media_category` WHERE `user_id`='{$supplier_id}'";
        $resulSupplier = $Db->query($querySupplier);
        while ($lineSupplier = $Db->get_stream($resulSupplier)) {
            $output .= '<option value="' . $lineSupplier['id'] . '">' . $lineSupplier['title'] . '</option>';
        }
    }

    /*}*/
    $output .= '</select>	&nbsp;&nbsp;';

    if ($mediaid) {
        $gallery = (isset($recursive[1]) && $recursive) ? $item_resolution_gallery_id : $item_gallery_id;
        $queryItems = "SELECT id,title,img_ext,category_id FROM `tb_media`
      					WHERE `category_id`='{$gallery}'";
        $resultItems = $Db->query($queryItems);
        $output .= '<select  class="media_items_sel ' . $class . '" ' . $resol_class . '>';
        $count = 1;
        $flag_select = 0;
        while ($rowItems = $Db->get_stream($resultItems)) {
            $selected = '';
            $flag_title = 0;
            //get resolution picture title
            if (isset($recursive[1]) && !empty($recursive[1])) {  //change from row['resolution_media_id']
                if (isset($resolution_media_id) && $resolution_media_id > 0 && $rowItems['id'] == $resolution_media_id) {  //change from row['resolution_media_id']
                    $queryMedTitle = "SELECT title,img_ext FROM `tb_media`
      								WHERE `id`='{$resolution_media_id}'";
                    $resultMedTitle = $Db->query($queryMedTitle);
                    $lineMedTitle = $Db->get_stream($resultMedTitle);
                    $queryExt = "SELECT Media.`img_ext`
									FROM `tb_media_resolutions` AS Main
										LEFT JOIN `tb_media` AS Media ON (Main.`media_id` = Media.`id`)
											WHERE Main.`resolution_media_id` = '{$resolution_media_id}' AND Main.`media_id` = '{$mediaid}'";
                    $res = $Db->query($queryExt);
                    $line = $Db->get_stream($res);
                    $main_ext = $line['img_ext'];
                    $title = $lineMedTitle['title'];
                    $flag_title = 1;
                    $flag_select = 1;
                    $selected = 'selected="selected"';
                    $border = 'selected_item';
                    $rowItems['img_ext'] = $lineMedTitle['img_ext'];
                }
            }

            if (isset($lineRow['img_ext']) && $lineRow['img_ext']) {
                $queryExt = "SELECT img_ext FROM `tb_media`
      								WHERE `id`='{$mediaid}'";
                $resultExt = $Db->query($queryExt);
                $lineExtention = $Db->get_stream($resultExt);
                //$rowItems['img_ext']=$lineRow['img_ext'];
                $rowItems['img_ext'] = $lineExtention['img_ext'];
            }
            // GET imgExt of the picture

            $src = (isset($recursive[1]) && !empty($recursive[1])) ? "/_media/media/{$item_gallery_id}/" . $mediaid . '_' . $recursive[1] . '.' . $main_ext . '?' . time() : "/_media/media/{$item_gallery_id}/" . $mediaid . '.' . $row_category['img_ext'] . '?' . time();
            $src_size = (isset($recursive[1]) && !empty($recursive[1])) ? "/_media/media/{$item_gallery_id}/" . $mediaid . '_' . $recursive[1] . '.' . $main_ext : "/_media/media/{$item_gallery_id}/" . $mediaid . '.' . $row_category['img_ext'];
            if (!file_exists($src)) {
                $queryExt = "SELECT img_ext FROM `tb_media`
      								WHERE `id`='{$mediaid}'";
                $resultExt = $Db->query($queryExt);
                //die('<hr /><pre>' . print_r(array($recursive,$queryExt), true) . '</pre><hr />');
            }
            if (($mediaid == $rowItems['id'] && (!isset($recursive[2]))) || (($mediaid == $rowItems['id']) && (isset($recursive[2]) && $recursive[2] == 'img'))) {  // if this is the picture..
                //also draw other pictures in resolutions..
                $media_id = $rowItems['id'];
                if (!$flag_select) {
                    $selected = 'selected="selected"';
                    $border = 'selected_item';
                }
            }
            if (!$flag_title) {
                $title = ($rowItems['title']) ? $rowItems['title'] : 'no name ' . $count;
            }
            $output .= '<option value="' . $gallery . '_' . $rowItems['id'] . '_' . $rowItems['img_ext'] . '" ' . $selected . '>' . $title . '</option>';
            $count++;

        }

        $output .= '</select>';
    } else {
        $output .= '<select  class="media_items_sel ' . $class . '"></select>';
    }

    $width_thumb = 200;
    //make small size for showing
    if (!empty($src_size)) {
        $sizeArr = getimagesize($_SERVER['DOCUMENT_ROOT'] . $src_size);
    } else {
        $sizeArr[0] = '200';
        $sizeArr[1] = '200';
    }
    $proportion = ($sizeArr[0] < $sizeArr[1]) ? $sizeArr[0] / $sizeArr[1] : $sizeArr[1] / $sizeArr[0];
    if ($sizeArr[0] < $sizeArr[1]) {
        $proportion = $sizeArr[0] / $sizeArr[1];
        $height = floor($width_thumb / $proportion);
    } else {
        $proportion = $sizeArr[1] / $sizeArr[0];
        $height = floor($width_thumb * $proportion);
    }
    if ($height == 0) {
        $height = '';
    }

    $is_shown = ($src) ? '' : 'display: none;';
    $output .= '&nbsp;&nbsp;&nbsp;<br /><img style="'.$is_shown.'"  src="' . $src . '" width="' . $width_thumb . '" height="' . $height . '" class="image_con ' . $class . ' ' . $border . '" />';

    if (!$recursive[0] && $resolutions) {
        $queryCategory = "SELECT mobile FROM `tb_media_category`
      							WHERE `id`='{$item_gallery_id}'";
        $res_category = $Db->query($queryCategory);
        $line_category = $Db->get_stream($res_category);
        if ($line_category['mobile'] && $item_gallery_id && $media_id) {

            $output .= '<div  class="media_items_sel_box ' . $class . '" >';
            $queryResolutionM = "SELECT Main.*,Resolution.title, Resolution.id as res_id
								FROM `tb_media_resolutions` as Main
									LEFT JOIN `tb_resolutions` as Resolution
										ON(Resolution.id=Main.resolution_id)
      										WHERE `media_id`='{$media_id}'
      									 		AND Resolution.separate=1
      									 		 	AND Resolution.active=1";
            $res_resolutionM = $Db->query($queryResolutionM);
            $rowArr = array();
            $row2 = array();
            if ($res_resolutionM->num_rows) {
                while ($row_med = $Db->get_stream($res_resolutionM)) {
                    $rowArr[$row_med['resolution_id']] = $row_med['resolution_id'];
                    $row2[$row_med['resolution_id']] = $row_med;
                }
            }
            $queryResolution = "SELECT * FROM `tb_resolutions` WHERE active=1 AND separate=1";
            $res_resolution = $Db->query($queryResolution);
            if ($res_resolution->num_rows) {
                while ($row_res = $Db->get_stream($res_resolution)) {
                    //if resolution_id reg = res_id in media -> get picture, otherwise just buttons
                    $res_html = (in_array($row_res['id'], $rowArr)) ? draw_table_media($filed_name, $row_res, $tipsArr[$filed_name], false) : draw_table_media($filed_name, $row_res, $tipsArr[$filed_name]);
                    $output .= $res_html;
                }
            }
            $output .= '</div>';
            if ($tipsArr) {
                $output .= "<input type='hidden' class='typeArr_size' value='" . json_encode($tipsArr[$filed_name]) . "' />";
            }
        } else {
            $output .= '<div  class="media_items_sel_box ' . $class . '" >';
            $output .= '</div>';
            if ($tipsArr) {
                $output .= "<input type='hidden' class='typeArr_size' value='" . json_encode($tipsArr[$filed_name]) . "' />";
            }
        }

    }
    return $output;
}

function draw_table_media($field_name = '', $arr, $tipArr = array(), $buttonOnly = true, $media_id = '')
{
    $res_html = '';

    if ($buttonOnly) {
        $pictureHtml = mediaSelector($field_name, array(), array(0, 0, 'none', 1));
    } else {
        $pictureHtml = (isset($media_id) && $media_id) ? mediaSelector($field_name, array(), array(1, $arr['title'], 'img'), $media_id) : mediaSelector($field_name, array(), array(1, $arr['title'], 'img'));
    }
    $style = (isset($tipArr[$arr['title']]) && $tipArr[$arr['title']]) ? 'style="margin-top: 0px;"' : '';
    $res_html .= '<table class="box_table" ' . $style . '>';
    $tipHtml = (isset($tipArr[$arr['title']]) && $tipArr[$arr['title']]) ? '<tr><td>' . $tipArr[$arr['title']] . '</td></tr>' : '';
    $res_html .= '<tr><td>' . $arr['title'] . '</td></tr>
									 <tr><td>' . $tipHtml . '</td></tr>
										<tr><td rel="' . $arr['id'] . '" resolution="' . $arr['title'] . '" >
												' . $pictureHtml . '
											</td>
										</tr>';
    $res_html .= '</table>';
    return $res_html;
}

/* constraction post */
function draw_constraction_images($gallery_id)
{

    include($_SERVER['DOCUMENT_ROOT'] . '/_static/mediaGroup/mediaGroup-' . $gallery_id . '.inc.php');//$mediaGroupsArr
    $html = '<div id="constraction_images">';
    foreach ($mediaGroupsArr AS $key => $value) {
        $html .= '<img src="/_media/media/' . $value['id'] . '.' . $value['img_ext'] . '" alt="' . $value['title'] . '" title= "' . $value['title'] . '" width="300" height="150" />';
    }
    $html .= '</div>';
    return $html;
}

function getGpsField()
{
    global $row;
    if (empty($row['full_address'])) {
        $row['full_address'] = '';
    }
    if (empty($row['gps_point'])) {
        $row['gps_point'] = '';
    }
    return <<<EOF
	<input type="text" name="full_address" value="{$row['full_address']}" id="full_address" style="width:280px; text-align:left; direction:ltr;" />
	<strong>נקודת GPS:</strong>
	<input type="text" name="gps_point" value="{$row['gps_point']}" id="gps" style="width: 160px; " />
	<input type="button" class="buttons" value="מצא נקודה" 
		onclick="
			document.getElementById('gps_loader').style.display = 'inline';
			window.open('/salat2/_public/geo3.php?fast=1&point=' + document.getElementById('full_address').value
				, 'gps_win', 'width=720,height=560');
			" />
	<img style="vertical-align: middle; display:none;" src="/salat2/images/ajax-loader.gif" id="gps_loader" />
	<input type="button" class="buttons" value="הצג נקודה" 
		onclick="var child = window.open('/salat2/_public/geo3.php?point=' + document.getElementById('full_address').value
			, 'gps_win', 'width=720,height=560');
			child.focus();" />
		<input type="button" class="buttons" value="פתח בחלון חדש " onclick="window.open('http://maps.google.com/maps?q={$row['full_address']}');"  />		
		<input type="button" id="btn_cor" value="לפי קורדינטות "   />		
	
EOF;
}

function get_user_link($user_id)
{

    if (is_numeric($user_id)) {
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/users/user-' . $user_id . '.inc.php');//$userArr
        return '<a href="/salat2/site/users.php?act=new&id=' . $user_id . '">' . $userArr['first_name'] . ' ' . $userArr['last_name'] . '</a>';
    } else {
        return 'jajaj';
    }
}

function load_post_pictures($post_id, $mdl = 'posts')
{
    global $_ProcessID,$Db;
    $query = "SELECT * FROM `tb_post_pictures` WHERE `type_id`='{$_ProcessID}' AND `post_id`='{$post_id}'";

    $result = $Db->query($query);
    $html = '<table border="1">';
    while ($row = $Db->get_stream($result)) {
        $dir = get_item_dir($row['id']);
//  $smart_dir=parent::smartDirctory('/_static/post/',$_REQUEST['inner_id']);


        $html .= <<<HTML
   <tr>
      <td><img src="/_media/{$mdl}/$dir/{$row['id']}.{$row['img_ext']}" alt="{$row['title']}" title="{$row['title']}" width="150" height="100" /></td>
      <td><input type="button" class="buttons red post_picture_del" rel="{$row['id']}"  value="מחק תמונה" /></td>
   </tr>
HTML;
    }
    $html .= '</table>';

    return $html;
}

function dyanmic_field_link($id, $value)
{
    return '<a href="">' . $value . '</a>';
}

function get_item_dir($id)
{
    if ($id) {
        return ceil(intval($id) / 1000);
    }
    return 1;
}

function fix_category_level()
{
    // load from static file
    global $_Proccess_Main_DB_Table,$Db;
    include($_SERVER['DOCUMENT_ROOT'] . '/_static/categoriesLevelGroup.inc.php');//$categoriesLevelGroupArr

    foreach ($categoriesLevelGroupArr AS $key => $value) {
        $level = $value['level'];
		$Db->query("UPDATE {$_Proccess_Main_DB_Table} SET `level`='{$level}' WHERE `id`='{$key}'") or die('aaa');
        if (count($value['categories'])) {
            update_sub_cat_level($value['categories']);
        }
    }
}

function update_sub_cat_level($categories)
{
    global $_Proccess_Main_DB_Table,$Db;
    foreach ($categories AS $key => $value) {
        $level = $value['level'];
		$Db->query("UPDATE {$_Proccess_Main_DB_Table} SET `level`='{$level}' WHERE `id`='{$key}'") or die('aaa');
        if (count($value['categories'])) {
            $tmp = $value['categories'];
            update_sub_cat_level($tmp);
        }
    }
}

function smartDirctory($path, $item_id, $items_per_dir = 1000)
{  // <--- spelling mistake. Needs to be smartDirectory

    $dirNum = ceil($item_id / $items_per_dir);
    $dirPath = $path . '/' . $dirNum . '/';

    if (!is_dir($_SERVER['DOCUMENT_ROOT'] . '/' . $dirPath)) {
        @mkdir($_SERVER['DOCUMENT_ROOT'] . '/' . $dirPath, 0777);// crate a new dir
        chmod($_SERVER['DOCUMENT_ROOT'] . '/' . $dirPath, 0777);// if the first line didnt Success
    }

    return $dirPath;
}

function draw_thumb($id, $img_ext, $album_id = '')
{
    if ($img_ext) {
        $targetFile = '/_media/media/' . (($album_id) ? $album_id . '/' : '') . $id . '.' . $img_ext;
        list($w, $h) = getimagesize($_SERVER['DOCUMENT_ROOT'] . $targetFile);
        $w = ($w > 100) ? 100 : $w;
        return '<img src="' . $targetFile . '" width="' . $w . '" />';
    }
    return null;
}

function draw_module_tabs()
{
    global $languagesArr, $module_lang_id;
    if (count($languagesArr) <= 1) {
        return null;
    }
    $lang_buttonArr = array();

    $act = (isset($_REQUEST['act']) && ($_REQUEST['act'])) ? $_REQUEST['act'] : 'show';
    $id = (isset($_REQUEST['id'])) ? '&id=' . $_REQUEST['id'] : '';

    foreach ($languagesArr AS $key => $value) {
        $class = ($module_lang_id == $key) ? 'active' : '';

        $lang_buttonArr[] = '<input type="button" class=" tabs ' . $class . '" onclick="javascript: location.href=\'?act=' . $act . '&lang_id=' . $key . $id . '\';" value="' . $value['description'] . '">';
    }


    $button_html = implode('&nbsp;', $lang_buttonArr);

    return <<<HTML
	<div class="tabs_con">
		$button_html
	</div>
HTML;
}

function get_user_menu($salat_user_id)
{
    $Db = Database::getInstance();
    $modulesArr = array();
    $query = "
			SELECT Module.`id`,Module.`title`,Module.`english_title`,Module.`page`,Module.`section`,Module.`parentid`,Module.`show_order`,Module.`icon_id`  FROM `tb_sys_processes` AS Module
			   	LEFT JOIN `tb_sys_user_permissions` AS Permissions ON (
			    	Permissions.`processid`=Module.id
				)
					WHERE Permissions.`sysuserid`='{$salat_user_id}' 
							AND
								Module.`show_in_tree`=1 
								
						ORDER BY Module.section DESC,Module.parentid ASC, Module.show_order ASC
							
	";
    $result = $Db->query($query);
    $resultArr = array();
    while ($row = $Db->get_stream($result)) {
        $resultArr[$row['id']] = $row;
//        if ($row['parentid']) {
//            $modulesArr[$row['section']][$row['parentid']]['items'][$row['id']] = $row;
//        } else {
//            $modulesArr[$row['section']][$row['id']] = $row;
//        }
    }


    foreach ($resultArr as $id => $moduleArr){
        if ($moduleArr['parentid']) {
            if($resultArr[$moduleArr['parentid']]['parentid']){
                $modulesArr[$moduleArr['section']][$resultArr[$moduleArr['parentid']]['parentid']]['items'][$moduleArr['parentid']]['items'][$moduleArr['id']] = $moduleArr;
            }else{
                $modulesArr[$moduleArr['section']][$moduleArr['parentid']]['items'][$moduleArr['id']] = $moduleArr;
            }
        }else{
            $modulesArr[$moduleArr['section']][$moduleArr['id']] = $moduleArr;
        }
    }

    $tempArr['main'] = $modulesArr['main'];
    $tempArr['site'] = $modulesArr['site'];
    unset($modulesArr['site']);
    unset($modulesArr['main']);
    return array_merge($tempArr, $modulesArr);
}
function get_all_modules($lang)
{
    $Db = Database::getInstance();
    $query = "
			SELECT Module.`id`,Module.`title`,Module.`english_title`,Module.`page`,Module.`section`,Module.`parentid`,Module.`show_order`,Module.`icon_id`  FROM `tb_sys_processes` AS Module
			   	LEFT JOIN `tb_sys_user_permissions` AS Permissions ON (
			    	Permissions.`processid`=Module.id
				)
					WHERE Module.`show_in_tree`=1 
								
						ORDER BY Module.section DESC,Module.parentid ASC, Module.show_order ASC
							
	";
    $field = $lang =='he'?'title':'english_title';
    $result = $Db->query($query);
    $all_modulesArr=array();
    while ($rowArr = $Db->get_stream($result)) {
        $all_modulesArr[$rowArr['id']] = $rowArr[$field];
    }
    return $all_modulesArr;
}
function query_to_csv($query, $filename, $attachment = false, $headers = true)
{
    $Db = Database::getInstance();

    if ($attachment) {
        // send response headers to the browser
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment;filename=' . $filename);
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        echo "\xEF\xBB\xBF"; // UTF-8 BOM
        $fp = fopen('php://output', 'w');
    } else {
        $fp = fopen($filename, 'w');
    }

    $result = $Db->query($query);

    if ($headers) {
        // output header row (if at least one row exists)
        $row = $Db->get_stream($result);
        if ($row) {
            fputcsv($fp, array_keys($row));
            // reset pointer back to beginning
            mysqli_data_seek($result, 0);
        }
    }

    while ($row = $Db->get_stream($result)) {
        /* if(isset($row['last_update'])&&$row['last_update']){
             $row['last_update']=date('d-m-Y [H:i]',$row['last_update']);
         }
         if(isset($row['created_ts'])&&$row['created_ts']){
             $row['created_ts']=date('d-m-Y [H:i]',$row['created_ts']);
         }*/
        fputcsv($fp, $row);
    }

    fclose($fp);
}

function draw_sub_items($itemsArr, $tree, $module_id,$lang_file,$is_sub_side_menu = 0)
{

    $main_ul_class = ($is_sub_side_menu) ? 'sub_side_menu' : 'side_menu';
    $main_ul_style = ($is_sub_side_menu) ? 'display:none' : '';
    $html = '<ul class="'.$main_ul_class.'" style="'.$main_ul_style.'">';
    foreach ($itemsArr AS $sub_module_id => $sub_module) {
        $sub_html = '';
        $title = $lang_file == "he" ? $sub_module['title']: $sub_module['english_title'];
        if(isset($sub_module['items']) && is_array($sub_module['items'])){
            $sub_html = draw_sub_items($sub_module['items'],$tree, $sub_module_id,$lang_file,1);
        }

        $extra_html = isset($sub_module['items']) ? '<span class="m_pls" rel="' . $module_id . '" > <img src="/salat2/_public/icons/icon_224.png" height="8" width="6"> </span>' : '';

        $html .= <<<HTML
			<li class="child hidden parent_{$module_id}"><a href="/salat2/{$tree}/{$sub_module['page']}" >{$title} {$extra_html}</a>{$sub_html}</li>
HTML;


    }

    return $html .= '</ul>';

}

function create_user_menu($user_id)
{
    $modulesArr = get_user_menu($user_id);
    $file = $_SERVER['DOCUMENT_ROOT'] . '/salat2/_static/menus/sys_user-' . $user_id . '.2.inc.php';
    @unlink($file);
    file_put_contents($file, get_user_menu_html($modulesArr,'he'));

    $file = $_SERVER['DOCUMENT_ROOT'] . '/salat2/_static/menus/sys_user-' . $user_id . '.1.inc.php';
    @unlink($file);
    file_put_contents($file, get_user_menu_html($modulesArr,'en'));

    $file = $_SERVER['DOCUMENT_ROOT'] . '/salat2/_static/menus/modulesArr.2.inc.php';
    @unlink($file);
    file_put_contents($file, serialize(get_all_modules('he')));

    $file = $_SERVER['DOCUMENT_ROOT'] . '/salat2/_static/menus/modulesArr.1.inc.php';
    @unlink($file);
    file_put_contents($file, serialize(get_all_modules('en')));

    $file = $_SERVER['DOCUMENT_ROOT'] . '/salat2/_static/new_menus/sys_user_array-' . $user_id . '.inc.php';
    @unlink($file);
    updateStaticFile($modulesArr, '/salat2/_static/new_menus/sys_user_array-' . $user_id . '.inc.php', 'modulesArr');
}

function get_user_menu_html($modulesArr,$lang_file)
{
    global $_LANG, $_salat_new_show;
    include_once($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/module/mediaManager.class.inc.php');
    ob_start();
    echo '<ul id="menu-header" class="menu" >';
    foreach ($modulesArr AS $key => $value) {
        echo '<li>
			<a href="" >' . $_LANG["tree_{$key}"] . '</a>';
        if (is_array($value)) {
            echo '<ul class="submenu sub_' . $key . '">';
            foreach ($value AS $module_id => $moduleArr) {
                if (isset($moduleArr['icon_id']) && $moduleArr['icon_id']) {
                    $Media = new mediaManager($moduleArr['icon_id']);
                    $icon_src = $Media->path;
                } else {
                    if ($_salat_new_show) {
                        $icon_class = "icon_" . ($module_id % 62) . '.png';
                        //	$icon_src='/salat2/_public/icons/'.$icon_class;
                        $icon_src = '/salat2/images/small/' . $icon_class;
                    } else {
                        $icon_class = "icon_" . ($module_id % 420) . '.png';
                        $icon_src = '/salat2/_public/icons/' . $icon_class;
                    }
                }
                $href = (($moduleArr['page']) ? 'href="/salat2/' . $key . '/' . $moduleArr['page'] . '"' : 'href="#" onmouseover="this.style.cursor=\'default\';" onclick="javascript:  return false;"');
                $title = $lang_file == "he" ? $moduleArr['title']: $moduleArr['english_title'];
                if (isset($moduleArr['items']) && is_array($moduleArr['items'])) {
                    $sub_html = draw_sub_items($moduleArr['items'], $key, $module_id,$lang_file);
                    echo '<li><a ' . $href . ' ><img src="' . $icon_src . '" class="icon_menu">&nbsp&nbsp' . $title . (isset($moduleArr['items']) ? '<span class="m_pls" rel="' . $module_id . '" > <img src="/salat2/_public/icons/icon_224.png" height="8" width="6"> </span>' : '') . '</a>
								' . $sub_html . '
						</li>';
                } else { // one level
                    echo '<li><a ' . $href . ' ><img src="' . $icon_src . '" class="icon_menu">&nbsp&nbsp' . $title . '</a></li>';
                }
            }
            echo '</ul>';
        }

        echo '</li>';
    }
    echo '</ul>';
    return $html = ob_get_clean();

}

function calculateBitwiseFlagSelection($flagArrName)
{
    return array_sum($_REQUEST[$flagArrName]);
}

function drawBitwiseFlagSelection($flagOptions, $flagValue)
{
    global $$flagOptions;
    $html = '';
    foreach ($$flagOptions AS $key => $value) {
        $checked = (is_bitflag_set($flagValue, $key)) ? 'checked="checked"' : '';
        $html .= '<label><input type="checkbox" value="' . $key . '" name="' . $flagOptions . '[]" ' . $checked . ' />' . $value . ' </label><br/>';
    }
    return $html;
}

// BASED ON: http://www.php.net/manual/en/language.operators.bitwise.php#90514
function is_bitflag_set($val, $flag)
{
    return (((int)$val & (int)$flag) == (int)$fla=g);
}

function showColorPicked($color)
{
    if (!$color) {
        $color = 'FFFFFF';
    }
    return "
            <div style='position: relative;width: 36px;height: 36px;'>
                <div style='background-color: $color;position: absolute;top: 3px;left: 3px;width: 30px;height: 30px; border:#000000 1px solid;'></div>
            </div>
            ";

}

function drawColorPicker($color, $class = '', $name = '')
{
    if (!$color) {
        $color = 'FFFFFF';
    }
    $name = ($name) ? $name : 'color';
    return '<div id="colorSelection" class="' . $class . '">
						<div></div>
						<input type="hidden" name="' . $name . '" value="' . $color . '" >
					</div>';
}

function draw_genric_gallery_images($gallery_id)
{
    include($_SERVER['DOCUMENT_ROOT'] . '/_static/mediaGroup/mediaGroup-' . $gallery_id . '.inc.php');//$mediaGroupsArr
    $html = '<div id="constraction_images" class="genric_gallery">';
    foreach ($mediaGroupsArr AS $key => $value) {
        $html .= '<img src="/_media/media/' . $gallery_id . '/' . $value['id'] . '.' . $value['img_ext'] . '" alt="' . $value['title'] . '" title= "' . $value['title'] . '" width="300" height="150" />';
    }
    $html .= '</div>';
    return $html;
}

function userHasPermissions($processid)
{
	global $Db;
    $query = "SELECT * FROM tb_sys_user_permissions WHERE sysuserid=" . $_SESSION['salatUserID'] . " AND processid=" . $processid;
    $result = $Db->query($query);
    if ($result) {
        if (($result->num_rows) > 0) {
            return true;
        }
    }
    return false;
}


function sanitizeText($msg)
{
    $decoded1 = urldecode($msg);
    $decoded = htmlspecialchars_decode($decoded1);
    $stripped = strip_tags($decoded);
    $html_safe = htmlentities($stripped);
    $final_message = str_replace("\\", "%5C", $html_safe);
    return $html_safe;
}

function query2csv($query, $path, $clearFile = true)
{
    global $db;

    if ($clearFile) {
        $handle = fopen($path, 'w+');
    } else {
        $handle = fopen($path, 'a+');
    }

    fwrite($handle, "\xEF\xBB\xBF", 3);

    $res = $db->query($query);
    while ($csv_row = mysql_fetch_assoc($res)) {
        foreach ($csv_row AS $key => $csv_val) {
            $csv_row[$key] = '"' . $csv_val . '"';
        }
        fputcsv($handle, $csv_row);
    }
    fclose($handle);


    return true;
}

function array2csv($array, $path, $clearFile = true)
{
    if ($clearFile) {
        $handle = fopen($path, 'w+');
    } else {
        $handle = fopen($path, 'a+');
    }
    fwrite($handle, "\xEF\xBB\xBF", 3);
    foreach ($array AS $index => $csv_row) {
        foreach ($csv_row as $key => $value) {
            $csv_row[$key] = '="' . $value . '"';
        }
        fputcsv($handle, $csv_row);
    }
    fclose($handle);

    return true;
}

// actually executes ajax call to refresh the items list
function drawParagraphItems($paragraph_id)
{
    $html = <<<html
<script type="text/javascript">
	var paragraph_id = '{$paragraph_id}'
	$.post('/salat2/_ajax/ajax.index.php',{'file':'footer_items','act':'show','paragraph_id':paragraph_id},
		function(response){
			$("#paragraphItems").html(response.html);
	},'json');
</script>

html;
    return $html;
}

/*

-	Ordering table needs to have `group_id`, `item_id` and `order_num`
- Id and Title fields are passed as arguments
- Items table and ordering table passed as arguments

*/
function drawGroupItems($id, $ordering_table = '', $items_table = '', $group_id_field = 'id', $item_id_field = 'id', $item_title_field = 'title')
{
    global $Db;
    $ordering_table = 'tb_footer_ordering';
    $items_table = 'tb_footer_lang';
    $group_id_field = 'paragraph_id';
    $item_id_field = 'obj_id';
    $item_title_field = 'content';

    $html = <<<HTML
<div id="dv_synonyms_questions">
	<table class="genricTable" border="0">
		<thead>
			<th>מיקום</th>
			<th>שם</th>
			<th>מחק</th>
			<th>סידור</th>
		</thead>
		<tbody>
HTML;

    $sql = "SELECT Item.{$item_id_field}, Item.{$item_title_field}, Ordering.order_num FROM `{$items_table}` AS Item
	LEFT JOIN `{$ordering_table}` AS Ordering ON Item.{$item_id_field}=Ordering.item_id 
	WHERE Ordering.group_id={$id} ORDER BY Ordering.order_num";

    $result = $Db->query($sql);
    if ($total = $result->num_rows) {
        $count = 0;
        while ($row = $Db->get_stream($result)) {
            $class = ($count % 2 == 0) ? 'even' : 'odd';
            if ($count == 0) {
                $order_link = <<<ORDER
		<img src="../images/ordering_down.gif" alt="למטה" title="למטה" class="pointer order down" />
ORDER;
            } else if ($count == ($total - 1)) {
                $order_link = <<<ORDER
		<img src="../images/ordering_up.gif" alt="למעלה" title="למעלה" class="pointer order up" />	
ORDER;
            } else {
                $order_link = <<<ORDER
		<img src="../images/ordering_down.gif" alt="למטה" title="למטה" class="pointer order down" />
		<img src="../images/ordering_up.gif" alt="למעלה" title="למעלה" class="pointer order up" />	
ORDER;

            }
            $html .= <<<HTML
		<tr class="{$class}" data-item-index="{$row['order_num']}">
			<td>{$row['order_num']}</td>
			<td>{$row[$item_title_field]}</td>
			<td align="center"><span class="synonyms_del">x</span></td>
			<td>{$order_link}</td>
		</tr>
HTML;

            $count++;
        }
    }
    return $html .= <<<HTML
		</tbody>
	</table>
</div>	
HTML;
}


function drawTourDestinations()
{
    global $row,$Db;

    $html = <<<tourDestinationsHeader
<table id="destinations" border="0" cellpadding="8">
	<tr>
		<th>#</th>
		<th>עיר</th>
		<th>מחיקה</th>
	</tr>
tourDestinationsHeader;
    if (isset($row['id'])) {
        $tour_id = $row['id'];
        // search and include destinations for current tour
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/cities.search.inc.php'); // $citiesSearch
        $index = 1;
        $destinationItemsSql = "SELECT `city_id` FROM `tb_tours_destinations` WHERE `tour_id`={$tour_id}";
        $destinationItemsRes = $Db->query($destinationItemsSql);
        if ($destinationItemsRes->num_rows) {
            while ($item = $Db->get_stream($destinationItemsRes)) {
                $rowClass = $index % 2 == 0 ? 'row-odd' : 'row-even';
                $cityText = $citiesSearch[$item['city_id']];
                $html .= <<<destinations
	<tr data-cityid="{$item['city_id']}" class="{$rowClass} tourDestination">
		<td>{$index}</td>
		<td>{$cityText}</td>
		<td><a href="#">X</a></td>
	</tr>
destinations;
                $index++;
            }
        }
    }
    $html .= '</table>';
    return '<div id="destinationsHolder">' . $html . '</div>';
}


function real_escape_request()
{
    global $fieldsArr;
    $wordsArr = array(";", "%");
    $replaceWordsArr = array("&#59;", "&#37;"); // ASCII Characters (Printable)

    foreach ($_REQUEST as $key => $value) {
        $value = str_replace($wordsArr, $replaceWordsArr, $value);
        if (!is_array($value) && (!array_key_exists($key, $fieldsArr) || (array_key_exists($key, $fieldsArr) && $fieldsArr[$key]['input']['type'] != "ckhtmltext"))) {
            $_REQUEST[$key] = $value;
        }
    }
    foreach ($_POST as $key => $value) {
        $value = str_replace($wordsArr, $replaceWordsArr, $value);
        if (!is_array($value) && (!array_key_exists($key, $fieldsArr) || (array_key_exists($key, $fieldsArr) && $fieldsArr[$key]['input']['type'] != "ckhtmltext"))) {
            $_POST[$key] = $value;
        }
    }

    foreach ($_GET as $key => $value) {
        $value = str_replace($wordsArr, $replaceWordsArr, $value);
        if (!is_array($value) && (!array_key_exists($key, $fieldsArr) || (array_key_exists($key, $fieldsArr) && $fieldsArr[$key]['input']['type'] != "ckhtmltext"))) {
            $_GET[$key] = $value;
        }
    }
}

function get_sys_users()
{
    $Db = Database::getInstance();
    $query = "SELECT id, fullname FROM tb_sys_users WHERE isactive = 1";
    $result = $Db->query($query);
    while ($row = $Db->get_stream($result)) {
        $usersArr[$row['id']] = $row['fullname'];
    }

    return $usersArr;
}


function draw_connected_user_tags($user_id)
{
    global $row;

    $Db = Database::getInstance();

    $langID = $_REQUEST['lang_id'];
    $user_id = ($user_id) ? $user_id : $row['id'];
    $dynamic_tags = '';
    $dynamic_tags .= '<div class="dynamic_tags">';
    $empty_rows = false;
    if ($user_id) {
        $count_index = 0;
        $cells = array();
        $query = "SELECT Tag.`id`, Tag.`title`
					FROM `tb_users__tags` AS Link
					  LEFT JOIN `tb_tags` AS Tag
					    ON (Link.`tag_id` = Tag.`id`)
						  WHERE Link.`user_id` = {$user_id}";
        $res = $Db->query($query);

        if ($res->num_rows) {
            while ($line = $Db->get_stream($res)) {
                $delete_btn_html = '<span class="delete_row delete_connected_tag" tag_id="' . $line['id'] . '" user_id="' . $user_id . '">X</span>';
                $cells[$count_index][0] = array(
                    'type' => 'label',
                    'value' => stripslashes($line['title']),
                    'name' => '',
                    'extra' => '',
                );
                $cells[$count_index][1] = array(
                    'type' => 'html',
                    'value' => $delete_btn_html,
                    'name' => '',
                    'extra' => '',
                );
                $count_index++;
            }
            $edit_btn_html = '<input type="button" class="buttons add_connected_tag" value="Add" />';
            $cells[$count_index][0] = array(
                'type' => 'text',
                'value' => '',
                'size' => 30,
                'name' => 'user_tagsArr[' . $count_index . ']',
                'extra' => 'id="user_tags" ',
            );
            $cells[$count_index][1] = array(
                'type' => 'html',
                'value' => $edit_btn_html,
                'name' => '',
                'extra' => '',
            );
        } else {
            $empty_rows = true;
        }
    } else {
        $empty_rows = true;
    }
    if ($empty_rows) {
        $add_btn_html = '<input type="button" class="buttons add_connected_tag" value="Add" />';
        $cells[0][0] = array(
            'type' => 'text',
            'value' => '',
            'size' => 30,
            'name' => 'user_tagsArr[0]',
            'extra' => 'id="user_tags"',
        );
        $cells[0][1] = array(
            'type' => 'html',
            'value' => $add_btn_html,
            'name' => '',
            'extra' => '',
        );
    }
    $dynamic_tags .= make_dynamic_table($cells, array('Tag', 'Action'), false, false);
    $dynamic_tags .= '</div>';
    return $dynamic_tags;
}

function draw_connected_company_industries($company_id)
{
    global $row;

    $Db = Database::getInstance();

    $langID = $_REQUEST['lang_id'];
    $company_id = ($company_id) ? $company_id : $row['id'];
    $dynamic_industries = '';
    $dynamic_industries .= '<div class="dynamic_industries">';
    $empty_rows = false;
    if ($company_id) {
        $count_index = 0;
        $cells = array();
        $query = "SELECT Industry.`id`, Industry.`title`
					FROM `tb_companies__industries` AS Link
					  LEFT JOIN `tb_industries` AS Industry
					    ON (Link.`industry_id` = Industry.`id`)
						  WHERE Link.`company_id` = {$company_id}";
        $res = $Db->query($query);

        if ($res->num_rows) {
            while ($line = $Db->get_stream($res)) {
                $delete_btn_html = '<span class="delete_row delete_connected_industry" industry_id="' . $line['id'] . '" company_id="' . $company_id . '">X</span>';
                $cells[$count_index][0] = array(
                    'type' => 'label',
                    'value' => stripslashes($line['title']),
                    'name' => '',
                    'extra' => '',
                );
                $cells[$count_index][1] = array(
                    'type' => 'html',
                    'value' => $delete_btn_html,
                    'name' => '',
                    'extra' => '',
                );
                $count_index++;
            }
            $edit_btn_html = '<input type="button" class="buttons add_connected_industry" value="Add" />';
            $cells[$count_index][0] = array(
                'type' => 'text',
                'value' => '',
                'size' => 30,
                'name' => 'company_industriesArr[' . $count_index . ']',
                'extra' => 'id="company_industries" ',
            );
            $cells[$count_index][1] = array(
                'type' => 'html',
                'value' => $edit_btn_html,
                'name' => '',
                'extra' => '',
            );
        } else {
            $empty_rows = true;
        }
    } else {
        $empty_rows = true;
    }
    if ($empty_rows) {
        $add_btn_html = '<input type="button" class="buttons add_connected_industry" value="Add" />';
        $cells[0][0] = array(
            'type' => 'text',
            'value' => '',
            'size' => 30,
            'name' => 'company_industriesArr[0]',
            'extra' => 'id="company_industries"',
        );
        $cells[0][1] = array(
            'type' => 'html',
            'value' => $add_btn_html,
            'name' => '',
            'extra' => '',
        );
    }
    $dynamic_industries .= make_dynamic_table($cells, array('Industry', 'Action'), false, false);
    $dynamic_industries .= '</div>';
    return $dynamic_industries;
}

function draw_meeting_room_limited_to_companies($room_id)
{
    global $row;

    $Db = Database::getInstance();

    $langID = $_REQUEST['lang_id'];
    $room_id = ($room_id) ? $room_id : $row['id'];
    $dynamic_industries = '';
    $dynamic_industries .= '<div class="dynamic_limited_companies">';
    $empty_rows = false;
    if ($room_id) {
        $count_index = 0;
        $cells = array();
        $query = "
            SELECT
              Company.`id`, Company.`name`
            FROM `tb_meeting_rooms__limited_to_companies` AS Link
              LEFT JOIN `tb_companies` AS Company ON Company.`id` = Link.`company_id`
            WHERE Link.`meeting_room_id` = " . $room_id . "
        ";
        $res = $Db->query($query);

        if ($res->num_rows) {
            while ($line = $Db->get_stream($res)) {
                $delete_btn_html = '<span class="delete_row delete_company" data-company-id="' . $line['id'] . '" data-room-id="' . $room_id . '">X</span>';
                $cells[$count_index][0] = array(
                    'type' => 'label',
                    'value' => stripslashes($line['name']),
                    'name' => '',
                    'extra' => '',
                );
                $cells[$count_index][1] = array(
                    'type' => 'html',
                    'value' => $delete_btn_html,
                    'name' => '',
                    'extra' => '',
                );
                $count_index++;
            }
            $edit_btn_html = '<input type="button" class="buttons add_room_company" value="Add" />';
            $cells[$count_index][0] = array(
                'type' => 'text',
                'value' => '',
                'name' => 'room_companiesArr[' . $count_index . ']',
                'extra' => 'id="room_companies" ',
            );
            $cells[$count_index][1] = array(
                'type' => 'html',
                'value' => $edit_btn_html,
                'name' => '',
                'extra' => '',
            );
        } else {
            $empty_rows = true;
        }
    } else {
        $empty_rows = true;
    }
    if ($empty_rows) {
        $add_btn_html = '<input type="button" class="buttons add_room_company" value="Add" />';
        $cells[0][0] = array(
            'type' => 'text',
            'value' => '',
            'name' => 'room_companiesArr[0]',
            'extra' => 'id="room_companies"',
        );
        $cells[0][1] = array(
            'type' => 'html',
            'value' => $add_btn_html,
            'name' => '',
            'extra' => '',
        );
    }
    $dynamic_industries .= make_dynamic_table($cells, array('Company', 'Action'), false, false);
    $dynamic_industries .= '</div>';
    return $dynamic_industries;
}

function draw_meeting_rooms_features_field($meeting_room_id = 0)
{
    global $module_lang_id;

    $Db = Database::getInstance();

    $html = "";

    $current_featuresArr = array();
    if ($meeting_room_id) {
        $sql = "
            SELECT `feature_id` FROM `tb_meeting_rooms__features_link` WHERE `meeting_room_id` = {$meeting_room_id}
        ";
        $result = $Db->query($sql);
        while ($rowArr = $Db->get_stream($result)) {
            $current_featuresArr[] = $rowArr['feature_id'];
        }
    }

    $sql = "
        SELECT Main.`id`, Lang.`title` FROM `tb_meeting_rooms__features` AS Main
          LEFT JOIN `tb_meeting_rooms__features_lang` AS Lang ON Lang.`obj_id` = Main.`id`
        WHERE Lang.`lang_id` = {$module_lang_id}
        ORDER BY `order_num`
    ";
    $result = $Db->query($sql);
    while ($rowArr = $Db->get_stream($result)) {
        $checked = in_array($rowArr['id'], $current_featuresArr) ? ' checked="checked"' : '';
        $html .= '<input type="checkbox" id="cbx_feature_' . $rowArr['id'] . '" name="featuresArr[]" value="' . $rowArr['id'] . '"' . $checked . ' /> ' . '<label for="cbx_feature_' . $rowArr['id'] . '">' . $rowArr['title'] . '</label>';
    }

    return $html;
}

function get_site_floorsArr($site_id)
{
    $Db = Database::getInstance();
    $floorsArr = array();

    $sql = "
        SELECT * FROM `tb_sites__floors_order` WHERE `site_id` = {$site_id} ORDER BY `order_num`
    ";
    $result = $Db->query($sql);
    while ($row = $Db->get_stream($result)) {
        $floorsArr[$row['id']] = $row;
    }

    return $floorsArr;
}

function draw_site_floors_order_field($site_id, $replace = false)
{

    $html = '
        <table class="genricTable" id="floors_order_table">
    		<thead>
				<th class="min_th">ID</th>
				<th>Floor</th>
				<th>Order</th>
				<th>Actions</th>
		</thead>
	';

    $count = 0;

    $floorsArr = get_site_floorsArr($site_id);
    $order_num = 0;
    $total = count($floorsArr);
    foreach ($floorsArr AS $floorArr) {
        $class = ($count % 2 == 0) ? 'odd' : 'even';
        if ($count == 0) {
            $order_link = <<<ORDER
		<img src="/salat2/images/ordering_down.gif" alt="Down" title="Down" class="pointer order down" />
ORDER;
        } else if ($count == ($total - 1)) {
            $order_link = <<<ORDER
		<img src="/salat2/images/ordering_up.gif" alt="Up" title="Up" class="pointer order up" />
ORDER;
        } else {
            $order_link = <<<ORDER
		<img src="/salat2/images/ordering_down.gif" alt="Down" title="Down" class="pointer order down" />
		<img src="/salat2/images/ordering_up.gif" alt="Up" title="Up" class="pointer order up" />
ORDER;

        }

        $html .= "<tr class=\"{$class}\" data-item-index=\"{$floorArr['order_num']}\" data-floor-id=\"{$floorArr['id']}\">";
        $html .= "<td>{$floorArr['id']}</td>";
        $html .= "<td data-name=\"name\"><input type=\"text\" name=\"floorArr[floor]\" value=\"{$floorArr['floor']}\" /></td>";
        $html .= "<td>{$order_link}</td>";
        $html .= "<td><input type=\"button\" class=\"buttons updateFloor\" value=\"Update\"> <input type=\"button\" class=\"buttons red deleteFloor\" value=\"Delete\"></td>";
        $html .= '</tr>';
        $count++;
        $order_num = $floorArr['order_num'];
    }

    $order_num += 1;

    $html .= <<<HTML
		<tr>
			<td colspan="6" style="background-color:#2f2f2f;height:3px;"></td>
		</tr>
		<tr>
			<form action="" method="post" name="add_floor_form">
				<td>#</td>
				<td><input type="text" name="floorArr[floor]" /></td>
				<td><input type="hidden" value="{$order_num}" name="floorArr[order_num]" /></td>
				<td>
					<input type="button" id="addFloor" value="Add" class="buttons" />
				</td>
			</form>
		</tr>
HTML;

    $html .= "</table>";
    if (!$replace) {
        $html = '<div id="floors_order">' . $html . '</div>';
    }

    return $html;
}


function array_to_csv($csvArr, $csv_name)
{
//    header('Content-Encoding: UTF-8');
//    header('Content-type: text/csv; charset=UTF-8');
//    header('Content-Disposition: attachment;filename=' . $csv_name . ".csv");
//    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//    header('Pragma: public');

//    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    $file_path = '/_static/reports/' . $csv_name . '_' . rand(111, 999) . '.csv';
    $file_full_path = $_SERVER['DOCUMENT_ROOT'] . $file_path;

    $fp = fopen($file_full_path, 'w');

    foreach ($csvArr as $csv_rowArr) {
        fputcsv($fp, $csv_rowArr);
    }

    fclose($fp);

    return siteFunctions::get_url_by_env() . $file_path;
}


?>