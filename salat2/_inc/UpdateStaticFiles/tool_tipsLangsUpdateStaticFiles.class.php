<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 * 
 */


//---------------------------------------------------------------------------//

class tool_tipsLangsUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

	//public $_Proccess_Main_DB_Table,$_ProcessID;
	public $className,$file_name,$itemsArr_name;


	//------------------------------------ FUNCTIONS ------------------------------------

	function __construct(){
		parent::__construct();
		$this->_Proccess_Main_DB_Table = 'tb_tool_tips';
		$this->_ProcessID = 61;
		$this->className= trim(get_class());
		$this->file_name='tool_tips.inc.php';
		$this->itemsArr_name='tool_tipsArr';
		$this->name='טול טיפ';
	}

	/*----------------------------------------------------------------------------------*/

	function updateStatics($id=''){
		$_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'] ;
		global $languagesArr,$module_lang_id;

		$smart_dir=parent::smartLangDirctory('/_static/tool_tips/',$_REQUEST['inner_id'],$module_lang_id);
		@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/tool_tips.'.$languagesArr[$module_lang_id]['title'].'.inc.php');

		updateStaticFile("SELECT Main.id,Lang.title FROM `{$this->_Proccess_Main_DB_Table}` AS Main
						         	LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
						               			Main.`id`=Lang.`obj_id`
						               		) WHERE Lang.lang_id={$module_lang_id}

         	",
			'/_static/tool_tips.'.$languagesArr[$module_lang_id]['title'].'.inc.php',
			'tool_tipsArr','id',true,true);


		if($_REQUEST['inner_id']) {
			@unlink($_SERVER['DOCUMENT_ROOT'].$smart_dir.'tool_tip-'.$_REQUEST['inner_id'].'.inc.php');

			updateStaticFile("SELECT * FROM `{$this->_Proccess_Main_DB_Table}` AS Main
               						LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
               							Main.`id`=Lang.`obj_id`
               						)
               								  WHERE Main.id='{$_REQUEST['inner_id']}' AND Lang.lang_id='{$module_lang_id}'",
				$smart_dir.'tool_tip-'.$_REQUEST['inner_id'].'.inc.php',
				'tool_tipArr');
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