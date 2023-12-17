<?php
/**
 * @desc: excel file builder manager  
 *        get function from user and print excel to the window
 * @author: gal zalait
 * @version :1.0
 * @since :06/10/11
 */
$act=$_REQUEST['act'];

$answer=array("err"=>'');
include($_project_server_path.'salat2/resources/php-excel/1.8.1/PHPExcel.php');
include($_project_server_path.'_inc/class/excelReports.class.inc.php');//excelReport

$act =($act)? $act : 'show';
$excelReport= new excelReport();     

switch ($act){
   case "show":
      $id=$_REQUEST['tp'];
      ($id)? $excelReport->__set('tp',$_REQUEST['id']) : ''; 
      $report=$_REQUEST['report_name'];

      $data = $excelReport->getReport($report);
      $file=$excelReport->print_report($data,$report);

$answer['html']=<<<HTML
<iframe width="1" height="1" src="{$file}">
</iframe>
HTML;
      break;
      
}
echo json_encode($answer);

?>