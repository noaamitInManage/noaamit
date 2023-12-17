<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 *
 */


//---------------------------------------------------------------------------//

class translateUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

	//public $_Proccess_Main_DB_Table,$_ProcessID;
	public $className,$file_name,$itemsArr_name;


	//------------------------------------ FUNCTIONS ------------------------------------

	function __construct(){
		parent::__construct();
		$this->_Proccess_Main_DB_Table = 'tb_translate';
		$this->_ProcessID = 68;
		$this->className= trim(get_class());
		$this->file_name='translations.inc.php';
		$this->itemsArr_name='translateArr';
		$this->name='תרגומים';
	}

	/*----------------------------------------------------------------------------------*/

	function updateStatics($id=''){
		global $languagesArr;

		foreach ($languagesArr as $lang_id => $lang) {
			@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/translations.'.$lang['title'].'.inc.php');
			updateStaticFile("SELECT key_code,text FROM `tb_translate` WHERE `lang_id`={$lang_id} ",
				'/_static/translations.'.$lang['title'].'.inc.php',
				'translationsArr','key_code',true);

			@unlink($_SERVER['DOCUMENT_ROOT'].'/_static/translations_gd.'.$lang['title'].'.inc.php');
			updateStaticFile("SELECT key_code,text FROM `tb_translate` WHERE `lang_id`={$lang_id} AND `show_in_gd`=1",
				'/_static/translations_gd.'.$lang['title'].'.inc.php',
				'gd_translationsArr','key_code',true,true);

            $this->save_lang_json_file($lang_id, $lang['title']);
		}
	}

	/*----------------------------------------------------------------------------------*/

    public function save_lang_json_file($lang_id, $lang_title)
    {
        global $Db;

        $file_path = $_SERVER['DOCUMENT_ROOT'] . '/_static/translations.' . $lang_title . '.json';

        @unlink($file_path);
        $sql = "
            SELECT `key_code`, `text` FROM `tb_translate` WHERE `lang_id` = {$lang_id} AND `text` <> ''
        ";
        $res = $Db->query($sql);
        $translations_jsonArr = array();
        while ($rowArr = $Db->get_stream($res)) {
            $translations_jsonArr[$rowArr['key_code']] = $rowArr['text'];
        }

        $handle = fopen($file_path, 'w+');
        fwrite($handle, json_encode($translations_jsonArr));
        fclose($handle);
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

//---------------------------------------------------------------------------//


?>