<?php
include("../_inc/config.inc.php");
error_reporting(E_ALL);
ini_set('display_errors', '0');
$_ProcessID = 7;
$query = "SELECT * FROM tb_sys_user_permissions WHERE ((sysuserid=" . $_SESSION['salatUserID'] . ") AND (processid=" . $_ProcessID . "))";
$result = $Db->query($query);
if ($result->num_rows == 0) {
    print "<div align=center style='font-family:arial;' dir=rtl><font color='#DD4B2E'><big><b>אין הרשאות מתאימות לתהליך זה!</b></big></font><br><br>\nצור קשר עם מנהל המערכת לקבלת הרשאה מתאימה או <a href='javascript:history.back(-1);' style='color:black;'>חזור</a> לעמוד קודם.</div>";
    exit();
}
$act = $_GET['act'];
if ($act == "") $act = $_POST['act'];
if ($act == "") $act = "show";

$mt = @filemtime($_project_server_path . "/_static/sitemap.xml");
if ($mt == '') $mt = 0;

if ($act == "dogsm") {
    // do action: save new or update existing
    if ((time() - $mt) > (60 * 60 * 48)) {
        include($_project_server_path . $_salat_path . "_gsm/gsm_build.php");
        header("location: " . $_SERVER['PHP_SELF'] . "?err=1");
        exit();
    }
}
include($_project_server_path . $_salat_path . $_includes_path . 'module_info.inc.php');
?>
<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<html dir="rtl">
<head>
    <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
    <link rel="StyleSheet" href="../_public/main.css" type="text/css">
    <script type="text/javascript" src="/salat2/_public/jquery1.8.min.js"></script>
    <script language="JavaScript"> if (window.parent == window) location.href = '../frames.php'; </script>
    <script language="javascript">
        function doNew() {
            document.frmNew.submit();
        }
    </script>
    <?= $_salat_style; ?>
</head>
<body>
<? include($_SERVER['DOCUMENT_ROOT'] . '/salat2/_inc/module_menu.inc.php'); ?>
<div class="maindiv">
    <span class="maintitle">מפת אתר - גוגל</span>
    <br><br>
    <table width="500">
        <tr>
            <td>
                <img src="../images/inm_gsm.gif" align="left" border="0" hspace="10" vspace="10"/>
                <br/><br/>
                מפת אתר של גוגל, או באנגלית Google SiteMap, מאפשרת למנוע החיפוש "Google" לקרוא ולהבין את האתר שלך באופן
                יעיל ומקיף יותר.<br/><br/>
                אינמאנג' מאפשרת לך באמצעות ממשק הניהול (סל"ת), לעדכן את מפת האתר שלך באופן שוטף כך ש-Google יהיה מעודכן
                תמיד לגבי השינויים באתר שלך.<br/><br/>
                <br/>
                <?php if ($_GET['err'] == 1) {
                    print "<font color='red'>עדכון Google Sitemap בוצע בהצלחה</font><br /><br />";
                } ?>
                עדכון מפת האתר של גוגל ניתן לבצע כל 48 שעות (לחיצה על הכפתור)
                <br/><br/>
                <input type="button" value=" - לחץ כאן לעדכון מפת האתר של גוגל - "
                       onClick="document.location.href='<?php echo $_SERVER['PHP_SELF']; ?>?act=dogsm';" <?php echo(((time() - $mt) <= (60 * 60 * 48) && false) ? 'disabled="disabled"' : ''); ?> />
                <br/>
                <small>[ עדכון אחרון בוצע ב: <?php echo($mt > 0 ? date("H:i:s d/m/Y", $mt) : "לא בוצע עדכון"); ?>]
                </small>
                <br/><br/>
                <a href="<?php echo $_html_nonsecured_path . "sitemap.xml"; ?>" target="_blank">קישור לקובץ מפת האתר של
                    גוגל</a>
                <br/><br/>
                <b>* חשוב ביותר, עליך לעדכן את מפת האתר בחשבונך באתר Google</b><br/><br/>
                איך מבצעים עדכון: <br/>
                גלוש לחשבונך באתר <a href="https://www.google.com/webmasters/sitemaps/login" target="_blank">https://www.google.com/webmasters/sitemaps/login</a>,
                <br/>
                ברשימת האתרים, הקלק על הקישור לעריכת הדומיין שלך (<?php echo $_project_domain_path; ?>), <br/>
                עבור ללשונית Sitemaps, <br/>
                סמן את "sitemap.xml" <br/>
                ולחץ על הכפתור "Resubmit Selected". <br/>
                זהו, פעולה זו שלחה לגוגל בקשה לעדכון מפת האתר וסריקה חוזרת של אתרך.
            </td>
        </tr>
    </table>
</div>

</body>
</html>