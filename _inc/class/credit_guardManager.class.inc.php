<?php

/**
 * Created by PhpStorm.
 * User: gal
 * Date: 13/05/14
 * Time: 13:29
 * @name  credit_guardManager
 * @author    Gal Zalait
 * @since    6.8.2014
 * @Dependence : xmlAssembly class
 * @Description:Credit-Guard API Inmanage Class
 * @chanage_log :
 *    7.08.14 - add cancel order  method
 */
class credit_guardManager extends BaseManager
{
    public $user = 'xxx';
    public $password = 'xxxx';
    public $main_mid = '11204';
    public $mid = '11204';
    public $main_terminal = '9498501'; // main token terminal
    public $terminal = '0962832';//'0962832'; // test terminal on the proudcation

    public $dev_mode = false;
    private $secured = false;
    private $secured_port = 8016;

    public $dev_midArr = array(
        'user' => 'xxxxx',
        'password' => 'xxx',
        'mid' => '1121', //11204
        'terminal' => '0962835',
        'cg_gateway_url' => 'https://cguat2.creditguard.co.il/xpo/Relay',
    );

    public $cg_gateway_url = 'https://kupot2.creditguard.co.il:8443/xpo/Relay';

    /**
     * @var bool
     * if yes every request and answer will  send mail
     */
    public $debug_mode = true;


    public $debug_emailsArr = array(
        'gal@inmanage.co.il',
        //		'mcmobile@mcdonalds.co.il',
        //		'avner@mcdonalds.co.il'
    );    //gal@inmanage.co.il

    // General Declaration
    /**
     * @var int time out between re try call
     */
    private $time_out = 3;

    /**
     * array(
     *    method_name => num of try
     * );
     * @var array
     */
    private $retry_methodArr = array(
        'j4' => 1,
    );

    /**
     * delay
     *
     * @var int white X second after $retry_methodArr method fail
     */
    private $retry_delay = 3;

    /**
     * @var string (options : ILS ,USD , EUR )
     */
    public $currency = 'ILS';

    /**
     * @var bool save token in {$this->tb_token}  table yes/no
     */
    public $save_token = true;
    /**
     * write request and response to  $tb_log
     */
    public $write_to_log = true;
    /**
     * write request and response to  $tb_broadcast_terminal
     */
    public $write_to_broadcast_log = true;
    /**
     * @var string
     *
     * DUMP:
     * CREATE TABLE IF NOT EXISTS `tb_cg__token` (
     * `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
     * `user_id` int(10) unsigned NOT NULL,
     * `mid` varchar(50) NOT NULL,
     * `title` varchar(255) NOT NULL,
     * `cardMask` varchar(50) NOT NULL,
     * `creditCompany` varchar(50) NOT NULL,
     * `cardExpiration` varchar(50) NOT NULL,
     * `token` varchar(255) NOT NULL,
     * `user_pid` varchar(50) NOT NULL,
     * `save_card` tinyint(1) unsigned NOT NULL DEFAULT '1',
     * `last_update` int(10) unsigned NOT NULL,
     * PRIMARY KEY (`id`),
     * UNIQUE KEY `user_id_3` (`user_id`,`mid`,`cardMask`,`cardExpiration`),
     * KEY `user_id` (`user_id`),
     * KEY `save_card` (`save_card`)
     * ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
     */

    public $tb_token = 'tb_cg__token';

    /**
     * @var string
     * DUMP :
     * CREATE TABLE IF NOT EXISTS `tb_cg__log` (
     * `id` int(10) unsigned NOT NULL,
     * `user_id` int(10) unsigned NOT NULL,
     * `request` text NOT NULL,
     * `response` text NOT NULL,
     * `method_name` varchar(255) NOT NULL,
     * `session_id` varchar(255) NOT NULL,
     * `status` int(10) unsigned NOT NULL,
     * `extra_fields` text NOT NULL,
     * `last_update` int(10) unsigned NOT NULL,
     * KEY `user_id` (`user_id`,`status`)
     * ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
     */

    public $tb_log = 'tb_cg__log';


    /**
     * @var string
     *
     * DUMP:
     * CREATE TABLE IF NOT EXISTS `tb_cg__orders` (
     * `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
     * `order_id` int(10) unsigned NOT NULL COMMENT 'link to id in tb_orders',
     * `j4_transaction_id` varchar(255) NOT NULL,
     * `token` varchar(255) NOT NULL,
     * `cardExpiration` varchar(50) NOT NULL,
     * `terminal` varchar(50) NOT NULL,
     * `sum` varchar(255) NOT NULL,
     * `status_id` int(10) unsigned NOT NULL COMMENT '1.success , 2.fail',
     * `last_update` int(10) unsigned NOT NULL,
     * PRIMARY KEY (`id`),
     * KEY `order_id` (`order_id`,`j4_transaction_id`,`status_id`)
     * ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
     */
    public $tb_orders = 'tb_cg__orders';

    /**
     * @var string
     * DUMP
     *
     *
     * CREATE TABLE IF NOT EXISTS `tb_cg__cancel` (
     * `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
     * `order_id` int(10) unsigned NOT NULL,
     * `cg_status` int(10) unsigned NOT NULL,
     * `cg_status_text` varchar(255) NOT NULL,
     * `tranId` int(10) unsigned NOT NULL,
     * `status` tinyint(3) unsigned NOT NULL,
     * `last_update` int(10) unsigned NOT NULL,
     * PRIMARY KEY (`id`),
     * KEY `order_id` (`order_id`,`status`)
     * ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
     */

    public $tb_cancel = 'tb_cg__cancel';

    /**
     * @var string
     * DUMP
     *
     *
     * CREATE TABLE IF NOT EXISTS `tb_cg__broadcast_terminal` (
     * `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
     * `terminal_num` varchar(255) NOT NULL,
     * `transmit_id` int(10) unsigned NOT NULL,
     * `answer` text NOT NULL,
     * `succeed` tinyint(2) unsigned NOT NULL COMMENT '0 - no, 1-yes',
     * `last_update` int(11) unsigned NOT NULL,
     * PRIMARY KEY (`id`)
     * ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
     */

    public $tb_broadcast_terminal = 'tb_cg__broadcast_terminal';


    /**
     * @var string (cg api version)
     */
    public $version = "1000";
    /**
     * @var string (options: HEB,ENG)
     */
    public $language = 'HEB';

