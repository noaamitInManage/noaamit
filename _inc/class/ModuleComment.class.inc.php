<?php

class ModuleComment extends BaseManager
{

    const tb_name = 'tb_contact_messages';

    private $obj_id;
    private $mdl_id;

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

    private static function getAllComments($obj_id)
    {
        $Db = Database::getInstance();

        $query = "SELECT * FROM  `" . self::tb_name . "` WHERE `contact_id`='{$obj_id}' ORDER BY `last_update` DESC"; /*`mdl_id`='{$mdl_id}' AND */
        return $result = $Db->query($query);

    }

    /*----------------------------------------------------------------------------------*/

    public static function addComment($sys_user_id, $obj_id, $content, $sent_to_user = 0)
    {
        $Db = Database::getInstance();

        $db_fields = array(
            "sys_user_id" => $sys_user_id,
            "contact_id" => $obj_id,
            "message" => urldecode(strip_tags($content)),
            "sent_to_user" => $sent_to_user,
            "last_update" => time(),
        );

        $tb_name = self::tb_name;
        $res = $Db->insert($tb_name, $db_fields);
    }

    /*----------------------------------------------------------------------------------*/

    public static function drawAllcomments($obj_id)
    {
        $Db = Database::getInstance();

        include_once($_SERVER['DOCUMENT_ROOT'] . "/salat2/_static/sysusers.inc.php"); // $sysusersArr
        $result = self::getAllComments($obj_id);
        $html = '';
        while ($row = $Db->get_stream($result)) {
            $sent = ($row["sent_to_user"] == 1) ? "&nbsp;&nbsp; | &nbsp;&nbsp;נשלח למשתמש" : "";
            $html .= '
         <div style="clear:both;"></div>
         <div class="div_notes">
            <div class="notes_title">
                <img title="מחיקת הודעה" alt="מחיקת הודעה" class="del_note del-ico" src="../images/no.gif" rel="' . $row['id'] . '" style="cursor:pointer;">'
                . $sysusersArr[$row['sys_user_id']]["fullname"] . '&nbsp;&nbsp; | &nbsp;&nbsp;' .
                date('j/m/y [H:i:s]', $row['last_update']) . $sent . '
            </div>
            <div class="notes_text">
               <b>תוכן ההודעה:</b>' . str_replace("\\n", "<br/>", $row['message']) . '
            </div>
         </div>
         <br/>
          ';
        }
        return $html;
    }

    /*----------------------------------------------------------------------------------*/

    public static function haveComment($mdl_id, $obj_id)
    {
        $Db = Database::getInstance();

        $query = "SELECT * FROM  `" . self::tb_name . "` WHERE `contact_id`='{$obj_id}' LIMIT 0,1";/*`mdl_id`='{$mdl_id}' AND */
        $result = $Db->query($query);
        return $result->num_rows;
    }

}

?>