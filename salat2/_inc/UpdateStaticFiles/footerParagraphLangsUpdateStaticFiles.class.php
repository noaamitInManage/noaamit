<?php
//---------------------------------------------------------------------------//

class footerParagraphLangsUpdateStaticFiles extends moduleUpdateStaticFiles  implements iUpdate{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className,$file_name,$itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct(){
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_footer_paragraph';
        $this->_ProcessID = 13;
        $this->className= trim(get_class());
        $this->file_name='footer_paragraph.inc.php';
        $this->itemsArr_name='footerParagraphArr';
        $this->name='קטגוריות פוטר';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id=''){
        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'] ;
        global $languagesArr,$module_lang_id;


        $smart_dir=parent::smartLangDirctory('/_static/footerParagraph/',$_REQUEST['inner_id'],$module_lang_id);
        @unlink($_SERVER['DOCUMENT_ROOT'].'/_static/footer_paragraphs.'.$languagesArr[$module_lang_id]['title'].'.inc.php');

        updateStaticFile("SELECT Main.id,Lang.paragraph_name FROM `{$this->_Proccess_Main_DB_Table}` AS Main
						         	LEFT JOIN `{$this->_Proccess_Main_DB_Table}_lang` AS Lang ON (
						               			Main.`id`=Lang.`obj_id`
						               		) WHERE Lang.lang_id={$module_lang_id}

         	",
            '/_static/footer_paragraphs.'.$languagesArr[$module_lang_id]['title'].'.inc.php',
            'footerParagraphsArr','id',true,true);


        if($_REQUEST['inner_id']) {
            @unlink($_SERVER['DOCUMENT_ROOT'].$smart_dir.'footerParagraph-'.$_REQUEST['inner_id'].'.inc.php');
            $sql = "SELECT
							 Paragraph.id,
							 ParagraphInfo.paragraph_name,
							 Item.id,
							 CONCAT('return array(',
							 GROUP_CONCAT(
								 DISTINCT(Ordering.order_num),'=>array(
									 \"content\"=>','\"',ItemInfo.content,'\",',
									 '\"link\"=>\"',ItemInfo.link,'\",',
									 '\"display\"=>\"',ItemInfo.display,'\",',
									 '\"no_follow\"=>\"',ItemInfo.no_follow,'\"',
									')'),');') as paragraph_items
							 FROM `tb_footer_paragraph` AS Paragraph
								 LEFT JOIN `tb_footer_paragraph_lang` AS ParagraphInfo ON Paragraph.id=ParagraphInfo.obj_id
								 LEFT JOIN (SELECT * FROM `tb_footer_ordering` ORDER BY `order_num`) AS Ordering ON Paragraph.id=Ordering.group_id
								 LEFT JOIN `tb_footer` AS Item ON Ordering.item_id=Item.id
								 LEFT JOIN `tb_footer_lang` AS ItemInfo ON Item.id=ItemInfo.obj_id
							 WHERE Paragraph.id={$_REQUEST['inner_id']}";

            updateStaticFile($sql,
                $smart_dir.'footerParagraph-'.$_REQUEST['inner_id'].'.inc.php',
                'footerParagraphArr');
        }
    }

    /*----------------------------------------------------------------------------------*/

    function updateAllStaticsFiles(){
        global $Db;
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