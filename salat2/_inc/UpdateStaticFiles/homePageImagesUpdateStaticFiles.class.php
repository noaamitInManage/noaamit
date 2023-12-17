<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 * 
 */


//---------------------------------------------------------------------------//

class homePageImagesUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

	//public $_Proccess_Main_DB_Table,$_ProcessID;
	public $className,$file_name,$itemsArr_name;


	//------------------------------------ FUNCTIONS ------------------------------------

	function __construct(){
		parent::__construct();
		$this->_Proccess_Main_DB_Table = 'tb_home_page_images';
		$this->_ProcessID = 73;
		$this->className= trim(get_class());
		$this->file_name='homePageImages.inc.php';
		$this->itemsArr_name='homePageImagesArr';
		$this->name='תמונות עמוד הבית';
	}

	/*----------------------------------------------------------------------------------*/

	function updateStatics($id=''){
		@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/homePageImages.inc.php');
		updateStaticFile("SELECT order_num,media_id FROM {$this->_Proccess_Main_DB_Table} ORDER BY order_num ASC",
			'/_static/homePageImages.inc.php',
			'homePageImagesArr','order_num',true,true);

	}

	/*----------------------------------------------------------------------------------*/

	function updateAllStaticsFiles(){
	    $Db = Database::getInstance();

		$this->updateStatics();


		$query= " SELECT id FROM {$this->_Proccess_Main_DB_Table}";
		$result=$Db->query($query);

		while($row = $Db->get_stream($result)) {
			$this->updateStatics($row['id']);
		}

		parent::writeUpdate();
	}

	/*----------------------------------------------------------------------------------*/

	public function getItemsNumber(){
		if(strstr($this->className,'Langs')){
			$file_nameArr=explode('.',$this->file_name);
			$file_nameArr[0]=$file_nameArr[0].'.'.default_lang;
			$this->file_name=implode('.',$file_nameArr);
			include($_SERVER['DOCUMENT_ROOT'].'/_static/'.$this->file_name);//$this->itemsArr_name
		}else{
			include($_SERVER['DOCUMENT_ROOT'].'/_static/'.$this->file_name);//$this->itemsArr_name
		}
		$tmp=$this->itemsArr_name;
		return count($$tmp);
	}


}



?>