<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 * 
 */


//---------------------------------------------------------------------------//

class mediaUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

	//public $_Proccess_Main_DB_Table,$_ProcessID;
	public $className,$file_name,$itemsArr_name;


	//------------------------------------ FUNCTIONS ------------------------------------

	function __construct(){
		parent::__construct();
		$this->_Proccess_Main_DB_Table = 'tb_media';
		$this->_ProcessID = 16;
		$this->className= trim(get_class());
		$this->file_name='media.inc.php';
		$this->itemsArr_name='mediaArr';
		$this->name='מדיה';
	}

	/*----------------------------------------------------------------------------------*/

	function updateStatics(){

		$Db = Database::getInstance();
		$smart_dir=parent::smartDirctory('/_static/media/',$_REQUEST['inner_id']);
		@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/'.$this->file_name);
		updateStaticFile("SELECT `id`,`title` FROM  `{$this->_Proccess_Main_DB_Table}` ",
			'/_static/'.$this->file_name,
			'mediaArr','id',true,true);

		include($_SERVER['DOCUMENT_ROOT'].'/_static/mediaCategory.inc.php');//$mediaCategorysArr
		$mediaGroupArr=array();

		foreach ($mediaCategorysArr AS $key=>$value){
			$mediaGroupArr[$key]=array();
			$q="SELECT `id`,`title`,`img_ext` FROM `{$this->_Proccess_Main_DB_Table}` WHERE `category_id`='{$key}'";
			$r=$Db->query($q);
			while($row = $Db->get_stream($r)) {
				$mediaGroupArr[$key][$row['id']]=$row;
			}
			updateStaticFile($mediaGroupArr[$key],
				'/_static/mediaGroup/mediaGroup-'.$key.'.inc.php',
				'mediaGroupsArr','id',true,false);
		}

		@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/mediaGroup.inc.php');

		updateStaticFile($mediaGroupArr,
			'/_static/mediaGroup.inc.php',
			'mediaGroupArr','id',true,false);
		if($_REQUEST['inner_id']) {
			@unlink($_SERVER['DOCUMENT_ROOT'].$smart_dir.'media-'.$_REQUEST['inner_id'].'.inc.php');
			updateStaticFile("SELECT * FROM {$this->_Proccess_Main_DB_Table} WHERE id='{$_REQUEST['inner_id']}'",
				$smart_dir.'media-'.$_REQUEST['inner_id'].'.inc.php',
				'imgArr');
		}

	}

	/*----------------------------------------------------------------------------------*/

	function updateAllStaticsFiles(){
		$this->updateStatics();

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