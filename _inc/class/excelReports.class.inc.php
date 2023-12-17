<?
/**
 * Genric excel 2007 Reports
 * @author : Gal Zalait - galzalait@gmail.com
 * @version : 1.2
 * @copyright :inmanage
 * @since 1876 :-)
 * @last_update: 6/10/11
 
send to print_report function data array of values with first array as head
 
 need to load 1.7.6/PHPExcel.php
 for adding function add function like excelTemplateReport
*/
class excelReport{
   
   public $ts;
   public $tp;   
   public $report_name;
   
   //protected static $count;
   
   const prefix='excel';
   const suffix='Report';
  
   /*----------------------------------------------------------------------------------*/

   function __construct(){
      $this->ts=time();
 
   }

   /*----------------------------------------------------------------------------------*/

   function __destruct(){

   }
	
   /*----------------------------------------------------------------------------------*/   

   public function __set($var, $val){
		$this->$var = $val;
	}

	/*----------------------------------------------------------------------------------*/
	
	public function __get($var){
		return $this->$var;
	}
	
	/*-------------------------------excel 2007 format----------------------------------*/   
	
	final public function print_report($data,$name){
	   
	   $with_logo=true;
	   
	   $head=array_shift($data);
      $objPHPExcel = new PHPExcel();
         
     	$default_border = array(
             'style' => PHPExcel_Style_Border::BORDER_THIN,
             'color' => array('rgb'=>'1006A3')
      );
      $style_header = array(
          'borders' => array(
              'bottom' => $default_border,
              'left' => $default_border,
              'top' => $default_border,
              'right' => $default_border,
          ),
          'fill' => array(
              'type' => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array('rgb'=>'E1E0F7'),
          ),
          'font' => array(
              'bold' => true,
          )
      );

         // Set properties
         $objPHPExcel->getProperties()->setCreator("Gal Zalait")
               							  ->setLastModifiedBy("wobi")
               							  ->setTitle("gal test office 2007 xlsx title")
               							  ->setSubject("gal test office 2007 xlsx file")
               							  ->setDescription("Test document by gal zalait.")
               							  ->setKeywords("office 2007 openxml php")
               							  ->setCategory("Test result file");
               							  
         $objPHPExcel->getActiveSheet()->setRightToLeft(true);
         							
        // ascci code of 'A'
        $zFlag=false;
        if($with_logo){
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow('A',1, "              ");// save space for logo
        }
        for($i=1,$j=0,$ABC=65;$i<=count($head)+1;$i++,$j++,$ABC++){
           if($ABC==91){
              $zFlag=true;    
              $ABC=65;// ascci code of 'A'
           }           
            $colStr = ($zFlag) ? chr(65).chr($ABC)."1" : chr($ABC)."1";
            $colStrOnly = ($zFlag) ? chr($startRow).chr($ABC) : chr($ABC);
            $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i,1, $head[$j]);
           	$objPHPExcel->getActiveSheet()->getColumnDimension($colStrOnly)->setAutoSize(true);
            try {
              	$objPHPExcel->getActiveSheet()->getStyle($colStr)->applyFromArray( $style_header );
               $objPHPExcel->getActiveSheet()->getStyle($colStr)->getFill()->getStartColor()->setARGB('00FF5500');
            }
            catch (Exception $e){
               die('<hr /><pre>' . print_r("under constraction", true) . '</pre><hr />');
            }
        }

        @unlink($_SERVER['DOCUMENT_ROOT'].'/salat2/_static/'.$name.'.xlsx');
        $totalDate=count($data) +1;
        for ($row=2,$arrIndex=0; $row <= $totalDate; $row++){
           $dataItem=array_shift($data);
           for($col=1,$itemJ=0;$col <= count($dataItem) ; $col++,$itemJ++){
              $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col,$row, $dataItem[$itemJ]); 
              if(substr_count($dataItem[$itemJ],'/')==2){//Special fix for string
                  $date_tmp=explode('/',$dataItem[$itemJ]);
                  $dateString = $date_tmp[2].'-'.$date_tmp[1].'-'.$date_tmp[0];
                  $PHPDateValue = strtotime($dateString) + (60*60*24);
                  $ExcelDateValue = PHPExcel_Shared_Date::PHPToExcel($PHPDateValue);
                  $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $ExcelDateValue); 
                  $objPHPExcel->getActiveSheet()->getStyleByColumnAndRow($col, $row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_DMYSLASH); 
              }
               if($itemJ==5){//תעודת זהות                 
                  $objPHPExcel->getActiveSheet()->getCell(chr($col + 65) . $row)->setValueExplicit(str_replace('"','',$dataItem[$itemJ]),PHPExcel_Cell_DataType::TYPE_STRING);
               }
           }
        }    
        
         $with_logo = (file_exists('logo.jpg')) ? $with_logo :false; // extra check for logo
         
         if($with_logo){
            $objDrawing = new PHPExcel_Worksheet_Drawing();
            $objDrawing->setName('Logo');
            $objDrawing->setDescription('Logo');
            $objDrawing->setPath('logo.jpg');
            $objDrawing->setHeight(50);
            
            $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
            $objDrawing->setName('Paid');
            $objDrawing->setDescription('Paid');
            $objDrawing->setPath('logo.jpg');
            $objDrawing->setCoordinates('A2');
            $objDrawing->setOffsetX(0);
            $objDrawing->setOffsetY(3);
            $objDrawing->setRotation(35);
            $objDrawing->getShadow()->setVisible(true);
            $objDrawing->getShadow()->setDirection(80);
         }

         $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
         @unlink($_SERVER['DOCUMENT_ROOT'].$_SERVER['DOCUMENT_ROOT'].'/_static/'.$name.'.xlsx');
         $full_file_name='/salat2/_static/'.$name.'.xlsx';
         $objWriter->save($_SERVER['DOCUMENT_ROOT'].$full_file_name);
         return $full_file_name;
