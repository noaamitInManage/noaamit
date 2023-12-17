<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 *
 */



//---------------------------------------------------------------------------//

class startup_imageLangsUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

	//public $_Proccess_Main_DB_Table,$_ProcessID;
	public $className,$file_name,$itemsArr_name;


	//------------------------------------ FUNCTIONS ------------------------------------

	function __construct(){
		parent::__construct();
		$this->_Proccess_Main_DB_Table = 'tb_startup_image';
		$this->_ProcessID = 78;
		$this->className= trim(get_class());
		$this->file_name='startup_images.inc.php';
		$this->itemsArr_name='startup_imagesArr';
		$this->name='תמונות פתיחה (מובייל)';
	}

	/*----------------------------------------------------------------------------------*/

	function updateStatics($id=''){
		$_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'] ;
		global $languagesArr,$module_lang_id;

		$smart_dir=parent::smartLangDirctory('/_static/startup_image/',$_REQUEST['inner_id'],$module_lang_id);
		@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/startup_image.'.$languagesArr[$module_lang_id]['title'].'.inc.php');

		updateStaticFile("SELECT Main.id,Lang.title FROM `{$this->_Proccess_Main_DB_Table}` AS Main
						         	LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
						               			Main.`id`=Lang.`obj_id`
						               		) WHERE Lang.lang_id={$module_lang_id}
						               				AND
						               				 	Main.`active`=1

         	",
			'/_static/startup_images.'.$languagesArr[$module_lang_id]['title'].'.inc.php',
			'startup_imagesArr','id',true,true);


		if($_REQUEST['inner_id']) {
			@unlink($_SERVER['DOCUMENT_ROOT'].$smart_dir.'startup_image-'.$_REQUEST['inner_id'].'.inc.php');

			updateStaticFile("SELECT * FROM `{$this->_Proccess_Main_DB_Table}` AS Main
               						LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
               							Main.`id`=Lang.`obj_id`
               						)
               								  WHERE Main.id='{$_REQUEST['inner_id']}' AND Lang.lang_id='{$module_lang_id}'",
				$smart_dir.'startup_image-'.$_REQUEST['inner_id'].'.inc.php',
				'startup_imageArr');
		}
	}

	/*----------------------------------------------------------------------------------*/

	function updateAllStaticsFiles(){
		$Db=Database::getInstance();
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