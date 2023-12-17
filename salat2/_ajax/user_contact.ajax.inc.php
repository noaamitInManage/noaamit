<?
set_time_limit(0);
$act=($_REQUEST['act']) ? $_REQUEST['act'] : 'show' ;
$email = trim($_REQUEST['email']);
$sub = lang("contact_message_subject");
$err='';
include($_SERVER['DOCUMENT_ROOT'].'/_inc/class/moduleComment.class.inc.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/class/siteFunctions.inc.class.php');
include_once($_SERVER['DOCUMENT_ROOT'].'/_inc/site.array.inc.php');

switch ($act){
   
   case "show":
         ob_start();
         include($_SERVER['DOCUMENT_ROOT'].'/salat2/_inc/contact.frm.inc.php');
         $html=ob_get_clean();
      break;


    case "send":
        $content = $Db->make_escape(trim($_REQUEST['content']));
        ModuleComment::addComment($_REQUEST["user_id"], $_REQUEST['obj_id'], $content, 1);
        $content = str_replace("\\n", "<br/>", $content);
        siteFunctions::send_mail($email, $sub, $content, array(), 1);
        $html=ModuleComment::drawAllcomments($_REQUEST['obj_id']);

        break;

    case "addComment":
        $content = $Db->make_escape(trim($_REQUEST['content']));
        ModuleComment::addComment($_REQUEST["user_id"], $_REQUEST['obj_id'], $content);
        $html=ModuleComment::drawAllcomments($_REQUEST['obj_id']);
        break;

    case "delComment":
        $tb_name='tb_contact_messages';
        $comment_id=$Db->make_escape($_REQUEST['comment_id']);
		$Db->query($query);("DELETE FROM  `{$tb_name}` WHERE `id` ='{$comment_id}'")or die("hahaha");
        $html=ModuleComment::drawAllcomments($_REQUEST['obj_id']);
        break;
}


echo json_encode(array("err"=>$err,"html"=>$html));


?>

