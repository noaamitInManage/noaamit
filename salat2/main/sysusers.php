<?php

error_reporting(E_ALL);
ini_set('display_errors', '0');

include_once("../_inc/config.inc.php");
$_ProcessID = 2;


$query = "SELECT * FROM tb_sys_user_permissions WHERE ((sysuserid=" . $_SESSION['salatUserID'] . ") AND (processid=" . $_ProcessID . "))";
$result = $Db->query($query);
if ($result->num_rows == 0) {
    print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>You have no permissions for this process</b></big></font><br><br>\nContact the system admin to aprove permission or <a href='javascript:history.back(-1);' style='color:black;'>go back</a> to previous page.</div>";
    exit();
}

$lang_file = $_project_server_path . $_salat_path . "_static/langs/processes/" . $_ProcessID . "." . $_SESSION['salatLangID'] . ".inc.php";
if (file_exists($lang_file)) include_once($lang_file);
include_once($_project_server_path . $_salat_path . "_static/langs/" . $_SESSION['salatLangID'] . ".inc.php");
include_once($_SERVER['DOCUMENT_ROOT'] . '/salat2/_static/sys_gen.php');

/* netanel - 03/11/2013: clear sql injection from $_REQUEST array. */
real_escape_request();

$act = $_GET['act'];
if ($act == "") $act = $_POST['act'];
if ($act == "") $act = "show";

