<?php
/**
 * Created by JetBrains PhpStorm.
 * User: netanel
 * Date: 03/12/13
 * Time: 14:44
 *
 */
//---------------------------------------------------------------------------//


class subSitesUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_sub_sites';
        $this->_ProcessID = template_ID;
        $this->className = trim(get_class());
        $this->file_name = 'sub-sites.inc.php'; // all Items  for count in moudle update_static
        $this->itemsArr_name = 'subSitesArr';
    }

    function updateStatics()
    {
        updateStaticFile("SELECT main.*, group_concat(lang_id) as langs
	        					FROM {$this->_Proccess_Main_DB_Table} as main
	        						LEFT JOIN tb_sub_sites_lang_link as link ON(
	        							main.id = link.site_id
	    							)
	    						GROUP BY main.id",
            '/_static/' . $this->file_name,
            $this->itemsArr_name, 'id', true);
    }

    function updateAllStaticsFiles()
    {
        $Db = Database::getInstance();

        $query = " SELECT id FROM {$this->_Proccess_Main_DB_Table}";
        $result = $Db->query($query);

        while ($row = $Db->get_stream($result)) {
            updateStaticFile("SELECT main.*, group_concat(lang_id) as langs
	        					FROM {$this->_Proccess_Main_DB_Table} as main
	        						LEFT JOIN tb_sub_sites_lang_link as link ON(
	        							main.id = link.site_id
	    							)
	    						WHERE main.id = '{$row['id']}'",
                '/_static/sub-sites/sub-site-' . $row['id'] . '.inc.php',
                'subSiteConfigArr');
        }
    }


}


?>