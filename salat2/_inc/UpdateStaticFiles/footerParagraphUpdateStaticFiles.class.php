<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 * 
 */


//---------------------------------------------------------------------------//

class footerParagraphUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

	//public $_Proccess_Main_DB_Table,$_ProcessID;
	public $className,$file_name,$itemsArr_name;


	//------------------------------------ FUNCTIONS ------------------------------------

	function __construct(){
		parent::__construct();
		$this->_Proccess_Main_DB_Table = 'tb_footer_paragraph';
		$this->_ProcessID = 13;
		$this->className= trim(get_class());
		$this->file_name='footerParagraph.inc.php';
		$this->itemsArr_name='footerParagraphArr';
		$this->name='קטגוריות פוטר';
	}

	/*----------------------------------------------------------------------------------*/

	function updateStatics(){


		@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/footerParagraph.inc.php');
		updateStaticFile("SELECT `id`,`paragraph_name` FROM  `{$this->_Proccess_Main_DB_Table}` ",
			'/_static/footerParagraph.inc.php',
			'footerParagraphArr','id',true,true);


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