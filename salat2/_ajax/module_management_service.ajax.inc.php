<?
$answerArr=array('err'=>'','msg'=>'');
$act= $_REQUEST['act'];

switch (strtolower($act)){
   
   case 'check_order':
	   $order_number=$_REQUEST['order_number'];
	   $query="SELECT show_order FROM `tb_sys_processes` ORDER BY show_order ASC";
	   $res=$Db->query($query) or die($query);
	   $orderArr=array();
	   while($line = $Db->get_stream($res)){
		   $orderArr[]=$line['show_order'];
	   }
	   if(in_array($order_number,$orderArr)){
		   $new_order=min($orderArr);
		   while(in_array($new_order,$orderArr)){
			   $new_order+=10;
		   }
		   $answerArr['msg']=$new_order;
	   }

       exit(json_encode($answerArr));
       break;

   case 'check_exist':

       include($_project_server_path.$_includes_path."class/autoModuleManager.class.inc.php");

       $element_name=$_REQUEST['name'];
       $section=$_REQUEST['section'];

       $Auto_module = new autoModuleManager($element_name, $section);
       if($Auto_module->check_files_exist()){
           $answerArr['err'] = 'files_exist';
           $answerArr['msg'] = 'files with this name is already exist, please select another name of page.';
       }
       if($Auto_module->check_tables_exist()) {
           $answerArr['err'] = 'table_exist';
           $answerArr['msg'] = 'table with this name is already exist, please select another name of page.';
       }

       exit(json_encode($answerArr));
       break;
   
}

?>