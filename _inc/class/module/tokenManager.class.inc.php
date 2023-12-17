<?

/**
 * @author : Netanel hadad
 * @desc : verify api call from client
 * @var : 1.0
 * @last_update :  18/04/2016
 */

class tokenManager{

    public $token;

    private static $instnace = null;
    private $ts=0;

    /*----------------------------------------------------------------------------------*/

    function __construct(){

        $this->ts = time();
        $this->token = isset($_SESSION['token_api']) ? $_SESSION['token_api'] : '';

    }

    /*----------------------------------------------------------------------------------*/


    public static function getInstance(){
        if (null === self::$instnace) {
            self::$instnace = new tokenManager();
        }else{
        }
        return self::$instnace ;
    }

    /*----------------------------------------------------------------------------------*/

    function __destruct(){

    }

    /*----------------------------------------------------------------------------------*/

    public function __set($var, $val){
        $this->$var = $val;
    }

    /*----------------------------------------------------------------------------------*/

    public function __get($var){
        return $this->$var;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @method: get_token
     * on the first method of api, client ask for token, and use it in all of api calls
     */
    public function get_token() {
        if(!$this->token) {
            $this->token = $this->create_token();
            $_SESSION['token_api'] = $this->token;
        }
        return $this->token;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @method: create_token
     * create the token by uniq md5 code
     */
    private function create_token() {
        $token = md5(uniqid(rand(), true));
        return $token;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @method: verify_token
     * each call, ApiManager need to verify the token
     */
    public function verify_token($token) {
        if(!empty($token) && $token == $this->token) {
            return true;
        } else {
            return false;
        }
    }



}

?>