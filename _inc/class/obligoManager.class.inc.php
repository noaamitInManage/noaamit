<?

/**
 * @author : Gal Zalait
 * @desc : genric obligo system
 * @var : 1.0
 * @last_update :  01/02/2016
 * @example : $Config = new configManager();
 *
 */
class obligoManager extends BaseManager
{

    public $defualt_valid_until_ts = '';
    /**
     *
     *
     * CREATE TABLE IF NOT EXISTS `tb_user_obligo` (
     * `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
     * `user_id` int(10) unsigned NOT NULL COMMENT 'link to tb_users',
     * `original_sum` float unsigned NOT NULL,
     * `current_amount` float unsigned NOT NULL,
     * `valid_until_ts` int(10) unsigned NOT NULL,
     * `from` varchar(255) ,
     * `last_update` int(10) unsigned NOT NULL,
     * PRIMARY KEY (`id`),
     * KEY `user_id` (`user_id`,`valid_until_ts`,`from`,`last_update`),
     * KEY `current_amount` (`current_amount`),
     * KEY `original_sum` (`original_sum`)
     * ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
     */
    public $tb_name = 'tb_obligo'; //tb_obligo


    //-------------------------------------------------------------------------------------------------------------------//

    function __construct()
    {
        parent::__construct();

        $this->defualt_valid_until_ts = strtotime("+ 10 years");
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

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * return all transcation history
     */
    public function get_user_obligo_history()
    {
        $itemsArr = array();
        $query = "SELECT * FROM `{$this->tb_name}` 
                  ORDER BY `last_update`
               ";
        $result = $this->db->query($query);
        while ($row = $this->db->get_stream($result)) {
            $itemsArr[] = $row;
        }
        return $itemsArr;
    }
    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * return user current available amount
     */
    public function get_user_current_amount($user_id)
    {

        $query = "SELECT SUM(`current_amount`) AS 'amount' 
                  FROM `{$this->tb_name}`
                     WHERE `user_id` = {$user_id}               
               ";
        $result = $this->db->query($query);
        $row = $this->db->get_stream($result);

        return $row['amount'];
    }

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * @param $user_id
     * @param $amount
     * @param $valid_until_ts
     * @param string $from
     */
    public function load_to_obligo_card($user_id, $amount, $valid_until_ts = '', $from = "")
    {

        $db_fields = array(
            "user_id" => $user_id,
            "original_sum" => $amount,
            "current_amount" => $amount,
            "valid_until_ts" => ($valid_until_ts) ? $valid_until_ts : $this->defualt_valid_until_ts,
        );

    }
    //-------------------------------------------------------------------------------------------------------------------//
    //-------------------------------------------------------------------------------------------------------------------//
    /**
     * charge  also write to log
     */
    public function charge_obligo_card($amount, $order_id, $user_id, $free_txt)
    {
        $ts = time();
        /***/
        //add in here
        /***/
        $current_amount = $this->get_user_current_amount($user_id);
        if ($current_amount < $amount) {
            return array(
                "code" => 20,
                "err" => "Lack of cash"
            );
        }

        /** write to log */
        $db_fields = array(
            "amount" => $amount,
            "order_id" => $order_id,
            "user_id" => $user_id,
            "free_txt" => $free_txt,
            "ip" => $_SERVER['REMOTE_ADDR'],
            "last_update" => $ts,
        );
        $this->db->insert("tb_obligo_use_log", $db_fields);
        /** end write to log */
    }

    //-------------------------------------------------------------------------------------------------------------------//
    //-------------------------------------------------------------------------------------------------------------------//

}

?>
