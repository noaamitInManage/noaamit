<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 * 
 */



class footerUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

	//public $_Proccess_Main_DB_Table,$_ProcessID;
	public $className,$file_name,$itemsArr_name;


	//------------------------------------ FUNCTIONS ------------------------------------

	function __construct(){
		parent::__construct();
		$this->_Proccess_Main_DB_Table = 'tb_footer';
		$this->_ProcessID = 14;
		$this->className= trim(get_class());
		$this->file_name='footers.inc.php';
		$this->itemsArr_name='footersArr';
		$this->name='פוטר';
	}

	/*----------------------------------------------------------------------------------*/

	function updateStatics(){
		global $languagesArr,$module_lang_id,$Db;
		$link_array=array();
//				$query=" SELECT * FROM `{$this->_Proccess_Main_DB_Table}`";
		$query="SELECT Main.id,Lang.content FROM `{$this->_Proccess_Main_DB_Table}` AS Main
						         	LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
						               			Main.`id`=Lang.`obj_id`
						               		) WHERE Lang.lang_id={$module_lang_id}";

		$result=$Db->query($query) or die($query);

		while($row = $Db->get_stream($result)) {
			$link_array[$row['paragraph_id']]=$row['paragraph_id'];
//					$result2=$Db->query("SELECT * FROM `{$this->_Proccess_Main_DB_Table}` WHERE paragraph_id ='{$row['paragraph_id']}'")or die(mysql_error());
			$result2=$Db->query("SELECT Main.id,Lang.content FROM `{$this->_Proccess_Main_DB_Table}` AS Main
						         	LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
						               			Main.`id`=Lang.`obj_id`
						               		) WHERE Lang.lang_id={$module_lang_id} AND Main.paragraph_id={$row['paragraph_id']}")or die(mysql_error());
			$line_num=1;
			$link_array[$row['paragraph_id']]=array();

			while($row2=$Db->get_stream($result2)){
				if ($line_num==9){
					$line_num=1;
				}

				$my_num=(int)$row2['id'];
				$link_array[$row['paragraph_id']][$my_num]=array();
				$link_array[$row['paragraph_id']][$my_num]['id']=$row2['id'];

				$link_array[$row['paragraph_id']][$row2['id']]['title']=$row2['content'];
				//$link_array[$row['paragraph_id']][$row2['id']]['order_num']=$row2['order_num'];
				$link_array[$row['paragraph_id']][$row2['id']]['link']=$row2['link'];
				$link_array[$row['paragraph_id']][$row2['id']]['no_follow']=$row2['no_follow'];
				$link_array[$row['paragraph_id']][$row2['id']]['display']=$row2['display'];
				$link_array[$row['paragraph_id']][$row2['id']]['paragraph']=$row['paragraph_name'];

				$line_num++; }

		}

		@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/footerLinks.inc.php');
		updateStaticFile($link_array,
			'/_static/footerLinks.'.$languagesArr[$module_lang_id]['title'].'.inc.php',
			'footerlinkArr');

		@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/footers.inc.php');
//				updateStaticFile("SELECT `id`,`paragraph_name` FROM  `{$this->_Proccess_Main_DB_Table}` ",
		updateStaticFile("SELECT Main.id,Lang.paragraph_name FROM `{$this->_Proccess_Main_DB_Table}` AS Main
						         	LEFT JOIN `{$this->_Proccess_Main_DB_Table}_paragraph_lang` AS Lang ON (
						               			Main.`id`=Lang.`obj_id`
						               		) WHERE Lang.lang_id={$module_lang_id} ",
			'/_static/footers.'.$languagesArr[$module_lang_id]['title'].'.inc.php',
			'footersArr','id',true,true);

	}

	/*----------------------------------------------------------------------------------*/

	function updateAllStaticsFiles(){
		$this->updateStatics();
		parent::writeUpdate();
	}

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