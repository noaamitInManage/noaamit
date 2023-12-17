<?php

/**
 * Created by JetBrains PhpStorm.
 * @User: gal
 * @Date: 24/06/13
 * @Time: 14:33
 * To change this template use File | Settings | File Templates.
 *
 * @version : 1.0
 *
 *
 * @example:<code> $Api = new apiManager($platform,$version,$method_name); // first call will run the method
 * 					if we need to run more method in this script we can call to :
 * 			 $Api->execute('some_method');
 * 		</code>
 * @template method:
 * <code>
 * 					public function getTemplateFunction(){
 * 						$item_id = intval($_POST['id']);
 * 						if(!$item_id){
 * 							return (json_encode(array("err"=>"unvalid param","status"=>"","message"=>"קלט לא תקין")));
 * 						}
 *						switch($this->version){
 *						case '1.0':
 *						default:
 * 							$Template_controller = new Template_controllerManager($item_id);
 * 							return (json_encode(array("err"=>"","status"=>"1","message"=>$Template_controller->content)));
 *						break;
 *
 *						}
 *					}
 * </code>
 *	@Last_modified:
 */

class websiteApiManager
{

    public $platform='website';
    public $resolution='';
    public $version=2.0;
    public $method_name='';
    public $application_version='';


    public $method_lock_by_ipArr=array(
    );

    public $allowed_ipsArr=array(
    );

    private $ts = 0;

    /*----------------------------------------------------------------------------------*/
    /**
     *
     */
    function __construct(){

        $this->ts = time();

        if(!isset($_SESSION['lang_id'])){
            $_SESSION['lang_id'] = 1;
            $_SESSION['lang'] = 'he';
        }
        if(isset($_SESSION['api']['resolution']) && $_SESSION['api']['resolution']){
            $this->resolution = $_SESSION['api']['resolution'];
        }
        if(isset($_SESSION['api']['application_version']) && $_SESSION['api']['application_version']){
            $this->application_version = $_SESSION['api']['application_version'];
        }
        if(in_array($this->method_name,$this->method_lock_by_ipArr)){
            if(!in_array($_SERVER['REMOTE_ADDR'],$this->allowed_ipsArr)){
                exit (json_encode(array("err"=>"Unauthorized access","status"=>"0","message"=>"Unauthorized access")));
            }
        }


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

    protected  function check_method($method_name){
        if(method_exists($this,$method_name)){
            return true;
        }else{
            return false;
        }
    }

    /*----------------------------------------------------------------------------------*/
    /**
     *	 @example: $Api->execute('getStartUpMsg');
     * 	 @return value: {"id":2,"message":"..."}
     *
     */
    public function execute($method_override=''){

        $method_name =  ($method_override) ? $method_override :  $this->method_name;

        $Api = new apiManager($this->platform,$this->version,$method_name);

        // save details to sent is letter when user confirm pin code
        if($method_name == "addUser") {
            $_SESSION['register_email'] = $_REQUEST['email'];
            $_SESSION['register_pass'] = $_REQUEST['pass'];
        }
        if($method_name == "activateUser") {
            $_REQUEST['email'] = $_SESSION['register_email'];
            $_REQUEST['password'] = $_SESSION['register_pass'];
        }

        // reset delivery address before do a set_store
        if($method_name == "setStoreByAddress") {
            unset($_SESSION['cart']['delivery']);
        }

        $answer = $Api->execute();


        if(method_exists($this,$method_name)){
            return $this->{$method_name}($answer);
        }else {
            return $answer;
        }
    }
}