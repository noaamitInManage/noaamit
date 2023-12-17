<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 * 
 */


//---------------------------------------------------------------------------//

class generalUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

/*
 -  This class is Generic for all modules:
function module_updateStaticFiles() {
	global $_Proccess_Main_DB_Table,$_Proccess_Title,$_ProcessID,$_Proccess_Has_MultiLangs;

	$UpdateStatic = new generalUpdateStaticFiles($_Proccess_Main_DB_Table,$_ProcessID,$_Proccess_Title,$_Proccess_Has_MultiLangs);
	$UpdateStatic->updateStatics("","users","user");
	// || ----- Don't forget create a new folder for the static files with 777 permissions! And make sure the fieldsArr Array is true. ---- ||

	// this function to create group tables:
	// $UpdateStatic->updateGroupStatics("","tb_web_services_methods_params","method_id","methodsGroups","methodGroup",$paramsFieldsArr,$replace);
}

 */

	//public $_Proccess_Main_DB_Table,$_ProcessID;
	public $className,$file_name,$itemsArr_name;

	//------------------------------------ FUNCTIONS ------------------------------------

	function __construct($tb_name="tb_contents",$processId=1,$moduleName='דפי תוכן',$multiLangs=true){
		parent::__construct();
		$this->_Proccess_Main_DB_Table = $tb_name;
		$this->_ProcessID = $processId;
		$this->className= trim(get_class());
		$this->file_name="";
		$this->itemsArr_name="";
		$this->name=$moduleName;
		$this->multiLangs=$multiLangs;
	}

	/*----------------------------------------------------------------------------------*/

	function updateStatics($id='',$folderName="",$fileName="",$fieldsArr=array(0=>"Main.id",1=>"Lang.title"))
	{
		global $languagesArr,$module_lang_id;

		/* Set the id to edit */
		$idRequest = ($id) ? $id : $_REQUEST['inner_id'];

		/* Add lang if exists and make the smart dir */
		if($this->multiLangs)
		{
			$whereLang = "Lang.lang_id={$module_lang_id}";
			$pathLang = '.'.$languagesArr[$module_lang_id]['title'];
			$smart_dir=parent::smartLangDirctory('/_static/'.$folderName.'/',$idRequest,$module_lang_id);
		}
		else
		{
			$pathLang = "";
			$smart_dir=parent::smartDirctory('/_static/'.$folderName.'/',$idRequest);
		}

		/* Set the private vars */
		$this->file_name = $folderName.$pathLang.'.inc.php';
		$this->itemsArr_name = $folderName.'Arr';

		/* Delete the last static file */
		@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/'.$folderName.$pathLang.'.inc.php');

		/* Create the new static file */
		$where = ($whereLang) ? "WHERE ".$whereLang : "";

		$fieldsString = (empty($fieldsArr))? "*" : implode(",", $fieldsArr);
		updateStaticFile("SELECT ".$fieldsString." FROM `{$this->_Proccess_Main_DB_Table}` AS Main
							            LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
							                        Main.`id`=Lang.`obj_id`
							                    ) {$where}",
			'/_static/'.$folderName.$pathLang.'.inc.php',
			$folderName.'Arr','id',true,true);

		/* create the specific static file - Make sure the table exists in the ftp */
		if($_REQUEST['inner_id']) {

			/* Delete the last static file*/
			@unlink($_SERVER['DOCUMENT_ROOT'].$smart_dir.$fileName.'-'.$idRequest.'.inc.php');

			/* Create the new static file */
			$where = ($whereLang) ? "AND ".$whereLang : "";
			updateStaticFile("SELECT * FROM `{$this->_Proccess_Main_DB_Table}` AS Main
	                                    LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
	                                        Main.`id`=Lang.`obj_id`
	                                    )
	                                              WHERE Main.id='{$idRequest}' {$where}",
				$smart_dir.$fileName.'-'.$idRequest.'.inc.php',
				$fileName.'Arr');
		}
	}
	/*----------------------------------------------------------------------------------*/

	/*----------------------------------------------------------------------------------*/

	function updateGroupStatics($id='',$tb_groupName="",$commonField="",$folderName="",$fileName="",$fieldsArr=array(0=>"Main.id",1=>"Lang.title"),$replace=false){

		global $languagesArr,$module_lang_id,$Db;

		/* Set the id to edit */
		$idRequest = ($id) ? $id : $_REQUEST['inner_id'];

		/* Add lang if exists and make the smart dir */
		if($this->multiLangs)
		{
			$whereLang = "Lang.lang_id={$module_lang_id}";
			$pathLang = '.'.$languagesArr[$module_lang_id]['title'];
			$smart_dir=parent::smartLangDirctory('/_static/'.$folderName.'/',$idRequest,$module_lang_id);
		}
		else
		{
			$pathLang = "";
			$smart_dir=parent::smartDirctory('/_static/'.$folderName.'/',$idRequest);
		}

		/* make the child table to the parent */
		if($replace)
		{
			$parentTable = $tb_groupName;
			$childTable = $this->_Proccess_Main_DB_Table;
		}
		else
		{
			$parentTable = $this->_Proccess_Main_DB_Table;
			$childTable = $tb_groupName;
		}

		/* Set the class vars */
		$this->file_name = $folderName.$pathLang.'.inc.php';
		$this->itemsArr_name = $folderName.'Arr';

		/* Delete the last static file */
		@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/'.$folderName.$pathLang.'.inc.php');

		/* Run on parent table and save the childes */
		$fieldsString = (empty($fieldsArr))? "*" : implode(",", $fieldsArr); // Make sure the id field exsits in the array!
		$data = array();
		$parentWhere = ($whereLang) ? "WHERE ".$whereLang : "";
		$childWhere = ($whereLang) ? "AND ".$whereLang : "";

		$parentQuery = $Db->query("SELECT `id` FROM `{$parentTable}` AS Main
               						LEFT JOIN `{$parentTable}_lang` AS Lang ON (
               							Main.`id`=Lang.`obj_id`
               						) {$parentWhere}");
		while($parentResults = $Db->get_stream($parentQuery))
		{
			$parentId = $parentResults['id'];
			$childQuery = $Db->query("SELECT ".$fieldsString." FROM `{$childTable}` AS Main
													LEFT JOIN `{$childTable}_lang` AS Lang
													ON (Main.`id`=Lang.`obj_id`)
						               		WHERE `{$commonField}`='{$parentId}' {$childWhere}");

			/* Get the details of child table and save in array with parentId key */
			while($childResults = $Db->get_stream($childQuery))
			{
				$data[$parentId][$childResults['id']] = $childResults;
			}
		}

		/* Create the new static file */
		updateStaticFile($data,	'/_static/'.$folderName.$pathLang.'.inc.php',
			$folderName.'Arr','id',true,true);

		/* create the specific static file - Make sure the table exists in the ftp */
		if($_REQUEST['inner_id']) {

			/* Delete the last static file*/
			@unlink($_SERVER['DOCUMENT_ROOT'].$smart_dir.$fileName.'-'.$idRequest.'.inc.php');

			/* Create the new static file */
			$commonValue = $idRequest;
			$where = ($whereLang) ? "AND ".$whereLang : "";
			updateStaticFile("SELECT * FROM `{$tb_groupName}` AS Main
													LEFT JOIN `{$tb_groupName}_lang` AS Lang
													ON (Main.`id`=Lang.`obj_id`)
						               		WHERE `{$commonField}`='{$commonValue}' {$where}",
				$smart_dir.$fileName.'-'.$commonValue.'.inc.php',
				$fileName.'Arr');
		}
	}

	/*----------------------------------------------------------------------------------*/


	function updateAllStaticsFiles(){
		$this->updateStatics();
		global $Db;

		$query= " SELECT id FROM {$this->_Proccess_Main_DB_Table}";
		$result=$Db->query($query);

		while($row = $Db->get_stream($result)) {
			$this->updateStatics($row['id']);
		}

		parent::writeUpdate();
	}

	/*----------------------------------------------------------------------------------*/

}

//---------------------------------------------------------------------------//


?>