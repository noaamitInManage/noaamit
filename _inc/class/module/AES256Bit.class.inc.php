<?php

/**
 *
 * AES256Bit
 *
 *
 *  256 bit AES (Advanced Encryption Standard)
 *
 * @User: david
 * @Date: 28/04/14
 * @Time: 12:30
 *
 *
 *
 * @version : 1.0
 *
 *
 * @last_update :  28/04/2014
 *
 *
 * @example : $Aes = new AES256Bit("hello world!");
 *
 */


class AES256Bit {

    /**
     * 256 bit key
     * @var string - key must to be  string 32 char length
     */
    // private $key = "afkinmanage167mcdonaldsadww76345";
    private $key = "afkinmanage167mc";
    /**
     * Encryption mode.
     * This AES supports only in ECB.
     * @var string
     */
    private $mode = "ECB";
    /**
     * Holder class of ASE
     * @var AES
     */
    private $aes = '';
    /**
     * When the encryption start (time)
     * @var string
     */
    private $start_encrypt = '';
    /**
     * When the encryption end (time)
     * @var string
     */
    private $end_encrypt = '';
    /**
     * input value to encrypt
     * @var string
     */
    public $input = '';
    /**
     * The encrypt value
     * @var string
     */
    public $encrypt_value = '';
    /**
     * The decrypt value
     * @var string
     */
    public $decrypt_value = '';


    /*----------------------------------------------------------------------------------*/

    /**
     *
     * Method name: __construct
     *
     * Get input value and create new AES object.
     *
     * @expect_values :
     * @param $input Required string (example: "hello world!")
     *
     */
    function __construct($key=''){
        if($key){
            $this->key=$key;
        }

        $this->Aes = new AES($this->key, $this->mode);
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
     *
     * Method name: encrypt
     *
     * Encrypt the input value that get in the constructor.
     *
     */
    /*   public function encrypt($value){
           return $this->Aes->encrypt($value);
       }*/

    /*----------------------------------------------------------------------------------*/

    /**
     *
     * Method name: decrypt
     *
     * decrypt the encrypt value.
     *
     */
    /*   public function decrypt($value){
           return  $this->Aes->decrypt($value);
       }*/

    /*----------------------------------------------------------------------------------*/

    function encrypt($str) {
        $key = $this->hex2bin($this->key);

        $td = mcrypt_module_open("rijndael-128", "", "cbc", "fedcba9876543210");

        mcrypt_generic_init($td, $key, "fedcba9876543210");

        $encrypted = mcrypt_generic($td, $str);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return bin2hex($encrypted);
    }
    /*----------------------------------------------------------------------------------*/
    function hex2bin($hexdata) {
        $bindata = "";

        for ($i = 0; $i < strlen($hexdata); $i += 2) {
            $bindata .= chr(hexdec(substr($hexdata, $i, 2)));
        }

        return $bindata;
    }

    /*----------------------------------------------------------------------------------*/

    function decrypt($code) {
        $key = $this->hex2bin($this->key);
        $code = $this->hex2bin($code);

        $td = mcrypt_module_open("rijndael-128", "", "cbc", "");

        mcrypt_generic_init($td, $key, "fedcba9876543210");
        $decrypted = mdecrypt_generic($td, $code);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

        return utf8_encode(trim($decrypted));
    }
    /*----------------------------------------------------------------------------------*/

}
