<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 * 
 */


//---------------------------------------------------------------------------//

class sitePagesUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

	public $className,$file_name,$itemsArr_name;


	//------------------------------------ FUNCTIONS ------------------------------------

	function __construct(){
		parent::__construct();
		$this->_Proccess_Main_DB_Table = 'tb_sitepages';
		$this->_ProcessID = 65;
		$this->className= trim(get_class());
		$this->file_name='seo-module.inc.php';
		$this->itemsArr_name='seoModuleArr';
		$this->name='הגדרות מודולים';
	}

	/*----------------------------------------------------------------------------------*/

	function updateStatics($id=''){
		$_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'] ;
		global $languagesArr,$module_lang_id,$Db;

		$modulesArr = array();
		$sql = "SELECT
						Page.mdl_name,Page.title,Page.is_static,Page.is_advanced,Page.lastupdate,
						Seo.module_id, Seo.url, Seo.file_name,Seo.arr_name,Seo.priority,Seo.seo_strict,Seo.da_smart_dir,Seo.da_file,Seo.da_arr_name
						FROM `tb_sitepages` AS Page
						LEFT JOIN `tb_seo` AS Seo ON Seo.module_id=Page.mdl_id
						WHERE Page.is_static=0 AND Page.is_advanced=1";
		$res = $Db->query($sql);
		while($row = $Db->get_stream($res)){
			$module = array(
				'file' 				=> $row['file_name'],
				'arrName' 		=> $row['arr_name'],
				'mdlId' 			=> $row['module_id'],
				'mdlName'			=> $row['mdl_name'],
				'priority'		=> $row['priority'],
				'seoStrict' 	=> $row['seo_strict'],
				'directAccess'=> array(
					'smart_dir' => $row['da_smart_dir'],
					'file'			=> $row['da_file'],
					'arrName'		=> $row['da_arr_name']
				)
			);
			$modulesArr[$row['url']] = $module;
		}

		updateStaticFile($modulesArr,'/_static/'.$this->file_name,$this->itemsArr_name,'id');
	}

	/*----------------------------------------------------------------------------------*/

	function updateAllStaticsFiles(){
		$Db= Database::getInstance();
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