if ($act == "new") {
    include_once($_project_server_path . $_salat_path . "_trees/treebysection.php");
    include_once($_project_server_path . $_salat_path . "_static/sysprocess.inc.php");//$sysprocessArr


    function ShowProcess($list, $processes, $parentid, $pad, $i, $new_listArr, $user_id = 0)
    {
        $parent_i = ($i - 1); // save parent $i for checkbox name refference
        foreach ($processes[$parentid] as $key => $val) { // loop children
            //die('<hr /><pre>' . print_r($list, true) . '</pre><hr />');
            if ($_SESSION['salatUserID'] != 1) {
                if (in_array($val, $new_listArr)) {
                    print "<span style='padding:0 " . $pad . "px;'>&nbsp;</span>";
                    // show check box
                    print "<input type=checkbox name=\"chkPerm[" . $i . "]\" id=\"chk" . $i . "\" value=\"" . $val . "\"";
                    if (isset($user_id) && $user_id > 0) {
                        //get from DB users proccesses
                        $user_processArr = get_user_processes($user_id);
                        if ($list[$parentid][$val]['sysuserid'] != '' && in_array($val, $user_processArr)) {
                            print " checked";
                        }
                    } else {    // if has permission
                        if ($list[$parentid][$val]['sysuserid'] != '') print " checked"; //change the condition!
                    }
                    // clicking child is clicking parent
                    if (($parentid != '0') && ($parentid != '-1')) print " onclick=\"if (this.checked) document.getElementById('chk" . $parent_i . "').checked=true;\"";
                    print ">";
                    print "<label for=\"chk" . $i . "\">" . $list[$parentid][$val]['title'] . "</label><br>";
                    $i++;
                }
            } else if ($_SESSION['salatUserID'] == 1) {
                print "<span style='padding:0 " . $pad . "px;'>&nbsp;</span>";
                // show check box
                print "<input type=checkbox name=\"chkPerm[" . $i . "]\" id=\"chk" . $i . "\" value=\"" . $val . "\"";
                // if has permission
                if ($list[$parentid][$val]['sysuserid'] != '') print " checked";
                // clicking child is clicking parent
                if (($parentid != '0') && ($parentid != '-1')) print " onclick=\"if (this.checked) document.getElementById('chk" . $parent_i . "').checked=true;\"";
                print ">";
                print "<label for=\"chk" . $i . "\">" . $list[$parentid][$val]['title'] . "</label><br>";
                $i++;
            }
            $i = ShowProcess($list, $processes, $val, $pad + 20, $i, $new_listArr, $user_id);
        }
        return ($i);
    }

    // show new form (with existing details)
    if ($_SESSION['salatUserID'] != 1) {
        unset($sysprocessArr['main']['main']['0'][current(array_keys($sysprocessArr['main']['main']['0'], 19))]);
        unset($sysprocessArr['main']['main']['0'][current(array_keys($sysprocessArr['main']['main']['0'], 36))]);
    }
    if ($_GET['id'] != "") {
        if (!is_numeric($_GET['id'])) {
            Header("Location: sysusers.php");
            exit();
        } else {
            // yes id - load permissions
            $query = "SELECT * FROM tb_sys_users WHERE (id=" . $_GET['id'] . ")";
            $result = $Db->query($query);
            if ($result->num_rows > 0) {
                $item = $Db->get_stream($result);
                if ($_SESSION['salatUserID'] != 1) {
                    $query = "SELECT tb_sys_processes.id,tb_sys_processes.title,tb_sys_processes.parentid,
				            tb_sys_user_permissions.sysuserid,tb_sys_processes.section,tb_sys_processes.tree
				              FROM tb_sys_processes
				                 INNER JOIN tb_sys_user_permissions ON (tb_sys_processes.id=tb_sys_user_permissions.processid)
				                    AND (tb_sys_user_permissions.sysuserid=" . $_SESSION['salatUserID'] . "  AND tb_sys_user_permissions.sysuserid  IS NOT NULL)
				                       ORDER BY tb_sys_processes.section,tb_sys_processes.tree";
                } else {
                    $query = "SELECT tb_sys_processes.id,tb_sys_processes.title,tb_sys_processes.parentid,
				            tb_sys_user_permissions.sysuserid,tb_sys_processes.section,tb_sys_processes.tree
				              FROM tb_sys_processes
				                 LEFT OUTER JOIN tb_sys_user_permissions ON (tb_sys_processes.id=tb_sys_user_permissions.processid)
				                    AND (tb_sys_user_permissions.sysuserid=" . $_GET['id'] . ")
				                       ORDER BY tb_sys_processes.section,tb_sys_processes.tree";

                }
                $result = $Db->query($query);
                $listcount = $result->num_rows;

                for ($i = 0; $i <= $listcount; $i++) {
                    $row = $Db->get_stream($result);
                    $list[$row['section']][$row['tree']][$row['parentid']][$row['id']] = Array('title' => $row['title'], 'sysuserid' => $row['sysuserid']);
                    //$list[$i] = $Db->get_stream($result);
                }
            } else {
                // no user - show all users

                Header("Location: sysusers.php");
                exit();
            }
        }
    } else {
        // no id - new user, get processes table
        if ($_SESSION['salatUserID'] != 1) {
            $query = "
					SELECT id,title,parentid,section,tree
						FROM tb_sys_processes
							INNER JOIN tb_sys_user_permissions ON (tb_sys_processes.id=tb_sys_user_permissions.processid)
								AND (tb_sys_user_permissions.sysuserid={$_SESSION['salatUserID']} AND tb_sys_user_permissions.sysuserid  IS NOT NULL )
									ORDER BY section,tree";
        } else {
            $query = "SELECT id,title,parentid,section,tree
						FROM tb_sys_processes
							ORDER BY section,tree";
        }
        $result = $Db->query($query);
        if ($result->num_rows > 0) {
            $listcount = $result->num_rows;
            for ($i = 0; $i < $listcount; $i++) {
                $row = $Db->get_stream($result);
                $list[$row['section']][$row['tree']][$row['parentid']][$row['id']] = Array('title' => $row['title']);
                //$list[$i] = $Db->get_stream($result);
            }
        } else {
            // no processes
            die("NO PROCESSES IN SYSTEM!");
        }
    }
} else if ($act == "after") {
    error_reporting(E_ERROR);
    ini_set('display_errors', '1');
    // do action: save new or update existing
    // txtFName, txtUName, txtEmail, txtPass, cmbIsActive, chkPerm[0...] -- ||
    $perms = $_POST['chkPerm'];
    $permscount = $_POST['permscount'];
    if ($_POST['id'] != "") {
        if (!is_numeric($_POST['id'])) {
            Header("Location: sysusers.php?act=new&err=2");
            exit();
        } else {
            // yes id - update existing
            $query = "UPDATE tb_sys_users SET fullname='" . addslashes($_POST['txtFName']) . "', username='" . addslashes($_POST['txtUName']) . "', email='" . $_POST['txtEmail'] . "', isactive='" . $_POST['cmbIsActive'] . "' WHERE (id=" . $_POST['id'] . ")";
            $Db->query($query);// or die ("error updating user");
            if ($_POST['txtPass'] != '') {
                $query = "UPDATE tb_sys_users SET password='" . hash("sha256", "_in" . $_POST['txtPass'] . "ge_") . "' WHERE (id=" . $_POST['id'] . ")";
                $Db->query($query);// or die ("error updating user");
            }
            $Db->query("BEGIN");// or die ("error updating permissions (1)");
            $Db->query("DELETE FROM tb_sys_user_permissions WHERE (sysuserid=" . $_POST['id'] . ")");// or die ("error updating permissions (2)");
            for ($i = 0; $i < $permscount; $i++) {
                if (is_numeric($perms[$i]))
                    $Db->query("INSERT INTO tb_sys_user_permissions (sysuserid,processid) VALUES (" . $_POST['id'] . "," . $perms[$i] . ")");// or die ("error updating permissions (4-".$i.")");
            }
            $Db->query("COMMIT");// or die ("error updating permissions (3)");
            create_user_menu($_POST['id']);
            Header("Location: sysusers.php?act=static&id=" . $_POST['id']);
            exit();
        }
    } else {
        // no id - just add new
        $query = "INSERT INTO tb_sys_users (id,fullname,username,password,email,isactive) VALUES (null,'" . addslashes($_POST['txtFName']) . "','" . addslashes($_POST['txtUName']) . "','" . hash("sha256", "_in" . $_POST['txtPass'] . "ge_") . "','" . $_POST['txtEmail'] . "','" . $_POST['cmbIsActive'] . "')";
        $Db->query($query);
        $newuserid = $Db->get_insert_id();
        $Db->query("BEGIN");// or die ("error creating permissions (1)");
        for ($i = 0; $i <= COUNT($perms); $i++) {
            //	if (is_numeric($perms[$i]))
            //	$Db->query("INSERT INTO tb_sys_user_permissions (sysuserid,processid) VALUES (".$newuserid.",".$perms[$i].")") or die ("error creating permissions (3-".$i.")");
        }
        foreach ($perms AS $key => $value) {
            $Db->query("INSERT INTO tb_sys_user_permissions (sysuserid,processid) VALUES (" . $newuserid . "," . $value . ")");
        }
        $Db->query("COMMIT");// or die ("error creating permissions (2)");
        create_user_menu($newuserid);
        Header("Location: sysusers.php?act=static&id=" . $newuserid);
        exit();
    }
} else if ($act == "del") {
    // delete selected user
    if (($_GET['id'] == "") || (!is_numeric($_GET['id']))) {
        Header("Location: sysusers.php");
        exit();
    }
    $query = "DELETE FROM tb_sys_user_permissions WHERE (sysuserid=" . $_GET['id'] . ")";
    $Db->query($query);// or die ("error deleting user(1)");
    $query = "DELETE FROM tb_sys_users WHERE (id=" . $_GET['id'] . ")";
    $Db->query($query);// or die ("error deleting user(2)");
    // delete permissions file
    @unlink($_project_server_path . $_salat_path . "_trees/permissions/treebyuser" . $_GET['id'] . ".php");
    // delete static tree
    include_once($_project_server_path . $_salat_path . "_trees/treebysection.php");
    foreach ($sectionsCombo as $key_sec => $val_sec) {
        foreach ($val_sec as $key_tree => $val_tree) {
            @unlink($_project_server_path . $_salat_path . "_static/trees/" . $key_sec . "." . $key_tree . "." . $_GET['id'] . ".tree.php");
        }
    }
    create_user_menu($_GET['id']);
    Header("Location: sysusers.php?act=static");
    exit();
} else if ($act == "static") {
    // update users file

    $query = "SELECT * FROM tb_sys_users";
    $result = $Db->query($query);
    $fileStr = "";
    while ($row = $Db->get_stream($result)) {
        if ($fileStr != '') $fileStr .= ",\n";
        $fileStr .= $row['id'] . " => Array('fullname'=>'" . addslashes($row['fullname']) . "','username'=>'" . $row['username'] . "','password'=>'" . $row['password'] . "','email'=>'" . $row['email'] . "','isactive'=>'" . $row['isactive'] . "')";
    }
    $fileStr = "<?php  \n\$sysusersArr = Array(\n$fileStr\n);\n ?>";
    @unlink($_project_server_path . $_salat_path . "_static/sysusers.inc.php");
    $file = fopen($_project_server_path . $_salat_path . "_static/sysusers.inc.php", 'w');
    fwrite($file, $fileStr);
    fclose($file);
    if ((int)$_GET['id'] > 0) {
        // update permissions file
        $query = "SELECT tb_sys_processes.id,tb_sys_processes.section,tb_sys_processes.tree,tb_sys_processes.parentid,tb_sys_processes.show_order
					FROM tb_sys_processes RIGHT OUTER JOIN tb_sys_user_permissions ON (tb_sys_processes.id=tb_sys_user_permissions.processid) AND (tb_sys_user_permissions.sysuserid=" . (int)$_GET['id'] . ")
					WHERE (NOT ISNULL(tb_sys_processes.id))";
        $result = $Db->query($query);
        $fileStr = "";
        while ($row = $Db->get_stream($result)) {
            $fileStr .= "\$treebyuserArr['" . $row['section'] . "']['" . $row['tree'] . "']['" . $row['parentid'] . "'][" . ($row['show_order'] ? $row['show_order'] : '') . "] = '" . $row['id'] . "'; \n";
        }
        $fileStr = "<?php  \n\$treebyuserArr = Array(); \n$fileStr ?>";
        @unlink($_project_server_path . $_salat_path . "_trees/permissions/treebyuser" . (int)$_GET['id'] . ".php");
        $file = fopen($_project_server_path . $_salat_path . "_trees/permissions/treebyuser" . (int)$_GET['id'] . ".php", 'w');
        fwrite($file, $fileStr);
        fclose($file);
    }
    // return
    header("location: sysusers.php?menurefresh=yes&tid=" . $_GET['id']);
    exit();
} else {
    include_once($_project_server_path . $_salat_path . "_static/sysusers.inc.php");
}
function get_user_processes($user_id)
{
    $Db = Database::getInstance();

    $query = "SELECT processid FROM tb_sys_user_permissions
							WHERE tb_sys_user_permissions.sysuserid={$user_id}";
    $res = $Db->query($query);
    $user_processArr = array();
    while ($line = $Db->get_stream($res)) {
        $user_processArr[] = $line['processid'];
    }
    return $user_processArr;
}

