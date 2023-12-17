<?

/**
 * @author : gal zalait
 * @desc : cookie manager
 * @var : 1.0
 * @last_update :  28/03/2012
 */
class cookieManager extends BaseManager
{

    const def_expire = 1209600;// 2 weeks

    /*----------------------------------------------------------------------------------*/

    function __construct()
    {
        parent::__construct(false);
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


    public static function getCookie($cookie_name)
    {
        return (isset($_COOKIE[$cookie_name]) ? $_COOKIE[$cookie_name] : '');
    }

    /*----------------------------------------------------------------------------------*/

    public static function setCookie($cookie_name, $value, $expire = 0)
    {
        $_COOKIE[$cookie_name] = $value;
        setcookie($cookie_name, $value, ($expire == 0 ? time() + 10000000 : $expire), '/', $_SERVER['HTTP_HOST']);
    }

    /*----------------------------------------------------------------------------------*/

    public static function delCookie($cookie_name)
    {
        $_COOKIE[$cookie_name] = '';
        setcookie($cookie_name, "", 1, '/', $_SERVER['HTTP_HOST']);
    }
}

?>