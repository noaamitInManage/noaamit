<?php

include_once("../_inc/config.inc.php");
$_ProcessID = 3;

include_once($_project_server_path . $_salat_path . "_static/langs/processes/" . $_ProcessID . "." . $_SESSION['salatLangID'] . ".inc.php");
include_once($_project_server_path . $_salat_path . "_static/langs/" . $_SESSION['salatLangID'] . ".inc.php");

$query = "SELECT * FROM tb_sys_user_permissions WHERE ((sysuserid=" . $_SESSION['salatUserID'] . ") AND (processid=" . $_ProcessID . "))";
$result = $Db->query($query);
if ($result->num_rows == 0) {
    print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>You have no permissions for this process</b></big></font><br><br>\nContact the system admin to aprove permission or <a href='javascript:history.back(-1);' style='color:black;'>go back</a> to previous page.</div>";
    exit();
}
/* netanel - 03/11/2013: clear sql injection from $_REQUEST array. */
real_escape_request();

$act = $_GET['act'];
if ($act == "") $act = $_POST['act'];
if ($act == "") $act = "show";

$msg = (int)$_GET['msg'];

if ($act == "after") {
    // yes id - update existing
    if (strlen($_POST['txtPassNew']) >= 8 && $_POST['txtPassNew'] == $_POST['txtPassConfirm'] && !preg_match('/(?:^\d+$)|(?:^[a-zA-Z]+$)/', $_POST['txtPassNew'])) {
        $trueStr = true;
        for ($i = 0; $i < strlen($_POST['txtPassNew']); $i++) {
            if ((ord($_POST['txtPassNew'][$i]) == (ord($_POST['txtPassNew'][$i + 1]) - 1) || ord($_POST['txtPassNew'][$i]) == (ord($_POST['txtPassNew'][$i + 1]) + 1))) {
                $trueStr = false;
            }
        }
    } else {
        $trueStr = false;
    }

    if ($trueStr) {
        $query = "UPDATE tb_sys_users SET password='" . hash("sha256", "_in" . $_POST['txtPassNew'] . "ge_") . "' WHERE ((id=" . $_SESSION['salatUserID'] . ") AND (password='" . hash("sha256", "_in" . $_POST['txtPassOld'] . "ge_") . "'))";
        $result = $Db->query($query);

        Header("Location: password.php?msg=" . (($Db->get_affected_rows() > 0) ? '1' : '2'));
    } else {
        Header("Location: password.php?msg=3");
    }

    exit();

    // yes id - update existing
    $query = "UPDATE tb_sys_users SET password='" . md5($_POST['txtPassNew']) . "' WHERE ((id=" . $_SESSION['salatUserID'] . ") AND (password='" . md5($_POST['txtPassOld']) . "'))";
    $result = $Db->query($query);
    Header("Location: password.php?msg=" . (($Db->get_affected_rows() > 0) ? '1' : '2'));
    exit();
} else {
    // show password changing form
}

?>

<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="<?php echo $_ProcessLang['salat_dir']; ?>">
<head>
    <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8"/>
    <link rel="StyleSheet" href="../_public/main.css" type="text/css">
    <script type="text/javascript" src="/salat2/_public/jquery1.8.min.js"></script>
    <script language="JavaScript"> if (window.parent == window) location.href = '../frames.php'; </script>
    <script language="javascript">
        function doNew() {
            if (document.frmNew.txtPassOld.value != "") {
                if (document.frmNew.txtPassNew.value != "") {
                    if (document.frmNew.txtPassConfirm.value == document.frmNew.txtPassNew.value) {
                        if (document.frmNew.txtPassNew.value.length >= 8 || document.frmNew.txtPassConfirm.value.length >= 8) {
                            document.frmNew.submit();
                        }
                        else {
                            alert("<?php echo $_ProcessLang['must_pass3'];?>");
                            document.frmNew.txtPassConfirm.focus();
                        }
                    } else {
                        alert("<?php echo $_ProcessLang['must_pass2'];?>");
                        document.frmNew.txtPassConfirm.focus();
                    }
                } else {
                    alert("<?php echo $_ProcessLang['must_pass1'];?>");
                    document.frmNew.txtPassNew.focus();
                }
            } else {
                alert("<?php echo $_ProcessLang['must_oldpass'];?>");
                document.frmNew.txtPassOld.focus();
            }
        }
    </script>
    <?php echo $_salat_style ?>
</head>
<body>
<? include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_menu.inc.php'); ?>
<div class="maindiv">
    <?php if ($act == "show") { ?>
        <span class="titleTxt"><?php echo $_ProcessLang['main_title']; ?></span>
        <br><br>
        <?php print "<span class=error>" . $_ProcessLang['err_' . $msg] . "</span>"; ?>
        <form name="frmNew" action="?act=after" method="post">
            <table width="500" border="0" cellpadding="3" cellspacing="1" class="dottTblo">
                <tr>
                    <td width="100" valign="top" class="normTxt">
                        <?php echo $_salat_icon ?>
                        &nbsp;<b><?php echo $_ProcessLang['oldpass']; ?>:</b></td>
                    <td width="150" valign="top"><input type="password" name="txtPassOld" id="txtPassOld"
                                                        dir="<?php echo $_LANG['salat_dir']; ?>"/></td>
                    <td width="200" valign="top"><?php echo $_ProcessLang['oldpass_hint']; ?></td>
                </tr>
                <tr>
                    <td width="220" valign="top" style="normTxt">
                        <?php echo $_salat_icon ?>
                        &nbsp;<b class="normTxt"><?php echo $_ProcessLang['pass1']; ?>:</b></td>
                    <td width="150" valign="top"><input type="password" name="txtPassNew" id="txtPassNew"
                                                        dir="<?php echo $_LANG['salat_dir']; ?>"/></td>
                    <td width="200" valign="top"><?php echo $_ProcessLang['pass1_hint']; ?></td>
                </tr>
                <tr>
                    <td width="220" valign="top" nowrap=nowrap class="normTxt">
                        <?php echo $_salat_icon ?>
                        &nbsp;<b class="normTxt"><?php echo $_ProcessLang['pass2']; ?>:</b></td>
                    <td width="150" valign="top"><input type="password" name="txtPassConfirm" id="txtPassConfirm"
                                                        dir="<?php echo $_LANG['salat_dir']; ?>"/></td>
                    <td width="200" valign="top" nowrap><?php echo $_ProcessLang['pass2_hint']; ?></td>
                </tr>
                <tr>
                    <td colspan=3>
                        <input type=button value="<?php echo $_ProcessLang['button_update']; ?>"
                               onClick="javascript:doNew();" class="btn"/>
                    </td>
                </tr>
            </table>
        </form>
    <?php } else if ($act == "after") { ?>
        do update or insert new
    <?php } ?>
</div>

</body>
</html>