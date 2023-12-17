<?

include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
$Db = Database::getInstance();
$act= $_REQUEST['act'];
$id=$Db->make_escape($_REQUEST['id']);
$active=$Db->make_escape($_REQUEST['active']);
$tb_name='tb_answers';
   $classArr=array(
                  0=>'no',
                  1=>'yes',
   );
   $colorArr=array(
                  0=>'red',
                  1=>'green',
   );
   $_yesNo_arr = array(
   	'0' => 'לא',
   	'1' => 'כן',
   );

switch (strtolower($act)){
   
   case 'update':
      $active=(intval($active) +1 )%2;
      $query="UPDATE `{$tb_name}` SET `active`='{$active}' WHERE `id`='{$id}' ";
	   $Db->query($query) ;//

      /* update Answer static file */
      $_REQUEST['inner_id']=$id;
      $UpdateStatic = new answerUpdateStaticFiles();
	   $UpdateStatic->updateStatics(); 
	   
	   /* update Question static file */
	   include($_SERVER['DOCUMENT_ROOT'].'/_static/answers-'.$id.'.inc.php');//$answerArr
	   
	   $_REQUEST['inner_id']=$answerArr['question_id'];
	   $UpdateStatic2 = new questionUpdateStaticFiles();
	   $UpdateStatic2->updateStatics(); 
	   
      /* also update Static file */
      echo  json_encode(array("html"=>'<strong style="color:'.$colorArr[$active].'" class="active_'.$classArr[$active].'" rel="'.$id.'">'.$_yesNo_arr[$active].'</strong>'));
   break;
   
}

?>