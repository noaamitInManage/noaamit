<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 03/12/13
 * Time: 14:12
 * 
 */

class contentUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

	//public $_Proccess_Main_DB_Table,$_ProcessID;
	public $className,$file_name,$itemsArr_name;


	//------------------------------------ FUNCTIONS ------------------------------------

	function __construct(){
		parent::__construct();
		$this->_Proccess_Main_DB_Table = 'tb_contents';
		$this->_ProcessID = 1;
		$this->className= trim(get_class());
		$this->file_name='contents.inc.php';
		$this->itemsArr_name='contentsArr';
		$this->name='דפי תוכן';
	}

	/*----------------------------------------------------------------------------------*/

	function updateStatics($id=''){
		$_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'] ;
		$smart_dir=parent::smartDirctory('/_static/contents/',$_REQUEST['inner_id']);
		@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/contents.inc.php');
		updateStaticFile("SELECT id,title FROM {$this->_Proccess_Main_DB_Table} ",
			'/_static/contents.inc.php',
			'contentsArr','id',true);

		if($_REQUEST['inner_id']) {
			@unlink($_SERVER['DOCUMENT_ROOT'].$smart_dir.'content-'.$_REQUEST['inner_id'].'.inc.php');
			updateStaticFile("SELECT * FROM {$this->_Proccess_Main_DB_Table} WHERE id='{$_REQUEST['inner_id']}'",
				$smart_dir.'content-'.$_REQUEST['inner_id'].'.inc.php',
				'contentArr');
		}

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
		include($_SERVER['DOCUMENT_ROOT'].'/_static/'.$this->file_name);//$this->itemsArr_name(){
		include($_SERVER['DOCUMENT_ROOT'].'/_static/'.$this->file_name);//$this->itemsArr_name
		$tmp=$this->itemsArr_name;
		return count($$tmp);
	}


}

?>