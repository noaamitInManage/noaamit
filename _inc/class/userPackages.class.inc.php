<?

class userPackages extends BaseManager
{


    public $user_id;

    /*----------------------------------------------------------------------------------*/

    function __construct()
    {
        parent::__construct();
    }

    /*----------------------------------------------------------------------------------*/

    function __destruct()
    {

    }

    /*----------------------------------------------------------------------------------*/

    public function __set($var, $val)
    {
        $this->$var = $val;
    }

    /*----------------------------------------------------------------------------------*/

    public function __get($var)
    {
        return $this->$var;
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * add packages to user
     *
     * @param unknown_type $package_type
     * @param unknown_type $package_id
     * @param unknown_type $user
     * @param unknown_type $custom_pacakage
     */

    public static function addPackage($package_type, $package_id, $user)
    {
        global $packages_types;

        $Db = Database::getInstance();

        include($_SERVER['DOCUMENT_ROOT'] . '/_static/packages/package-' . $package_id . '.inc.php');  //$packageArr

        $db_fields = array(
            "package_id" => $package_id,
            "user_id" => $user,
            "package_type" => $package_type,
            "purchese_ts" => time(),
            "last_update" => time(),
        );
        $package_items = array(
            $packageArr["{$packages_types[$package_type]['prefix']}_reg_post"],
            $packageArr["{$packages_types[$package_type]['prefix']}_pink_post"],
            $packageArr["{$packages_types[$package_type]['prefix']}_orange_post"],
        );

        list($db_fields['reg_count'], $db_fields['pink_count'], $db_fields['orange_count']) = $package_items;

        $res = $Db->insert('tb_packages_objects', $db_fields);

        return $res;
    }


    public static function deletePackage($id)
    {

    }

    public function getUserPackages($user_id, $category_id)
    {
        $query = " SELECT * FROM `tb_packages_objects` AS PackagesLink
                  LEFT JOIN `tb_packages` AS Pack ON (
                      Pack.`id`=PackagesLink.`package_id`
                  )
                     WHERE Pack.`category_id`='{$category_id}' AND PackagesLink.`user_id`='{$user_id}'
	   ";
        return $this->db->query($query);
    }


}

?>