<?php
/**
 * Created by PhpStorm.
 * User: gal
 * Date: 16/05/14
 * Time: 14:02
 *
 */


/**
 * @author : gal zalait
 * @desc : SMS sender MAnager send sms via inforUmobile Api
 * @var : 1.0
 * @last_update : 16.05.2014
 */
class smsManager extends BaseManager
{
    public $url = 'http://api.inforu.co.il/SendMessageXml.ashx?InforuXML=';
    public $sms_host = "api.inforu.co.il"; // Application server's URL;
    public $sms_port = '80';
    public $sms_path = "/SendMessageXml.ashx"; // Application server's PATH;
    public $sender_name = "###"; // Application server's PATH;
    public $werite_to_log = true; // if yes write sms log to tb_sms_log
    public $market = false; // if yes write sms log to tb_sms_log

    /*----------------------------------------------------------------------------------*/

    function __construct($user = '###', $password = '###', $sender_num = '###', $sender_name = '###')
    {
        parent::__construct();

        $this->user_name = $user;
        $this->password = $password;
        $this->sender_num = $sender_num;
        $this->sender_name = $sender_name;
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

    public function set_market()
    {
        $this->sender_name = '099512824';
        $this->sender_num = '099512824';

        return $this->market = true;

    }

    /*----------------------------------------------------------------------------------*/

    public function send($msg, $phoneArr)
    {

        if ($this->market) {
            $msg .= " " . lang('remove_market_sms_txt');

        }
        $msg = str_replace(array('<', '>', '\"', "\'", "&", "\r\n"), array('%26lt;', '%26gt;', '%26quot;', '%26apos;', '%26amp;', '%0D%0A'), $msg);
        if (in_array($_SERVER['REMOTE_ADDR'], array('62.219.212.139', '81.218.173.175'))) {
            //	die('<hr /><pre>' . print_r($this->get_xml($msg,$phoneArr), true) . '</pre><hr />');
        }
        $url = $this->url . urlencode($this->get_xml($msg, $phoneArr));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_NOBODY, FALSE); // remove body
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);
        //	mail('gal@inmanage.co.il','sms - '.__FILE__,print_r(array($response,$msg,$phoneArr,__FILE__,__LINE__,__CLASS__,__METHOD__,__FUNCTION__),true),'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);
        if (strstr($response, ' success')) {
            return array(true, $response);
        } else {
            siteFunctions::send_sms_error($url, $response, implode(',', $phoneArr));
            return false;
        }

    }


    /*----------------------------------------------------------------------------------*/
    public function get_xml($message, $phoneArr)
    {
        $phone = (is_array($phoneArr)) ? implode(';', $phoneArr) : $phoneArr; //  0501111111;0502222222
        return <<<XML
<Inforu>
 <User>
 <Username>{$this->user_name}</Username>
 <Password>{$this->password}</Password>
 </User>
 <Content Type="sms">
 <Message>{$message}</Message>
 </Content>
 <Recipients>
 <PhoneNumber>{$phone}</PhoneNumber>
 </Recipients>
 <Settings>
 <SenderName>{$this->sender_name}</SenderName>
 <SenderNumber>{$this->sender_num}</SenderNumber>
 </Settings>
</Inforu>


XML;

    }

    /*----------------------------------------------------------------------------------*/

    public function write_answer_to_log($phone_num, $message)
    {
        $tb_name = 'tb_sms_return_log';
        $db_fields = array(
            "num" => $phone_num,
            "message" => $message,
            "last_update" => time(),
        );

        return mcdonaldsManager::insert_to_db($tb_name, $db_fields);
    }

    /*----------------------------------------------------------------------------------*/

    public function remove_user_from_market_sms($phone_num, $message)
    {
        $remove_key_word = lang('remove_key_word');
        header('Content-type: text/html;charset=utf-8');
        if (strstr($message, $remove_key_word)) {
            if ($phone_num) {
                $db_fieldsArr = array(
                    'newsletter' => 0
                );
                $this->db->update('tb_users', $db_fieldsArr);
                return true;
            }
        }
        return false;
    }

    /*----------------------------------------------------------------------------------*/

}