/*         header('Content-type: application/x-msdownload');
         header('Content-Disposition: attachment;filename='.$name.'.xlsx');
         header('Cache-Control: max-age=0');
         $handle=fopen($_SERVER['DOCUMENT_ROOT'].'/_static/'.$name.'.xlsx','r');
            fpassthru($handle);
         exit();*/
	}
	
	/*----------------------------------------------------------------------------------*/   

	public function getReport($report_name,$tp=''){
	   $this->__set('report_name',$report_name);
	   $this->__set('tp',$tp);

      return  $this->{$this->getFunctionName($report_name)}();
   }
   
   /*----------------------------------------------------------------------------------*/
   
   private function getFunctionName($report_name){
      return self::prefix.ucfirst($report_name).self::suffix ;
   }
   
   /*----------------------------------------------------------------------------------*/   
     
   private function excelContactReport(){
   //   $whereTp = ($this->tp) ? "`tb1`.tp =  '$this->tp'" : "(`tb1`.tp =  '5') OR (`tb1`.tp =  '6') OR (`tb1`.tp =  '11') ";
		$Db = Database::getInstance();
      $data[]=array(
               'קוד',
               'שם',
               'אימייל',
               'טלפון',
               'תאריך',
      );
         $query=" SELECT `id`,`name`,`email`,`phone`,`last_update` FROM `tb_contacts` ";
         $result=$Db->query($query);
         while($row = $Db->get_stream($result)) {
         	$data[]=array(
                    $row['id'],      // 'קוד',
                    $row['name'],      // 'שם',
                    $row['email'],      // 'אימייל',
                    $row['phone'],     // 'טלפון',
                    date('j/m/Y',$row['last_update']),      // 'תאריך',
                     
         	);
         
         }
         return $data;
    
   }
    
   /*----------------------------------------------------------------------------------*/   
   
   private function excelCitiesReport(){
      //$whereTp = ($this->tp) ? "`tb1`.tp =  '$this->tp'" : "(`tb1`.tp =  '5') OR (`tb1`.tp =  '6') OR (`tb1`.tp =  '11') ";
      global $areasArr ,$Db;
      $data[]=array(
               'קוד',
               'שם',
               'אזור',
      );
         $query=" SELECT * FROM `tb_cities` ";
         $result=$Db->query($query);
         while($row = $Db->get_stream($result)) {
         	$data[]=array(
                    $row['id'],      // 'קוד',
                    $row['title'],      // 'שם',
                    $areasArr[$row['area_id']],      // 'אזור',
                     
         	);
         
         }
         return $data;
    
   }
     
 /*----------------------------------------------------------------------------------*/   
   
   private function excelStreetsReport(){
      //$whereTp = ($this->tp) ? "`tb1`.tp =  '$this->tp'" : "(`tb1`.tp =  '5') OR (`tb1`.tp =  '6') OR (`tb1`.tp =  '11') ";
      global $areasArr,$Db;
      include($_SERVER['DOCUMENT_ROOT'].'/_static/neighborhood.inc.php');//$neighborhoodsArr
      $data[]=array(
               'קוד',
               'שם',
               'שכונה',
      );
         $query=" SELECT id,title,neighborhood_id  FROM `tb_streets` WHERE `city_id`=1212
						ORDER BY ABS(`title`) ASC";
         $result=$Db->query($query);
         while($row = $Db->get_stream($result)) {
         	$data[]=array(
                    $row['id'],      // 'קוד',
                    $row['title'],      // 'שם',
                    $neighborhoodsArr[$row['neighborhood_id']],      // 'אזור',
                     
         	);
         
         }
         return $data;
    
   }   
   /*----------------------------------------------------------------------------------*/   
	
   private function excelUserOfferReport(){
      //$whereTp = ($this->tp) ? "`tb1`.tp =  '$this->tp'" : "(`tb1`.tp =  '5') OR (`tb1`.tp =  '6') OR (`tb1`.tp =  '11') ";
      global $areasArr,$Db;
      $data[]=array(
               'קוד',
               'קוד משתמש',
               'לינק',
               'שדה מוצע',
               'שתאריך',
      );
         $query=" SELECT * FROM `tb_user_offer` ";
         $result=$Db->query($query);
         while($row = $Db->get_stream($result)) {
         	$data[]=array(
                    $row['id'],      // 'קוד',
                    $row['user_id'],      // 'קוד משתמש',
                    $row['page_link'],      // 'לינק',
                    $row['title'],      // 'שדה מוצע',
                    date('j-m-y [H:i]',$row['last_update']),      // 'אזור',
                     
         	);
         
         }
         return $data;
    
   }
     
   /*----------------------------------------------------------------------------------*/   
   
   private function excelCampaignsReport(){

         $data[]=array(
            'קוד',
            'שם פרוייקט',
            'מפרסם משויך',
            'תאריך התחלה',
            'תאריך סיום',
            'סה"כ באנרים',
            'סה"כ צפיות',
            'סה"כ הקלקות',
            'פעיל',
          );
      
      $query=" SELECT Camp.`id`,Camp.title,Camp.`advertiser_id`,Camp.`start_date`,Camp.`end_date` ,Camp.is_active ,Adv.title AS 'advertiser'
                           FROM `tb_campaign` AS Camp
                              LEFT JOIN `tb_advertisers` AS Adv ON (
                                 Adv.id=Camp.advertiser_id 
                              )
                           
                           ";
      $Db = Database::getInstance();
      $result=$Db->query($query);
      while($row = $Db->get_stream($result)) {
         
      	$q="SELECT COUNT(`id`) AS 't_banners' ,SUM(total_clicks) AS 't_clicks',SUM(total_views) AS 't_views' 
      	        FROM `tb_banners`  
      	              WHERE `campaign_id`='{$row['id']}'";
      	$r=$Db->get_stream($Db->query($q));
      	
         $data[]=array(
              $row['id'],// 'קוד',
              $row['title'],// 'שם פרוייקט',
              $row['advertiser'],// 'מפרסם משויך',
              date('j/m/Y [H:i]',$row['start_date']),// 'תאריך התחלה',
              date('j/m/Y [H:i]',$row['end_date']),// 'תאריך סיום',
              $r['t_banners'],// 'סה"כ באנרים',
              $r['t_views'],// 'סה"כ צפיות',
              $r['t_clicks'],// 'סה"כ הקלקות',
              $row['is_active'],// 'פעיל',
          );      	
      }

               return $data;
   }
   
   /*----------------------------------------------------------------------------------*/   
   
   private function excelSuggestionReport(){
   	global $Db;
      $_yesNo_arr = array(
      	'0' => 'לא',
      	'1' => 'כן',
      );

      $data[]=array(
               'קוד',
               'קוד משתמש',
               'מייל',
               'הצעה',
               'עמוד המציע',
               'פעיל',
               'ip',
               'תאריך',
      );    
      $query=" SELECT * FROM `tb_fields_suggestion`  ";   
      $result=$Db->query($query);
         
      while($row = $Db->get_stream($result)) {
         	$data[]=array(
                    $row['id'],      // 'קוד',
                    $row['user_id'],      // 'קוד משתמש',
                    $row['project'],//  'מייל',
                    $row['area'],// 'הצעה',
                    $row['mail'],//  'עמוד המציע',
                    $_yesNo_arr[$row['active']],//   'פעיל',
                    $row['ip'],//   'כתובת IP',
                    date('j/m/Y [H:i]',$row['last_update']),//   'תאריך',
         	);              	
      }     
      
      return $data;
   }
   
   /*----------------------------------------------------------------------------------*/      
   
   private function excelConstractorContactReport(){
      //$whereTp = ($this->tp) ? "`tb1`.tp =  '$this->tp'" : "(`tb1`.tp =  '5') OR (`tb1`.tp =  '6') OR (`tb1`.tp =  '11') ";
      global $areasArr,$Db;
      $data[]=array(
               'קוד',
               'שם',
               'פרוייקט',
               'אזור',
               'דואל',
               'טלפון',
               'נושא',
               'פרטים',
               'תאריך',
      );
      
         $query=" SELECT * FROM `tb_constractor_contact` ";
         $result=$Db->query($query);
         
         while($row = $Db->get_stream($result)) {
         	$data[]=array(
                    $row['id'],      // 'קוד',
                    $row['name'],      // 'שם',
                    $row['project'],//  'פרוייקט',
                    $row['area'],// 'אזור',
                    $row['email'],//  'דואל',
                    $row['phone'],//   'טלפון',
                    $row['subject'],//   'נושא',
                    $row['details'],//   'פרטים',
                    date('j/m/Y [H:i]',$row['last_update']),//   'תאריך',
         	);
         }
         return $data;
    
   }
     	
   /*----------------------------------------------------------------------------------*/      
   
   //user_offer
}


?>