include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_info.inc.php');
?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_ProcessLang['salat_dir']; ?>">
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
    <link rel="StyleSheet" href="../_public/main.css" type="text/css">
    <script language="JavaScript" src="../_public/formvalid.js"></script>
    <script type="text/javascript" src="/salat2/_public/jquery1.8.min.js"></script>
    <script language="JavaScript"> if (window.parent == window) location.href = '../frames.php'; </script>
    <script language="javascript">
        function doDel(lid) {
            if (confirm("<?php echo $_ProcessLang['delete_question'];?>"))
                document.location = "?act=del&id=" + lid;
        }
        function doNew() {
            if (document.frmNew.txtUName.value != "") {
                document.frmNew.submit();
            } else {
                alert("<?php echo $_ProcessLang['must_name'];?>");
                document.frmNew.txtUName.focus();
            }
        }
    </script>
</head>
<?php echo $_salat_style; ?>
<body onLoad="top.framMenu.NodeByKey('sysusers','click')">
<? include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_menu.inc.php'); ?>
<div class="maindiv">
    <?php if ($act == "show") { ?>
        <span style="color:<?php echo $_color_normal; ?>;font-size:16px;font-weight:bold;"><?php echo $_ProcessLang['main_title']; ?></span> |
        <a href="?act=new" style="color:<?php echo $_color_normal; ?>;"><?php echo $_ProcessLang['add_new']; ?></a>
        <br><br>
        <?php if ($_GET['err'] != "") printError(); ?>
        <table width="500" border="0" cellpadding="3" cellspacing="1"
               style="border:dotted 1px <?php echo $_color_normal; ?>;">
            <tr style="color:<?php echo $_color_normal; ?>;font-weight:bold;">
                <td align=center><b><?php echo $_ProcessLang['lst_id']; ?></b></td>
                <td align=center><b><?php echo $_ProcessLang['lst_name']; ?></b></td>
                <td align=center><b><?php echo $_ProcessLang['lst_uname']; ?></b></td>
                <td align=center><b><?php echo $_ProcessLang['lst_email']; ?></b></td>
                <td align=center><b><?php echo $_ProcessLang['lst_active']; ?></b></td>
                <td align=center><b><?php echo $_ProcessLang['lst_edit']; ?></b></td>
                <td align=center><b><?php echo $_ProcessLang['lst_del']; ?></b></td>
            </tr>
            <?php foreach ($sysusersArr as $key => $val) {
                if ($key == 1 && ($_SESSION['salatUserID'] != 1)) {
                    continue;
                }
                ?>
                <tr onMouseOver="this.className='mover';" onMouseOut="this.className='mout';"> <!-- DD4B2E -->
                    <td align=center><?php echo $key; ?></td>
                    <td align=center><?php echo stripslashes($val['fullname']); ?></td>
                    <td align=center><?php echo $val['username']; ?></td>
                    <td align=center dir=ltr><?php echo $val['email']; ?></td>
                    <td align=center><img src='../images/<?php echo $val['isactive']; ?>.gif' border=0 align=absmiddle>
                    </td>
                    <td align=center><a href="?act=new&id=<?php echo $key; ?>"><img src="../images/edit.png" border=0
                                                                                    align=absmiddle height=16 width=16></a>
                    </td>
                    <td align=center><a href="javascript:doDel(<?php echo $key; ?>);"><img src="../images/delete.png"
                                                                                           border=0 align=absmiddle
                                                                                           height=16 width=16></a></td>
                </tr>
            <?php }
            if (COUNT($sysusersArr) == 0) print "<tr><td colspan=7 align=center>" . $_ProcessLang['src_nores'] . "</td></tr>"; ?>
        </table>
    <?php } else if ($act == "new") { ?>
        <span style="color:<?php echo $_color_normal; ?>;font-size:16px;font-weight:bold;"><?php echo $_ProcessLang['main_title']; ?></span> |
        <a href="?act=show" style="color:<?php echo $_color_normal; ?>;"><?php echo $_ProcessLang['show_all']; ?></a>
        <br><br>
        <?php if ($_GET['err'] != "") printError(); ?>
        <form name="frmNew" action="?act=after" method="post">
            <input type=hidden name="id" value="<?php print $item['id']; ?>">
            <input type=hidden name="permscount" value="<?php print $listcount; ?>">
            <table width="80%" border="0" cellpadding="3" cellspacing="1"
                   style="border:dotted 1px <?php echo $_color_normal; ?>;">
                <tr>
                    <td colspan=2 style="color:<?php echo $_color_normal; ?>;">
                        <b><?php echo $_ProcessLang['cat_details']; ?></b></td>
                </tr>
                <tr>
                    <td style="color:<?php echo $_color_normal; ?>;"><img
                                src="../images/item_<?php echo($_ProcessLang['salat_dir'] == 'rtl' ? 2 : 1); ?>.gif"
                                width="4" height="7" border="0"
                                align="absmiddle"/>&nbsp;<?php echo $_ProcessLang['frm_name']; ?>:
                    </td>
                    <td><input type=text name="txtFName" id="txtFName"
                               value="<?php print htmlspecialchars($item['fullname']); ?>"
                               style="border: solid 1px <?php echo $_color_normal; ?>;"
                               dir="<?php echo $_LANG['salat_dir']; ?>"/></td>
                </tr>
                <tr>
                    <td style="color:<?php echo $_color_normal; ?>;"><img
                                src="../images/item_<?php echo($_ProcessLang['salat_dir'] == 'rtl' ? 2 : 1); ?>.gif"
                                width="4" height="7" border="0"
                                align="absmiddle"/>&nbsp;<?php echo $_ProcessLang['frm_uname']; ?>:
                    </td>
                    <td><input type=text name="txtUName" id="txtUName"
                               value="<?php print htmlspecialchars($item['username']); ?>"
                               style="border: solid 1px <?php echo $_color_normal; ?>;"
                               dir="<?php echo $_LANG['salat_dir']; ?>"/></td>
                </tr>
                <tr>
                    <td style="color:<?php echo $_color_normal; ?>;"><img
                                src="../images/item_<?php echo($_ProcessLang['salat_dir'] == 'rtl' ? 2 : 1); ?>.gif"
                                width="4" height="7" border="0"
                                align="absmiddle"/>&nbsp;<?php echo $_ProcessLang['frm_pass']; ?>:
                    </td>
                    <td><input type=text name="txtPass" id="txtPass" value=""
                               style="border: solid 1px <?php echo $_color_normal; ?>;"
                               dir="<?php echo $_LANG['salat_dir']; ?>"/>&nbsp;<?php echo $_ProcessLang['hint_pass']; ?>
                    </td>
                </tr>
                <tr>
                    <td style="color:<?php echo $_color_normal; ?>;"><img
                                src="../images/item_<?php echo($_ProcessLang['salat_dir'] == 'rtl' ? 2 : 1); ?>.gif"
                                width="4" height="7" border="0"
                                align="absmiddle"/>&nbsp;<?php echo $_ProcessLang['frm_email']; ?>:
                    </td>
                    <td><input type=text name="txtEmail" id="txtEmail" value="<?php print $item['email']; ?>"
                               style="border: solid 1px <?php echo $_color_normal; ?>;"
                               dir="<?php echo $_LANG['salat_dir']; ?>"/></td>
                </tr>
                <tr>
                    <td style="color:<?php echo $_color_normal; ?>;"><img
                                src="../images/item_<?php echo($_ProcessLang['salat_dir'] == 'rtl' ? 2 : 1); ?>.gif"
                                width="4" height="7" border="0"
                                align="absmiddle"/>&nbsp;<?php echo $_ProcessLang['frm_active']; ?>:
                    </td>
                    <td><select name="cmbIsActive"
                                id="cmbIsActive"><?php BuildCombo($yesnoArr, $item['isactive']); ?></select></td>
                </tr>
                <tr>
                    <td colspan=2 style="color:<?php echo $_color_normal; ?>;">
                        <b><?php echo $_ProcessLang['cat_perms']; ?></b></td>
                </tr>
                <tr>
                    <td colspan=2>
                        <?php
                        $i = 0;
                        $new_listArr = get_user_processes($_SESSION['salatUserID']);
                        foreach ($list as $section_id => $sec_arr) {
                            print "<span style='padding:0 15px'></span><big>" . $sectionsTop[$section_id] . "</big><br>";
                            foreach ($sec_arr as $tree_id => $tree_arr) {
                                print "<span style='padding:0 30px;'></span><b>" . $sectionsCombo[$section_id][$tree_id] . "</b><br>";
                                $i = ShowProcess($tree_arr, $sysprocessArr[$section_id][$tree_id], ((is_array($sysprocessArr[$section_id][$tree_id]['-1'])) ? '-1' : '0'), 50, $i, $new_listArr, $item['id']);
                            }
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td colspan=2>
                        <input type=button
                               value="<?php if ($item['id'] == "") print $_ProcessLang['button_new']; else print $_ProcessLang['button_update']; ?>"
                               onClick="javascript:doNew();"
                               style="border:solid 1px <?php echo $_color_dark; ?>;color:<?php echo $_color_text; ?>;font-size:15px;width:80px;height:22px;font-weight:bold;background-color:<?php echo $_color_normal; ?>;"/>
                        &nbsp;&nbsp;&nbsp;
                        <input type=button value="<?php echo $_ProcessLang['button_cancel']; ?>"
                               onClick="javascript:history.back(-1);"
                               style="border:solid 1px <?php echo $_color_dark; ?>;color:<?php echo $_color_text; ?>;font-size:15px;width:80px;height:22px;font-weight:bold;background-color:<?php echo $_color_normal; ?>;"/>
                    </td>
                </tr>
            </table>
        </form>
    <?php } else if ($act == "after") { ?>
        do update or insert new
    <?php } else if ($act == "del") { ?>
        do delete
    <?php } ?>
</div>

<?php if ($_GET['menurefresh'] == "yes") { ?>
    <script>top.framMenu.MenuRefreshToU('all', '<?php echo $_GET['tid'];?>');</script>
<?php } ?>

</body>
</html>
