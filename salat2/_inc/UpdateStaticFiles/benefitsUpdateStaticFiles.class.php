<?php

class benefitsUpdateStaticFiles extends moduleUpdateStaticFiles implements iUpdate
{

    //public $_Proccess_Main_DB_Table,$_ProcessID;
    public $className, $file_name, $itemsArr_name;


    //------------------------------------ FUNCTIONS ------------------------------------

    function __construct()
    {
        parent::__construct();
        $this->_Proccess_Main_DB_Table = 'tb_benefits';
        $this->_ProcessID = 1;
        $this->className = trim(get_class());
        $this->file_name = 'benefits.inc.php';
        $this->itemsArr_name = 'benefitsArr';
        $this->name = 'Benefits';
    }

    /*----------------------------------------------------------------------------------*/

    function updateStatics($id = '')
    {
        $Db = Database::getInstance();

        $_REQUEST['inner_id'] = ($id) ? $id : $_REQUEST['inner_id'];
        $smart_dir = parent::smartDirctory('/_static/benefits/', $_REQUEST['inner_id']);
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/benefits.inc.php');
        updateStaticFile("SELECT `id`, `excerpt` FROM `{$this->_Proccess_Main_DB_Table}`",
            '/_static/benefits.inc.php',
            'benefitsArr', 'id', true);

        $benefits_by_citiesArr = $this->get_benefits_by_cities_and_categoriesArr();
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/benefits_by_cities.inc.php');
        updateStaticFile($benefits_by_citiesArr,
            '/_static/benefits_by_cities.inc.php',
            'benefitsArr', 'id', true);

        $citiesArr = $this->get_citiesArr();
        @unlink($_SERVER['DOCUMENT_ROOT'] . '/_static/cities.inc.php');
        updateStaticFile($citiesArr,
            '/_static/cities.inc.php',
            'citiesArr');

        if ($_REQUEST['inner_id']) {
            $this->update_static_file($_REQUEST['inner_id']);
        }
    }

    /*----------------------------------------------------------------------------------*/

    function updateAllStaticsFiles()
    {
        $Db = Database::getInstance();

        $this->updateStatics();


        $query = " SELECT id FROM `tb_benefits`";
        $result = $Db->query($query);

        while ($row = $Db->get_stream($result)) {
            $this->updateStatics($row['id']);
        }

        parent::writeUpdate();
    }

    /*----------------------------------------------------------------------------------*/

    public function update_static_file($item_id)
    {
        $smart_dir = parent::smartDirctory('/_static/benefits/', $item_id);
        @unlink($_SERVER['DOCUMENT_ROOT'] . $smart_dir . 'benefit-' . $item_id . '.inc.php');
        $benefitArr = $this->get_item_staticArr($item_id);
        updateStaticFile($benefitArr,
            $smart_dir . 'benefit-' . $item_id . '.inc.php',
            'benefitArr');
    }

    /*----------------------------------------------------------------------------------*/

    public function get_item_staticArr($item_id)
    {
        $Db = Database::getInstance();
        $ts = time();

        $sql = "SELECT * FROM `tb_benefits` WHERE `id` = {$item_id}";
        $result = $Db->query($sql);
        $benefitArr = $Db->get_stream($result);

        return $benefitArr;
    }

    /*----------------------------------------------------------------------------------*/

    public function get_benefits_by_cities_and_categoriesArr()
    {
        $Db = Database::getInstance();

        $benefitsArr = array();

        $sql = "
            SELECT Benefit.`id`, Cities.`cities`, Categories.`categories`
            FROM `tb_benefits` AS Benefit
              LEFT JOIN (
                SELECT GROUP_CONCAT(DISTINCT(`city_key`)) AS `cities`, `benefit_id` FROM `tb_benefits__cities_link` GROUP BY `benefit_id`
              ) AS Cities ON Cities.`benefit_id` = Benefit.`id`
              LEFT JOIN (
                SELECT GROUP_CONCAT(DISTINCT(`category_id`)) AS `categories`, `benefit_id` FROM `tb_benefits__categories_link` GROUP BY `benefit_id`
              ) AS Categories ON Categories.`benefit_id` = Benefit.`id`
            ORDER BY Benefit.`order_num`
        ";
        $result = $Db->query($sql);
        while ($rowArr = $Db->get_stream($result)) {
            if (!$rowArr['categories'] || !$rowArr['cities']) {
                continue;
            }

            $citiesArr = explode(',', $rowArr['cities']);
            $category_idsArr = explode(',', $rowArr['categories']);

            foreach ($citiesArr as $city_key) {
                foreach ($category_idsArr as $category_id) {
                    $benefitsArr[$city_key][$category_id]['benefitsArr'][] = $rowArr['id'];
                }
            }
        }

        foreach ($benefitsArr as $city_key => $cityArr) {
            foreach ($cityArr as $category_id => $categoryArr) {
                $benefitsArr[$city_key][$category_id]['count'] = count($benefitsArr[$city_key][$category_id]['benefitsArr']);
            }
        }

        return $benefitsArr;
    }

    /*----------------------------------------------------------------------------------*/

    public function get_citiesArr()
    {
        $Db = Database::getInstance();

        $citiesArr = array();

        $sql = "
            SELECT `city_key`, `city_title` FROM `tb_benefits__cities_link` GROUP BY `city_title` ORDER BY `city_key`
        ";
        $result = $Db->query($sql);
        $order_num = 1;
        while ($rowArr = $Db->get_stream($result)) {
            $citiesArr[$rowArr['city_key']] = array(
                'key' => $rowArr['city_key'],
                'title' => $rowArr['city_title'],
                'order_num' => $order_num,
            );

            $order_num++;
        }

        return $citiesArr;
    }

    /*----------------------------------------------------------------------------------*/

    public function getItemsNumber()
    {
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/' . $this->file_name);//$this->itemsArr_name(){
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/' . $this->file_name);//$this->itemsArr_name
        $tmp = $this->itemsArr_name;
        return count($$tmp);
    }


}