<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 * 
 */


class generalContentCategoriesUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

	//public $_Proccess_Main_DB_Table,$_ProcessID;
	public $className,$file_name,$itemsArr_name;


	//------------------------------------ FUNCTIONS ------------------------------------

	function __construct(){
		parent::__construct();
		$this->_Proccess_Main_DB_Table = 'tb_general_content_categories';
		$this->_ProcessID = 72;
		$this->className= trim(get_class());
		$this->file_name='generalContentCategories.inc.php';
		$this->itemsArr_name='generalContentCategoriesArr';
		$this->name='תוכן כללי - קטגוריות';
	}

	/*----------------------------------------------------------------------------------*/

	function updateStatics($id=''){
		@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/generalContentCategories.inc.php');
		updateStaticFile("SELECT id,title FROM {$this->_Proccess_Main_DB_Table} ",
			'/_static/generalContentCategories.inc.php',
			'generalContentCategoriesArr','id',true,true);

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