    /*----------------------------------------------------------------------------------*/

    function __construct()
    {
        parent::__construct();

        $this->dependence_check(); // if  we don't have all dependence, die.


        if ($this->dev_mode) {
            $this->cg_gateway_url = $this->dev_midArr['cg_gateway_url'];
            $this->terminal_id = $this->main_terminal = $this->dev_midArr['terminal'];
            $this->mid = $this->main_mid = $this->dev_midArr['mid'];
            $this->user = $this->dev_midArr['user'];
            $this->password = $this->dev_midArr['password'];
            $this->secured = false;
        }
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
    private function dependence_check()
    {
        if (!class_exists('xmlAssembly')) {
            die('pls include class xmlAssembly, thanks!');
        }
    }

    /*----------------------------------------------------------------------------------*/

    public function set_terminal($terminal_id = '')
    {
        $this->terminal_id = $terminal_id;
    }

    /*----------------------------------------------------------------------------------*/

    public function set_mid($mid = '')
    {
        $this->mid = $mid;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * method j4 - This method responsible to set the payment via cg,
     *                call this method only after you made j5 on the transaction sum
     *                dont forget to save the j4_transaction num
     * @param (int) $order_id - link to id in tb_orders
     * @param (int) $terminal_id - branch terminal id
     * @param (string) $token card token
     * @param (string) $customer_x_field for customer billing information (Not required)
     * @param (int) $auth_number user the auth_number that you get in the j5 method
     * @param (string ) $sum (example : 65.50 , 65.00)
     *
     * @param $sum
     *
     *
     * @return (example 1: success)
     *
     * (
     * [response] => Array
     * (
     * [command] => doDeal
     * [dateTime] => 2014-06-08 12:42
     * [requestId] => Array
     * (
     * )
     *
     * [tranId] => 4142994
     * [result] => 000
     * [message] => עסקה תקינה
     * [userMessage] => עסקה תקינה
     * [additionalInfo] => Array
     * (
     * )
     *
     * [version] => 1000
     * [language] => Heb
     * [doDeal] => Array
     * (
     * [status] => 000
     * [statusText] => עסקה תקינה
     * [terminalNumber] => 0962835
     * [cardId] => 1073413892161121
     * [cardBin] => 458011
     * [cardMask] => 458011******1121
     * [cardLength] => 16
     * [cardNo] => xxxxxxxxxxxx1121
     * [cardName] => ASIV IMUEL
     * [cardExpiration] => 0115
     * [cardType] => Local
     * [extendedCardType] => Array
     * (
     * [@attributes] => Array
     * (
     * [code] =>
     * )
     *
     * )
     *
     * [creditCompany] => Alphacard
     * [cardBrand] => Visa
     * [cardAcquirer] => Visa
     * [serviceCode] => 000
     * [transactionType] => AuthDebit
     * [creditType] => RegularCredit
     * [currency] => ILS
     * [transactionCode] => Phone
     * [total] => 1290
     * [balance] => Array
     * (
     * )
     *
     * [starTotal] => 0
     * [firstPayment] => Array
     * (
     * )
     *
     * [periodicalPayment] => Array
     * (
     * )
     *
     * [numberOfPayments] => Array
     * (
     * )
     *
     * [clubId] => Array
     * (
     * )
     *
     * [clubCode] => 0
     * [validation] => AutoComm
     * [commReason] => Array
     * (
     * [@attributes] => Array
     * (
     * [code] =>
     * )
     *
     * )
     *
     * [idStatus] => NotValidated
     * [cvvStatus] => Absent
     * [authSource] => VoiceMail
     * [authNumber] => 0017359
     * [fileNumber] => 81
     * [slaveTerminalNumber] => 002
     * [slaveTerminalSequence] => 644
     * [creditGroup] => Array
     * (
     * )
     *
     * [pinKeyIn] => 0
     * [pfsc] => 0
     * [eci] => 0
     * [cavv] => Array
     * (
     * [@attributes] => Array
     * (
     * [code] =>
     * )
     *
     * )
     *
     * [user] => 8
     * [addonData] => Array
     * (
     * )
     *
     * [supplierNumber] => 0356896
     * [intIn] => Bxxxxxxx1121C1290D011150E0017359F0G0H0J4TxxxxY300018983X8
     * [intOt] => 0000xxxxxxxxxxxxxxx1121220004xxxx3000001290        000000006021 150  3001735900000000000000000081001001LEUMI VISA     0                  8
     * )
     *
     * )
     *
     * )
     */
    public function j4($order_id, $terminal_id, $token, $auth_number, $card_expiration, $customer_x_field, $sum)
    {
        $sum *= 100;
        if (in_array($_SERVER['REMOTE_ADDR'], array('62.219.212.139', '81.218.173.175'))) {
            mail('gal@inmanage.co.il', 'mail - ' . __FILE__, print_r(array($order_id, $terminal_id, $token, $auth_number, $card_expiration, $customer_x_field, $sum), true), 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
        }
        $xmlArr = array(
            'tag' => 'ashrait',
            'params' => array(),
            'items' => array(
                array('tag' => 'request',
                    'params' => array(),
                    'items' => array(
                        array('tag' => 'command',
                            'value' => 'doDeal',
                            'params' => array()
                        ),
                        array('tag' => 'dateTime',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'requestId',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'tranId',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'result',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'message',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'userMessage',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'additional',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'Info',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'version',
                            'value' => $this->version,
                            'params' => array()

                        ),
                        array('tag' => 'language',
                            'value' => $this->language,
                            'params' => array()

                        ),
                        array('tag' => 'mayBeDuplicate',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'doDeal',
                            'params' => array(),
                            'items' => array(
                                array('tag' => 'terminalNumber',
                                    'value' => $terminal_id,
                                    'params' => array()
                                ),
                                array('tag' => 'cardId',
                                    'value' => $token,
                                    'params' => array()
                                ),
                                array('tag' => 'transactionType',
                                    'value' => 'Debit',  //
                                    'params' => array()
                                ),
                                array('tag' => 'creditType',
                                    'value' => 'RegularCredit',
                                    'params' => array()
                                ),
                                array('tag' => 'currency',
                                    'value' => 'ILS',
                                    'params' => array()
                                ),
                                array('tag' => 'transactionCode',
                                    'value' => 'Phone',
                                    'params' => array()
                                ),
                                array('tag' => 'total',
                                    'value' => $sum,
                                    'params' => array()
                                ),

                                array('tag' => 'authNumber',
                                    'value' => $auth_number,
                                    'params' => array()
                                ),


                                array('tag' => 'validation',
                                    'value' => 'AutoComm', //AutoComm
                                    'params' => array()

                                ),

                                array('tag' => 'mainTerminalNumber',
                                    'value' => '9498000',
                                    'params' => array()

                                ),


                                array('tag' => 'user', // for customer billing information
                                    'value' => $customer_x_field,
                                    'params' => array()

                                ),


                            ),
                        ),


                    ),
                ),

            )
        );
        if (in_array($_SERVER['REMOTE_ADDR'], array('62.219.212.139', '81.218.173.175'))) {
            mail('gal@inmanage.co.il', 'j4 - ' . __FILE__, print_r(array($xmlArr, __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__), true), 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
        }
        $answerArr = $this->do_request($xmlArr, __METHOD__, array(
            "file" => __FILE__,
            "LINE" => __LINE__,
            "METHOD" => __METHOD__,
            "order_id" => $order_id,
        ));


        $this->write_j4_answer($order_id, $answerArr);

        return $answerArr;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * method write_j4_answer -
     *    write j4 answer into $this->tb_orders table
     *
     */

    public function write_j4_answer($order_id, $answerArr)
    {
        $status_id = (isset($answerArr['response']['result']) && intval($answerArr['response']['result']) == 0) ? 1 : 0;
        $db_fields = array(
            "order_id" => $order_id,
            "j4_transaction_id" => $answerArr['response']['tranId'],
            "token" => $answerArr['response']['doDeal']['cardId'],
            "cardExpiration" => $answerArr['response']['doDeal']['cardExpiration'],
            "terminal" => $answerArr['response']['doDeal']['terminalNumber'],
            "status_id" => $status_id,
            "sum" => $answerArr['response']['doDeal']['total'],
            "last_update" => $this->ts,

        );
        foreach ($db_fields AS $key => $value) {
            $db_fields[$key] = $this->db->make_escape($value);
        }
        mail('gal@inmanage.co.il', '_j4_answer - ' . __FILE__, print_r(array($db_fields, $answerArr, __FILE__, __LINE__, __CLASS__, __METHOD__, __FUNCTION__), true), 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
        $res = $this->db->insert($this->tb_orders, $db_fields);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * check if the customer have the money to this deal
     * @param $sum
     *
     */
    public function j5($token = '', $card_expiration, $terminal_id = '', $sum = '', $user_pid = '', $cvv = '')
    {
        $sum *= 100;
        $terminal_id = ($terminal_id) ? $terminal_id : $this->terminal;
        $xmlArr = array(
            'tag' => 'ashrait',
            'params' => array(),
            'items' => array(
                array('tag' => 'request',
                    'params' => array(),
                    'items' => array(
                        array('tag' => 'command',
                            'value' => 'doDeal',
                            'params' => array()
                        ),
                        /*	array('tag'=>'dateTime',
							'value'=>'',
							'params'=>array()
						),
						array('tag'=>'requestId',
							'value'=>'',
							'params'=>array()
						),
						array('tag'=>'tranId',
							'value'=>'',
							'params'=>array()
						),*/
                        array('tag' => 'result',
                            'value' => '',
                            'params' => array()
                        ),
                        /*	array('tag'=>'message',
							'value'=>'',
							'params'=>array()
						),
						array('tag'=>'userMessage',
							'value'=>'',
							'params'=>array()

						),*/
                        array('tag' => 'additional',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'Info',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'version',
                            'value' => $this->version,
                            'params' => array()

                        ),
                        array('tag' => 'language',
                            'value' => $this->language,
                            'params' => array()

                        ),
                        array('tag' => 'mayBeDuplicate',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'doDeal',
                            'params' => array(),
                            'items' => array(
                                array('tag' => 'terminalNumber',
                                    'value' => $terminal_id, //$terminal_id 0962832  \ 9498001
                                    'params' => array()
                                ),
                                array('tag' => 'cardId',
                                    'value' => $token,
                                    'params' => array()
                                ),
                                /*array('tag'=>'track2',
									'value'=>'',
									'params'=>array()
								),
								array('tag'=>'cardNo',
									'value'=>'CGMPI',
									'params'=>array()
								),*/
                                array('tag' => 'total',
                                    'value' => $sum,//$sum,
                                    'params' => array()
                                ),
                                /*array('tag'=>'starTotal',
									'value'=>'',
									'params'=>array()
								),*/
                                array('tag' => 'transactionType',
                                    'value' => 'Debit',
                                    'params' => array()
                                ),
                                array('tag' => 'creditType',
                                    'value' => 'RegularCredit',
                                    'params' => array()
                                ),
                                array('tag' => 'currency',
                                    'value' => 'ILS',
                                    'params' => array()
                                ),
                                array('tag' => 'transactionCode',
                                    'value' => 'Phone',
                                    'params' => array()
                                ),/*
								array('tag'=>'authNumber',
									'value'=>'',
									'params'=>array()
								),

								array('tag'=>'firstPayment',
									'value'=>'0',
									'params'=>array()
								),

								array('tag'=>'periodicalPayment',
									'value'=>'0',
									'params'=>array()
								),

								array('tag'=>'numberOfPayments',
									'value'=>'0',
									'params'=>array()
								),

								array('tag'=>'slaveTerminalNumber',
									'value'=>'',
									'params'=>array()
								),*/
                                array('tag' => 'validation',
                                    'value' => 'Verify',
                                    'params' => array()

                                ),

                                /*	array('tag'=>'delekCode',
									'value'=>'',
									'params'=>array()

								),

								array('tag'=>'delekQuantity',
									'value'=>'',
									'params'=>array()

								),

								array('tag'=>'oilQuantity',
									'value'=>'',
									'params'=>array()

								),

								array('tag'=>'oilSum',
									'value'=>'',
									'params'=>array()

								),

								array('tag'=>'odometer',
									'value'=>'',
									'params'=>array()

								),
								array('tag'=>'carNum',
									'value'=>'',
									'params'=>array()

								),
								array('tag'=>'clubCode',
									'value'=>'',
									'params'=>array()

								),
								array('tag'=>'clubId',
									'value'=>'',
									'params'=>array()

								),*/
                                array('tag' => 'mainTerminalNumber',
                                    'value' => '9498000',//9498501
                                    'params' => array()

                                ),

                                array('tag' => 'cardExpiration',
                                    'value' => $card_expiration,
                                    'params' => array()

                                ),

                                /*array('tag'=>'cvv',
									'value'=>$cvv,
									'params'=>array()

								),*/
                                /*
								array('tag'=>'dealerNumber',
									'value'=>'',
									'params'=>array()

								),

								array('tag'=>'last4D',
									'value'=>'',
									'params'=>array()

								),*/

                                array('tag' => 'user',
                                    'value' => '',
                                    'params' => array()

                                ),

                                /*	array('tag'=>'id',
																	'value'=>$user_pid,
																	'params'=>array()

																),*/
                                /*
																array('tag'=>'addonData',
																	'value'=>'',
																	'params'=>array()

																),

																array('tag'=>'cavv',
																	'value'=>'',
																	'params'=>array()

																),

																array('tag'=>'eci',
																	'value'=>'',
																	'params'=>array()

																),
																array('tag'=>'delek',
																	'value'=>'',
																	'params'=>array()

																),
																array('tag'=>'ticketNumber',
																	'value'=>'',
																	'params'=>array()

																),
																array('tag'=>'customerData',
																	'value'=>'',
																	'params'=>array()

																),
																	array('tag'=>'subCustomerData',
																		'value'=>'',
																		'params'=>array()

																	),
																	array('tag'=>'sectorData',
																		'value'=>'',
																		'params'=>array()

																	),*/
                                /*array('tag'=>'mid',
																		'value'=>$this->mid,
																		'params'=>array()

																	),
																	array('tag'=>'mpiValidation',
																		'value'=>'Verify',
																		'params'=>array()

																	),
																	array('tag'=>'uniqueid',
																		'value'=>time().'.'.microtime(),
																		'params'=>array()

																	),
																	array('tag'=>'successUrl',
																		'value'=>'http://tran.mcdonalds.co.il/resource/credit-guard/return.php?status=success',
																		'params'=>array()

																	),

																	array('tag'=>'failureUrl',
																		'value'=>'',
																		'params'=>array()

																	),
																	array('tag'=>'cancelUrl',
																		'value'=>'',
																		'params'=>array()

																	),*/


                            ),
                        ),


                    ),
                ),

            )
        );


        $answerArr = $this->do_request($xmlArr, __METHOD__, array(
            "file" => __FILE__,
            "LINE" => __LINE__,
            "METHOD" => __METHOD__,
        ));
        //die('<hr /><pre>' . print_r($answerArr, true) . '</pre><hr />');
        return $answerArr;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @param array $xmlArr
     * @param $sum
     *  // J102  - get card token
     *
     * @return : url
     */
    public function get_payment_form()
    {
        $User = User::getInstance();

        $serialized_session_id = 0;
        if($_SESSION['api']['platform'] == 'website' && param('SAVE_SESSION_FOR_CG')) {
            $serialized_session_id = siteFunctions::save_order_session_to_db($User->id);
        }

        $xmlArr = array(
            'tag' => 'ashrait',
            'params' => array(),
            'items' => array(
                array('tag' => 'request',
                    'params' => array(),
                    'items' => array(
                        array('tag' => 'command',
                            'value' => 'doDeal',
                            'params' => array()
                        ),
                        array('tag' => 'dateTime',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'requestId',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'tranId',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'result',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'message',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'userMessage',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'additional',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'Info',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'version',
                            'value' => $this->version,
                            'params' => array()

                        ),
                        array('tag' => 'language',
                            'value' => $this->language,
                            'params' => array()

                        ),
                        array('tag' => 'mayBeDuplicate',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'doDeal',
                            'params' => array(),
                            'items' => array(
                                array('tag' => 'terminalNumber',
                                    'value' => $this->main_terminal,
                                    'params' => array()
                                ),
                                array('tag' => 'cardId',
                                    'value' => '',
                                    'params' => array()
                                ),
                                array('tag' => 'track2',
                                    'value' => '',
                                    'params' => array()
                                ),
                                array('tag' => 'cardNo',
                                    'value' => 'CGMPI',
                                    'params' => array()
                                ),
                                array('tag' => 'total',
                                    'value' => '100',
                                    'params' => array()
                                ),
                                array('tag' => 'starTotal',
                                    'value' => '',
                                    'params' => array()
                                ),
                                array('tag' => 'transactionType',
                                    'value' => 'Debit',
                                    'params' => array()
                                ),
                                array('tag' => 'creditType',
                                    'value' => 'RegularCredit',
                                    'params' => array()
                                ),
                                array('tag' => 'currency',
                                    'value' => 'ILS',
                                    'params' => array()
                                ),
                                array('tag' => 'transactionCode',
                                    'value' => 'Phone',
                                    'params' => array()
                                ),
                                array('tag' => 'authNumber',
                                    'value' => '',
                                    'params' => array()
                                ),

                                array('tag' => 'firstPayment',
                                    'value' => '0',
                                    'params' => array()
                                ),

                                array('tag' => 'periodicalPayment',
                                    'value' => '0',
                                    'params' => array()
                                ),

                                array('tag' => 'numberOfPayments',
                                    'value' => '0',
                                    'params' => array()
                                ),

                                array('tag' => 'slaveTerminalNumber',
                                    'value' => '',
                                    'params' => array()
                                ),
                                array('tag' => 'validation',
                                    'value' => 'TxnSetup',
                                    'params' => array()

                                ),

                                array('tag' => 'delekCode',
                                    'value' => '',
                                    'params' => array()

                                ),

                                array('tag' => 'delekQuantity',
                                    'value' => '',
                                    'params' => array()

                                ),

                                array('tag' => 'oilQuantity',
                                    'value' => '',
                                    'params' => array()

                                ),

                                array('tag' => 'oilSum',
                                    'value' => '',
                                    'params' => array()

                                ),

                                array('tag' => 'odometer',
                                    'value' => '',
                                    'params' => array()

                                ),
                                array('tag' => 'carNum',
                                    'value' => '',
                                    'params' => array()

                                ),
                                array('tag' => 'clubCode',
                                    'value' => '',
                                    'params' => array()

                                ),
                                array('tag' => 'clubId',
                                    'value' => '',
                                    'params' => array()

                                ),
                                array('tag' => 'mainTerminalNumber',
                                    'value' => '9498000',
                                    'params' => array()

                                ),
                                array('tag' => 'cardExpiration',
                                    'value' => '',
                                    'params' => array()

                                ),

                                array('tag' => 'cvv',
                                    'value' => '',
                                    'params' => array()

                                ),

                                array('tag' => 'dealerNumber',
                                    'value' => '',
                                    'params' => array()

                                ),

                                array('tag' => 'last4D',
                                    'value' => '',
                                    'params' => array()

                                ),

                                array('tag' => 'user',
                                    'value' => '',
                                    'params' => array()

                                ),

                                array('tag' => 'id',
                                    'value' => '',
                                    'params' => array()

                                ),

                                array('tag' => 'addonData',
                                    'value' => '',
                                    'params' => array()

                                ),

                                array('tag' => 'cavv',
                                    'value' => '',
                                    'params' => array()

                                ),

                                array('tag' => 'eci',
                                    'value' => '',
                                    'params' => array()

                                ),
                                array('tag' => 'delek',
                                    'value' => '',
                                    'params' => array()

                                ),
                                array('tag' => 'ticketNumber',
                                    'value' => '',
                                    'params' => array()

                                ),
                                array('tag'=>'customerData',
                                    'params'=>array(),
                                    'items'=>array(
                                        array('tag'=>'userData7', //send session
                                            'value'=>md5($serialized_session_id), // session
                                            'params'=>array()
                                        ),
                                    ),
                                ),
                                array('tag' => 'subCustomerData',
                                    'value' => '',
                                    'params' => array()

                                ),
                                array('tag' => 'sectorData',
                                    'value' => '',
                                    'params' => array()

                                ),
                                array('tag' => 'mid',
                                    'value' => $this->main_mid,
                                    'params' => array()

                                ),
                                array('tag' => 'mpiValidation',
                                    'value' => 'Verify',
                                    'params' => array()

                                ),
                                array('tag' => 'uniqueid',
                                    'value' => time() . '.' . microtime(),
                                    'params' => array()

                                ),
                                array('tag' => 'successUrl',
                                    'value' => 'https://' . $_SERVER['HTTP_HOST'] . '/resource/credit-guard/return.php?status=success',
                                    'params' => array()

                                ),

                                array('tag' => 'errorUrl',
                                    'value' => 'https://' . $_SERVER['HTTP_HOST'] . '/resource/credit-guard/return.php?status=error'.urlencode('&hash='.md5($serialized_session_id)),
                                    'params' => array()

                                ),
                                array('tag' => 'cancelUrl',
                                    'value' => 'https://' . $_SERVER['HTTP_HOST'] . '/resource/credit-guard/return.php?status=error'.urlencode('&hash='.md5($serialized_session_id)),
                                    'params' => array()

                                ),


                            ),
                        ),


                    ),
                ),

            )
        );
        $answerArr = $this->do_request($xmlArr, __METHOD__, array(
            "file" => __FILE__,
            "LINE" => __LINE__,
            "METHOD" => __METHOD__,
        ));

        if (intval($answerArr['response']['doDeal']['status']) == 0) {
            return $answerArr['response']['doDeal']['mpiHostedPageUrl'];
        }

    }

    /*----------------------------------------------------------------------------------*/
    public function broadcast_terminal($terminal = '')
    {
        /*
         *  <ashrait>
                <request>
                    <version>1000</version>
                    <language>HEB</language>
                    <dateTime>2011-08-01 14:18:26</dateTime>
                    <command>transmitTerminal</command>
                    <requestId>1312197506-23756</requestId>
                    <transmitTerminal>
                        <terminalNumber>096XXXX</terminalNumber>
                        <transmitType>settlements</transmitType>
                        <responseType>wait/post</responseType>
                    </transmitTerminal>
                </request>
            </ashrait>

         */
        // todo replace the terminal id

        $xmlArr = array(
            'tag' => 'ashrait',
            'params' => array(),
            'items' => array(
                array('tag' => 'request',
                    'params' => array(),
                    'items' => array(
                        array('tag' => 'version',
                            'value' => '1000',
                            'params' => array()
                        ),
                        array('tag' => 'language',
                            'value' => 'HEB',
                            'params' => array()
                        ),
                        array('tag' => 'dateTime',
                            'value' => date('Y-m-d H:i:s'),
                            'params' => array()
                        ),
                        array('tag' => 'command',
                            'value' => 'transmitTerminal',
                            'params' => array()
                        ),
                        array('tag' => 'requestId',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'transmitTerminal',
                            'params' => array(),
                            'items' => array(
                                array('tag' => 'terminalNumber',
                                    'value' => $terminal,
                                    'params' => array()
                                ),
                                array('tag' => 'transmitType',
                                    'value' => 'settlements',
                                    'params' => array()
                                ),
                                array('tag' => 'responseType',
                                    'value' => 'wait/post',
                                    'params' => array()
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        $answerArr = $this->do_request($xmlArr, __METHOD__, array(
            "file" => __FILE__,
            "LINE" => __LINE__,
            "METHOD" => __METHOD__,
        ));
        $this->write_broadcast_terminal_log($answerArr['response']['transmitTerminal']['terminalNumber'], $answerArr['response']['transmitTerminal']['transmitId'], $answerArr['response']['message'], $answerArr['response']['result']);

        if (intval($answerArr['response']['result']) == 1000) {
            return $answerArr['response']['message'];
        }
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @method name - refund_order
     *            make semi refund on the order, before calling this method you need to check that the sum is equal or less from the order sum ,
     *            and also that has been a business day before the refund.
     *
     *            you can call this method only after the transaction has been launched
     * @param $order_id `id` fields in
     * @param $sum
     * @return (array)
     * example:
     *
     * Array
     * (
     * [response] => Array
     * (
     * [command] => doDeal
     * [dateTime] => 2014-06-08 18:06
     * [requestId] => Array
     * (
     * )
     *
     * [tranId] => 4143057
     * [result] => 000
     * [message] => עסקה תקינה
     * [userMessage] => עסקה תקינה
     * [additionalInfo] => Array
     * (
     * )
     *
     * [version] => 1000
     * [language] => Heb
     * [doDeal] => Array
     * (
     * [status] => 000
     * [statusText] => עסקה תקינה
     * [terminalNumber] => 0962835
     * [cardId] => 1073413892161121
     * [cardBin] => 458011
     * [cardMask] => 458011******1121
     * [cardLength] => 16
     * [cardNo] => xxxxxxxxxxxx1121
     * [cardName] => ASIV IMUEL
     * [cardExpiration] => 0115
     * [cardType] => Local
     * [extendedCardType] => Array
     * (
     * [@attributes] => Array
     * (
     * [code] =>
     * )
     *
     * )
     *
     * [creditCompany] => Alphacard
     * [cardBrand] => Visa
     * [cardAcquirer] => Visa
     * [serviceCode] => 000
     * [transactionType] => AuthCredit
     * [creditType] => RegularCredit
     * [currency] => ILS
     * [transactionCode] => Phone
     * [total] => 1000
     * [balance] => Array
     * (
     * )
     *
     * [starTotal] => 0
     * [firstPayment] => Array
     * (
     * )
     *
     * [periodicalPayment] => Array
     * (
     * )
     *
     * [numberOfPayments] => Array
     * (
     * )
     *
     * [clubId] => Array
     * (
     * )
     *
     * [clubCode] => 0
     * [validation] => AutoComm
     * [commReason] => Array
     * (
     * [@attributes] => Array
     * (
     * [code] =>
     * )
     *
     * )
     *
     * [idStatus] => NotValidated
     * [cvvStatus] => Absent
     * [authSource] => VoiceMail
     * [authNumber] => 6621336
     * [fileNumber] => 81
     * [slaveTerminalNumber] => 002
     * [slaveTerminalSequence] => 650
     * [creditGroup] => Array
     * (
     * )
     *
     * [pinKeyIn] => 0
     * [pfsc] => 0
     * [eci] => 0
     * [cavv] => Array
     * (
     * [@attributes] => Array
     * (
     * [code] =>
     * )
     *
     * )
     *
     * [user] => Array
     * (
     * )
     *
     * [addonData] => Array
     * (
     * )
     *
     * [supplierNumber] => 0356896
     * [intIn] => Bxxxxxxx1121C1000D511150E6621336F0G0H0J4TxxxxY300018983
     * [intOt] => 0000xxxxxxxxxxxxxxx1121220004xxxx3000001000        000000006531 150  3662133600000000000000000081001001LEUMI VISA     0
     * )
     *
     * )
     *
     * )
     */
    public function refund_order($order_id, $sum)
    {

        $orderArr = $this->get_order_information($order_id);
        if ($orderArr['status_id'] == 2) { // payment fail
            return array("err" => 'Payment not received');
        }
        $sum *= 100;
        $xmlArr = array(
            'tag' => 'ashrait',
            'params' => array(),
            'items' => array(
                array('tag' => 'request',
                    'params' => array(),
                    'items' => array(
                        array('tag' => 'command',
                            'value' => 'refundDeal',
                            'params' => array()
                        ),
                        array('tag' => 'dateTime',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'requestId',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'tranId',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'result',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'message',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'userMessage',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'additional',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'Info',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'version',
                            'value' => $this->version,
                            'params' => array()

                        ),
                        array('tag' => 'language',
                            'value' => $this->language,
                            'params' => array()

                        ),
                        array('tag' => 'mayBeDuplicate',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'refundDeal',
                            'params' => array(),
                            'items' => array(
                                array('tag' => 'terminalNumber',
                                    'value' => $orderArr['terminal'],
                                    'params' => array()
                                ),
                                array('tag' => 'tranId',
                                    'value' => $orderArr['j4_transaction_id'],
                                    'params' => array()
                                ),
                                array('tag' => 'cardNo',
                                    'value' => '',
                                    'params' => array()
                                ),
                                /*array('tag'=>'cardNo',
									'value'=>'CGMPI',
									'params'=>array()
								),*/
                                array('tag' => 'total',
                                    'value' => $sum,
                                    'params' => array()
                                ),
                                array('tag' => 'user',
                                    'value' => '',
                                    'params' => array()
                                ),

                            ),
                        ),
                    ),
                ),
            )
        );
        $answerArr = $this->do_request($xmlArr, __METHOD__, array(
            "file" => __FILE__,
            "LINE" => __LINE__,
            "METHOD" => __METHOD__,
        ));


        return $answerArr;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * method - cancel_order
     *        cancel order, check if payment receive
     *
     *
     * @param (int) $order_id id in tb_orders
     * @return array|mixed
     */
    public function cancel_order($order_id)
    {
        $orderArr = $this->get_order_information($order_id);
        if ($orderArr['status_id'] == 2) { // payment fail
            return array("err" => 'Payment not received');
        }
        $sum = $orderArr['sum']; // allready with X*100
        $xmlArr = array(
            'tag' => 'ashrait',
            'params' => array(),
            'items' => array(
                array('tag' => 'request',
                    'params' => array(),
                    'items' => array(
                        array('tag' => 'command',
                            'value' => 'refundDeal',
                            'params' => array()
                        ),
                        array('tag' => 'dateTime',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'requestId',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'tranId',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'result',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'message',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'userMessage',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'additional',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'Info',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'version',
                            'value' => $this->version,
                            'params' => array()

                        ),
                        array('tag' => 'language',
                            'value' => $this->language,
                            'params' => array()

                        ),
                        array('tag' => 'mayBeDuplicate',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'refundDeal',
                            'params' => array(),
                            'items' => array(
                                array('tag' => 'terminalNumber',
                                    'value' => $orderArr['terminal'],
                                    'params' => array()
                                ),
                                array('tag' => 'tranId',
                                    'value' => $orderArr['j4_transaction_id'],
                                    'params' => array()
                                ),
                                array('tag' => 'cardNo',
                                    'value' => '',
                                    'params' => array()
                                ),
                                /*array('tag'=>'cardNo',
									'value'=>'CGMPI',
									'params'=>array()
								),*/
                                array('tag' => 'total',
                                    'value' => $sum,
                                    'params' => array()
                                ),
                                array('tag' => 'user',
                                    'value' => '',
                                    'params' => array()
                                ),

                            ),
                        ),
                    ),
                ),
            )
        );
        $answerArr = $this->do_request($xmlArr, __METHOD__, array(
            "file" => __FILE__,
            "LINE" => __LINE__,
            "METHOD" => __METHOD__,
        ));
        $status = intval($answerArr['response']['refundDeal']['status']);
        $this->write_cancel_log($orderArr, $answerArr);
        if (!$status) {
            // send error mail

        }

        return $answerArr;
    }

    /*----------------------------------------------------------------------------------*/

    public function write_cancel_log($orderArr, $answerArr)
    {
        $cg_status = intval($answerArr['response']['refundDeal']['status']);
        $status = ($cg_status) ? 0 : 1;
        $db_fields = array(
            "order_id" => $orderArr['order_id'],
            "cg_status" => $cg_status,
            "cg_status_text" => $answerArr['response']['refundDeal']['statusText'],
            "tranId" => $answerArr['response']['tranId'],
            "status" => $status, // 0.fail 1. success
            "last_update" => time(),

        );
        foreach ($db_fields AS $key => $value) {
            $db_fields[$key] = $this->db->make_escape($value);
        }
        $res = $this->db->insert($this->tb_cancel, $db_fields);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     *
     *    get&save token to user
     *
     *    call this method on every return page on mode success
     *  this method call only to the token terminal
     *
     * the method save every card that return but when pulling the data dont take card without save card options on (return from $_GET in userData1 param)
     *
     *
     * @param $mpi_transaction_id
     * @param string $mid
     * @param string $terminal
     * @return (int)$Cg->tb_token id
     */
    public function get_token($mpi_transaction_id, $mid = '', $terminal = '')
    {

        $mid = ($mid) ? $mid : $this->main_mid;
        $terminal = ($terminal) ? $terminal : $this->main_terminal;
        $answerArr = $this->inquire_transactions($mpi_transaction_id, $this->main_mid, $this->main_terminal);
        $cardMask = $answerArr['response']['inquireTransactions']['row']['cgGatewayResponseXML']['ashrait']['response']['doDeal']['cardMask'];
        $creditCompany = $answerArr['response']['inquireTransactions']['row']['cgGatewayResponseXML']['ashrait']['response']['doDeal']['creditCompany'];
        $cardExpiration = $answerArr['response']['inquireTransactions']['row']['cgGatewayResponseXML']['ashrait']['response']['doDeal']['cardExpiration'];
        $cardId = $answerArr['response']['inquireTransactions']['row']['cardId'];
        $personalId = $answerArr['response']['inquireTransactions']['row']['personalId'];
        $save_card = (isset($_REQUEST['userData1']) && $_REQUEST['userData1']) ? 1 : 0;
        $cvv = '';
        return $this->save_token_to_db($_SESSION['user_id'], $mid, $cardMask, $creditCompany, $cardExpiration, $cardId, $personalId, $cvv, $save_card);

    }

    /*----------------------------------------------------------------------------------*/

    public function save_token_to_db($user_id, $mid, $cardMask, $creditCompany, $cardExpiration, $cardId, $personalId = '', $cvv = '', $save_card)
    {

        $db_fields = array(
            "user_id" => $user_id,
            "mid" => $mid,
            "cardMask" => $cardMask,
            "creditCompany" => $creditCompany,
            "cardExpiration" => $cardExpiration,
            "token" => $cardId,
            "user_pid" => $personalId,
            "save_card" => $save_card,
            "last_update" => $this->ts,
        );

        $result = $this->db->insert($this->db_token, $db_fields);

        if (!$result) {// token exists
            $query = "SELECT * FROM `{$this->tb_token}`
					WHERE
						`user_id` = '{$db_fields['user_id']}'
						AND
						`cardMask` = '{$db_fields['cardMask']}'
						AND
						`creditCompany` = '{$db_fields['creditCompany']}'
						AND
						`cardExpiration` = '{$db_fields['cardExpiration']}'

				";
            $result = $this->db->query($query);
            $row = $this->db->get_stream($result);
            return $row['id'];
        }
        return $this->db->get_insert_id();

    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @param $token_id - id value in table tb_cg__token
     */
    public function get_user_token_information($token_id)
    {
        $query = "SELECT * FROM `{$this->tb_token}` WHERE `id`={$token_id}";
        $result = $this->db->query($query);
        $row = $this->db->get_stream($result);
        $_SESSION['card']['cardMask'] = $row['cardMask'];
        $_SESSION['card']['cardExpiration'] = $row['cardExpiration'];
        return $row;
    }    /*----------------------------------------------------------------------------------*/
    /**
     * @param $token_id - id value in table tb_cg__token
     *
     * @return (array)
     * (example)
     *
     *    array(
     *    "id"=>"XXX" ,// dont metter
     *    "order_id"=>10,
     *  "j4_transaction_id"=>'213232023',
     *  "token"=>'3213456465',
     *  "cardExpiration"=>'0816',
     *  "status_id"=>'1', // 1.success , 2.fail
     *  "last_update"=>'1',
     * );
     */
    public function get_order_information($order_id)
    {
        $query = "SELECT * FROM `{$this->tb_orders}` WHERE `order_id`={$order_id}";
        $result = $this->db->query($query);
        $row = $this->db->get_stream($result);
        return $row;
    }
    /*----------------------------------------------------------------------------------*/
    /**
     * is_user_token check if the token belong to the user
     * @param (int)$token_id - id value in table tb_cg__token
     * @param (int) $user_id
     *
     * @return (bool) true/false
     */
    public function is_user_token($token_id, $user_id)
    {
        $query = "SELECT * FROM `{$this->tb_token}` WHERE `id`={$token_id} AND `user_id`={$user_id}";
        $result = $this->db->query($query);
        if ($this->db->get_stream($result)) {
            return true;
        }
        return false;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     *  get information about transaction_id ,  we use this method for getting the real token id after call the j102 method
     * @param $mpi_transaction_id - transaction_id
     * @param $mid
     * @param $terminal
     * @return mixed
     */
    public function inquire_transactions($mpi_transaction_id, $mid, $terminal)
    {
        $xmlArr = array(
            'tag' => 'ashrait',
            'params' => array(),
            'items' => array(
                array('tag' => 'request',
                    'params' => array(),
                    'items' => array(
                        array('tag' => 'command',
                            'value' => 'inquireTransactions',
                            'params' => array()
                        ),
                        array('tag' => 'dateTime',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'requestId',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'tranId',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'result',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'message',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'userMessage',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'additional',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'Info',
                            'value' => '',
                            'params' => array()

                        ),
                        array('tag' => 'version',
                            'value' => $this->version,
                            'params' => array()

                        ),
                        array('tag' => 'language',
                            'value' => $this->language,
                            'params' => array()

                        ),
                        array('tag' => 'mayBeDuplicate',
                            'value' => '',
                            'params' => array()
                        ),
                        array('tag' => 'inquireTransactions',
                            'params' => array(),
                            'items' => array(
                                array('tag' => 'terminalNumber',
                                    'value' => $terminal,
                                    'params' => array()
                                ),
                                array('tag' => 'transmitId',
                                    'value' => '',
                                    'params' => array()
                                ),
                                array('tag' => 'mid',
                                    'value' => $mid,
                                    'params' => array()
                                ),
                                array('tag' => 'queryName',
                                    'value' => 'mpiTransaction',
                                    'params' => array()
                                ),
                                array('tag' => 'mpiTransactionId',
                                    'value' => $mpi_transaction_id,
                                    'params' => array()
                                ),
                                array('tag' => 'userData1',
                                    'value' => '',
                                    'params' => array()
                                ),
                                array('tag' => 'userData2',
                                    'value' => 'Debit',
                                    'params' => array()
                                ),
                                array('tag' => 'userData3',
                                    'value' => '',
                                    'params' => array()
                                ),
                                array('tag' => 'userData4',
                                    'value' => '',
                                    'params' => array()
                                ),
                                array('tag' => 'userData5',
                                    'value' => '',
                                    'params' => array()
                                ),
                            ),
                        ),
                    ),
                ),
            )
        );

        return $answerArr = $this->do_request($xmlArr, __METHOD__, array(
            "file" => __FILE__,
            "LINE" => __LINE__,
            "METHOD" => __METHOD__,
        ));
    }


    /*----------------------------------------------------------------------------------*/
    /**
     * @param string $request
     * @param string $response
     * @param string $method_name
     * @param string $status 9999 - only request
     * @param array $extra_fields
     */
    private function log($request = '', $response = '', $method_name = '', $status = '9999', $extra_fields = array(), $update_id = 0)
    {
        $db_fields = array(
            "user_id" => $_SESSION['user_id'],
            "request" => $request,
            "response" => $response,
            "session_id" => session_id(),
            "method_name" => $method_name,
            "status" => $status,
            "extra_fields" => base64_encode(serialize($extra_fields)),
            "order_id" => (isset($extra_fields['order_id']) ? $extra_fields['order_id'] : 0),
            "last_update" => $this->ts,

        );
        // update fields
        if ($update_id) {
            $db_fields['id'] = $update_id;
        }
        $this->db->replace($this->tb_log, $db_fields);
        return $this->db->get_insert_id();

    }

    /*----------------------------------------------------------------------------------*/

    public function do_request($xmlArr, $method_name, $detailsArr = array(), $try = 1)
    {
        // write to log
        //die('<hr /><pre>' . print_r($xmlArr, true) . '</pre><hr />');
        $xml = $this->build_xml($xmlArr);
        /*$post_string = 'user='.$cgConf['user'];
		$post_string .= '&password='.$cgConf['password'];

		/*Build Ashrait XML to post*/
        //	$uniuque_id=time().rand(100,1000);

        $paramsArr = array(
            "user" => $this->user,
            "password" => $this->password,
            "int_in" => $xml
        );
        if ($detailsArr['METHOD'] == 'credit_guardManager::j5') {
            //die('<hr /><pre>' . print_r($paramsArr, true) . '</pre><hr />');
        }
        //die('<hr /><pre>' . print_r($detailsArr, true) . '</pre><hr />');
        $post_string = http_build_query($paramsArr);
        if ($this->write_to_log) {
            $log_id = $this->log($post_string, '', $method_name, '0', $detailsArr); // request
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->cg_gateway_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 45); // Cg ask to define 45 sec timeout for each call...
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        if ($this->secured) {
            curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_PORT, $this->secured_port);
        }
        $result = curl_exec($ch);
        $error = curl_error($ch);
        // if curl didn't success retry
        if ($error && in_array($method_name, $this->retry_methodArr)) {
            if ($try < $this->retry_methodArr[$method_name]) {
                sleep($this->retry_delay);
                $this->do_request($xmlArr, $method_name, $detailsArr, ++$try);
            }
        }

        $Xml = simplexml_load_string(str_replace("ISO-8859-8", "UTF-8", $result), 'SimpleXMLElement', LIBXML_NOCDATA | LIBXML_NOBLANKS);

        if (!$Xml) {
            if ($this->dev_mode) {
                die('<hr /><pre>' . print_r(array(
                        "result" => $result,
                        "request" => $paramsArr,
                    ), true) . '</pre><hr />');
            }
        }
        $order_status = reset($Xml->response->doDeal->status);
        if ($this->write_to_log) {
            $this->log($post_string, $result, $method_name, $order_status, $detailsArr, $log_id); // answer
        }

        if ($this->debug_mode) {
            if (count($this->debug_emailsArr)) {
                $subject = $_SERVER['HTTP_HOST'] . ' - Credit Guard Error !';
                $dataArr = array(
                    "data_out" => $post_string,
                    "data_in" => $Xml,
                    "error" => $result,
                    "file" => __FILE__,
                    "line" => __LINE__,
                    "class" => __CLASS__,
                    "calling_method" => $method_name,
                    "method" => __METHOD__,
                    "function" => __FUNCTION__
                );
                if (__METHOD__ == "j5") {
                    die('<hr /><pre>' . print_r($dataArr, true) . '</pre><hr />');
                    mail(implode(',', $this->debug_emailsArr), $subject, print_r($dataArr, true), 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
                }
            }
        }
        return json_decode(json_encode($Xml), true);
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @param array $xmlArr
     *
     *    $xmlArr=array(
     * 'tag'=>'mainTag',
     * 'params'=>array(),
     * 'items'=>array(
     * array('tag'=>'subTagItem1',
     * 'value'=>'1',
     * 'params'=>array()
     * ),
     * array('tag'=>'subTagItem2',
     * 'value'=>'2',
     * 'params'=>array()
     * ),
     * array('tag'=>'subTagItem3',
     * 'value'=>'3',
     * 'params'=>array()
     * ),
     * )
     * );
     *
     * @return (example)
     *
     *  <mainTag>
     *        <subTagItem1>1</subTagItem1>
     *        <subTagItem2>2</subTagItem2>
     *        <subTagItem3>3</subTagItem3>
     *  </mainTag>
     *
     */
    private function build_xml($xmlArr = array())
    {

        return xmlAssembly::assembly($xmlArr, false);
    }

}

?>