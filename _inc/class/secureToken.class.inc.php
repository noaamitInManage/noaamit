<?

/**
 * @author : Nechami Karelitz
 * @desc : manage secure token for api
 * @var : 1.0
 * @last_update :  09/05/2016
 * @example : $Token = new secureToken();
 *
 */
class secureToken extends BaseManager
{

    const prefix = "sa_to_";
    const suffix = "_lat_ken";

    const header_key = "TOKEN";
    const header_value = "inmanga_secure";

    function __construct($item_id, $lang = '')
    {
        parent::__construct();
    }

    //-------------------------------------------------------------------------------------------------------------------//

    function __destruct()
    {

    }

    //-------------------------------------------------------------------------------------------------------------------//

    public function __set($var, $val)
    {
        $this->$var = $val;
    }


    //-------------------------------------------------------------------------------------------------------------------//


    public function __get($var)
    {
        return $this->$var;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: create_token
     *
     * create token in db and store in session
     *
     * @expect_values :
     * $udid; Required String: "123"
     *
     * @return null
     */

    public static function create_token($udid)
    {
        $Db = Database::getInstance();

        $ts = time();
        $detailsArr = array(
            $udid,
            $ts
        );

        $token = hash("sha256", self::prefix . implode("_", $detailsArr) . self::suffix);

        $db_fieldsArr = array(
            'active' => 0,
            'last_update' => $ts,
        );
        $Db->update('tb_secure_tokens_log', $db_fieldsArr, 'udid', $udid);

        $db_fieldsArr = array(
            "udid" => $udid,
            "token" => $token,
            "created_ts" => $ts,
            "active" => 1,
            "last_update" => $ts
        );

        $Db->insert('tb_secure_tokens_log', $db_fieldsArr);

        $_SESSION["token"] = $token;
        $_SESSION["udid"] = $udid;
        return $token;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: get_token
     *
     * get token stored in db, for udid
     *
     * @expect_values :
     * $udid; Required String: "123"
     *
     * @return String / false
     * @example:
     * Case 1 (error):
     * false - no token at db
     *
     * @example:
     * Case 2 (success):
     * "7b27d6a54503d3bc49f4e1d7043f85c247424ac7e24921b57a02227c7de37a60" - token string
     */

    public static function get_token($udid)
    {
        $Db = Database::getInstance();

        $query = "
            SELECT `token`
                FROM `tb_secure_tokens_log`
                    WHERE `udid` = '{$udid}'
                        AND `active` =1
        ";
        $result = $Db->query($query);

        if ($result->num_rows == 0) {
            return false;
        }

        $row = $Db->get_stream($result);
        return $row['token'];
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method: validate_token
     *
     * check if token in session is same as token in db
     *
     * @expect_values :
     * null
     *
     * @return Boolean
     */

    public static function validate_token()
    {
        if (isset($_SESSION['token']) && ($_SESSION['token']) && isset($_REQUEST['applicationToken']) && ($_REQUEST['applicationToken']) && isset($_SESSION['udid']) && ($_SESSION['udid'])) {
            $_REQUEST['applicationToken'] = siteFunctions::safe_value($_REQUEST['applicationToken'], "text");
            $db_token = self::get_token($_SESSION['udid']);
            if ($db_token !== false && $_SESSION['token'] === $db_token && $_REQUEST['applicationToken'] === $db_token) {
                return true;
            }
        }
        return false;
    }
}

?>
