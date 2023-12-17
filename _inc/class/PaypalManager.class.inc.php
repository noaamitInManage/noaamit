<?php

class PaypalManager extends BaseManager
{


    // sandbox details:
    public $dev_config = array(
        "username" => "groupon_api1.inmanage.net",
        "password" => "JCUZ7PYHNBLFN63H",
        "signature" => "AFcWxV21C7fd0v3bYYYRCpSSRl31ANz6DIJowIHMRlP4Oj3FPe.hIYFd");
    public $dev_endPoint = 'https://api-3t.sandbox.paypal.com/nvp';
    public $dev_paypalUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_express-checkout&useraction=commit&token=';

    // production details:
    public $live_config = array(
        "username" => "***",
        "password" => "JCUZ7PYHNBLFN63H",
        "signature" => "AFcWxV21C7fd0v3bYYYRCpSSRl31ANz6DIJowIHMRlP4Oj3FPe.hIYFd");
    public $live_endPoint = 'https://api-3t.paypal.com/nvp';
    public $live_paypalUrl = 'https://www.paypal.com/webscr&cmd=_express-checkout&token=';

    public $mode = 'dev'; // options: dev , live
    public $save_card = 0; // options: dev , live

    public $subject = '';

    // only if you need to user proxy
    public $useProxy = false;
    public $proxyHost = '127.0.0.1';
    public $proxyPort = '8080';

    // return in

    // version of paypal
    public $version = '114.0';
    public $ackSuccess = 'SUCCESS';
    public $ackSuccessWithWarning = 'SUCCESSWITHWARNING';

    // the url that paypal send the user after he finish the process set it in construct
    public $returnUrl;
    public $cancelUrl;


    public $reqConfirmShipping = 0;
    public $noShipping = 1;
    public $localCode = 'he_IL'; //en_US
    public $currencyCodeType = 'ILS';

    // logo in paypal page
    public $hdrImage = 'https://www.paypal.com/de_DE/DE/i/logo/logo_150x65.gif';

    public $emailToSendDebug = 'netanel@inmanage.net';

    public $nvpHeader;
    public $token;
    public $cartAmount;

    public $paymentType = 'Authorization'; // 'Sale'

    public $billingAgreementId;
    public $userId;


    /* DB structure:
    -- --------------------------------------------------------

--
-- Table structure for table `tb_paypal__charge_queue`
--

CREATE TABLE IF NOT EXISTS `tb_paypal__charge_queue` (
  `id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `authorization_id` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `token_id` int(10) unsigned NOT NULL,
  `sum` varchar(255) NOT NULL,
  `last_update` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=229 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tb_paypal__error_log`
--

CREATE TABLE IF NOT EXISTS `tb_paypal__error_log` (
  `id` int(10) unsigned NOT NULL,
  `request` text NOT NULL,
  `response` text NOT NULL,
  `last_update` int(11) NOT NULL,
  `user_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=256 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tb_paypal__log`
--

CREATE TABLE IF NOT EXISTS `tb_paypal__log` (
  `id` int(10) unsigned NOT NULL,
  `request` text NOT NULL,
  `response` text NOT NULL,
  `last_update` int(11) NOT NULL,
  `user_id` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7046 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tb_paypal__orders`
--

CREATE TABLE IF NOT EXISTS `tb_paypal__orders` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `order_id` int(10) unsigned NOT NULL,
  `amount` float unsigned NOT NULL,
  `token` varchar(150) NOT NULL,
  `transaction_id` varchar(150) NOT NULL,
  `token_id` int(10) unsigned NOT NULL,
  `status_id` int(2) unsigned NOT NULL COMMENT '1.success , 2.fail ,3.cancel',
  `last_update` int(10) unsigned NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2467 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tb_paypal__refound`
--

CREATE TABLE IF NOT EXISTS `tb_paypal__refound` (
  `id` int(10) unsigned NOT NULL,
  `refund_transaction_id` varchar(200) NOT NULL,
  `fee_refund_amt` varchar(200) NOT NULL,
  `gross_refund_amt` varchar(200) NOT NULL,
  `net_refund_amt` varchar(200) NOT NULL,
  `total_refunded_amount` varchar(200) NOT NULL,
  `correlation_id` varchar(200) NOT NULL,
  `last_update` int(11) NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=530 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `tb_paypal__token`
--

CREATE TABLE IF NOT EXISTS `tb_paypal__token` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `title` varchar(150) NOT NULL,
  `payer_id` varchar(100) NOT NULL,
  `cardMask` varchar(150) NOT NULL,
  `billing_agreement_id` varchar(150) NOT NULL,
  `creditCompany` varchar(100) NOT NULL DEFAULT 'paypal',
  `save_card` int(1) NOT NULL,
  `last_update` int(10) unsigned NOT NULL
) ENGINE=MyISAM AUTO_INCREMENT=85 DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_paypal__charge_queue`
--
ALTER TABLE `tb_paypal__charge_queue`
  ADD PRIMARY KEY (`id`), ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `tb_paypal__error_log`
--
ALTER TABLE `tb_paypal__error_log`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tb_paypal__log`
--
ALTER TABLE `tb_paypal__log`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tb_paypal__orders`
--
ALTER TABLE `tb_paypal__orders`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`,`user_id`), ADD KEY `last_update` (`last_update`), ADD KEY `order_id` (`order_id`), ADD KEY `status_id` (`status_id`), ADD KEY `transaction_id` (`transaction_id`);

--
-- Indexes for table `tb_paypal__refound`
--
ALTER TABLE `tb_paypal__refound`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`id`), ADD KEY `refund_transaction_id` (`refund_transaction_id`,`last_update`);

--
-- Indexes for table `tb_paypal__token`
--
ALTER TABLE `tb_paypal__token`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `id` (`user_id`,`payer_id`), ADD KEY `last_update` (`last_update`), ADD KEY `paypal_email` (`payer_id`), ADD KEY `payerId` (`payer_id`), ADD KEY `save_card` (`save_card`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_paypal__charge_queue`
--
ALTER TABLE `tb_paypal__charge_queue`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=229;
--
-- AUTO_INCREMENT for table `tb_paypal__error_log`
--
ALTER TABLE `tb_paypal__error_log`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=256;
--
-- AUTO_INCREMENT for table `tb_paypal__log`
--
ALTER TABLE `tb_paypal__log`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7046;
--
-- AUTO_INCREMENT for table `tb_paypal__orders`
--
ALTER TABLE `tb_paypal__orders`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2467;
--
-- AUTO_INCREMENT for table `tb_paypal__refound`
--
ALTER TABLE `tb_paypal__refound`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=530;
--
-- AUTO_INCREMENT for table `tb_paypal__token`
--
ALTER TABLE `tb_paypal__token`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=85;

     */

    public function __construct($mode = '')
    {
        parent::__construct();

        if ($mode) {
            $this->mode = $mode;
        }

        // set the userId from session.
        $this->set_user();

        // set the username,password and signature from config
        switch ($this->mode) {
            case 'live':
                $this->config = $this->live_config;
                $this->endPoint = $this->live_endPoint;
                $this->paypalUrl = $this->live_paypalUrl;
                break;
            case 'dev':
                // sandbox details:
                $this->config = $this->dev_config;
                $this->endPoint = $this->dev_endPoint;
                $this->paypalUrl = $this->dev_paypalUrl;
                break;
        }
        $this->nvpHeader = "&PWD=" . urlencode($this->config['password']) . "&USER=" . urlencode($this->config['username']) . "&SIGNATURE=" . urlencode($this->config['signature']);

        // set token if exists
        if (isset($_SESSION['paypal']['token']) && $_SESSION['paypal']['token']) {
            $this->set_token($_SESSION['paypal']['token']);
        }


        $this->returnUrl = 'http://' . $_SERVER['HTTP_HOST'] . "/resource/paypal/return.php?status=success";
        $this->cancelUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/resource/paypal/return.php?status=error';

    }

    public function set_token($token)
    {
        $this->token = $token;
        $_SESSION['paypal']['token'] = $token;
    }

    public function replace_order_to_paypal_format($orderItemsArr)
    {
        $itemsArr = array();
        $cartAmount = 0;
        $i = 0;
        foreach ($orderItemsArr as $itemArr) {
            $itemsArr['L_PAYMENTREQUEST_0_NAME' . $i] = $itemArr['name'];
            $itemsArr['L_PAYMENTREQUEST_0_DESC' . $i] = $itemArr['desc'];
            $itemsArr['L_PAYMENTREQUEST_0_AMT' . $i] = $itemArr['total_price'];
            $itemsArr['L_PAYMENTREQUEST_0_QTY' . $i] = 1;
            $cartAmount += floatval($itemArr['total_price']);
            $i++;
        }
        return array('cartAmount' => $cartAmount, 'items' => $itemsArr);
    }

    public function set_user()
    {
        $User = User::getInstance();
        $this->userId = $User->id;
    }

    /*----------------------------------------------------------------------------------*/
    public function get_charge_billing_fields($order_id)
    {
        $tb_name = 'tb_paypal__charge_queue';
        $query = "SELECT * FROM `{$tb_name}` WHERE `order_id` = {$order_id}";
        $result = $this->db->query($query);

        if ($result->num_rows) {
            $row = $this->db->get_stream($result);
            return $row;
        }
        return false; // else case
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * set_express_checkout: call to paypal and make the token for the order. if success - send the user to paypal page with the tokan.
     *                       this is the first method to paypal
     * expect_values:
     *
     *      type    Param name    Required    example
     *        (Array)  $orderItemsArr    Yes    array(array('price' => '9.00','name' => 'בקבוק קולה','desc' => 'בקבוק 1.5 ליטר'),array('price' => '7.00','name' => 'פחית קולה','desc' => ' פחית קוקה קולה'));
     *
     * @return :    true / false
     *
     */
    public function set_express_checkout($orderItemsArr, $lang = 1)
    {
        $User = User::getInstance();

        $serialized_session_id = 0;
        if($_SESSION['api']['platform'] == 'website' && param('SAVE_SESSION_FOR_PAYPAL')) {
            $serialized_session_id = siteFunctions::save_order_session_to_db($User->id);
        }

        $this->localCode = (isset($lang) && $lang == 1) ? 'he_IL' : 'en_US'; // 1 = hebrew, 2 = eng
        // Make array with all items details in paypal format
        $itemsArr = $this->replace_order_to_paypal_format($orderItemsArr);
        $this->cartAmount = (string)$itemsArr['cartAmount']; // sum of the order

        $this->returnUrl = $serialized_session_id ? $this->returnUrl."&userData7=".md5($serialized_session_id) : $this->returnUrl;

        // Set the main vars
        $requestArr = array(
            'LOCALECODE' => $this->localCode,
            'RETURNURL' => $this->returnUrl,
            'CANCELURL' => $this->cancelUrl,
            'REQCONFIRMSHIPPING' => $this->reqConfirmShipping,
            'NOSHIPPING' => $this->noShipping,


            'MAXAMT' => $this->cartAmount,
            'PAYMENTREQUEST_0_AMT' => $this->cartAmount,
            'PAYMENTREQUEST_0_ITEMAMT' => $this->cartAmount,
            'PAYMENTREQUEST_0_CURRENCYCODE' => $this->currencyCodeType,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Authorization',

            'L_BILLINGTYPE0' => 'MerchantInitiatedBilling',
            'L_BILLINGAGREEMENTDESCRIPTION0' => 'one click deposits',
            'L_PAYMENTTYPE0' => 'InstantOnly',
            'L_BILLINGAGREEMENTCUSTOM0' => 'one click deposits',

            'HDRIMG' => $this->hdrImage,
        );

        // merge the main vars and the items.
        $requestArr = array_merge($requestArr, $itemsArr['items']);
        // send call to paypal
        $responseArr = $this->hash_call("SetExpressCheckout", $requestArr);
        if ($responseArr['TOKEN']) { // if success set token and move to paypal page
            $this->set_token($responseArr['TOKEN']);
            return $this->paypalUrl . $this->token;
        } else {
            return false;
        }

    }



    /*----------------------------------------------------------------------------------*/
    /**
     * get_express_checkout_details: this function start the calls to paypal. take token from session and start the process.
     * the order of functions:
     * 1. get_express_checkout_details().
     * 2. do_express_checkout_payment().
     * 3. send_order_and_do_capture().
     * 4. save_order_to_db().
     *
     * expect_values:
     *  null - take token from session that set in set_express_checkout()
     * @return :    $authorizationId - return by do_express_checkout_payment()
     *
     */
    public function get_express_checkout_details()
    {

        if (!$this->token) {
            return false;
        }

        $requestArr = array(
            'TOKEN' => $this->token,
        );
        $responseArr = $this->hash_call("GetExpressCheckoutDetails", $requestArr);
        if ($responseArr['ACK'] == 'Success' || $responseArr['ACK'] == 'SuccessWithWarning') {
            $payerId = $responseArr['PAYERID'];
            $payerEmail = $responseArr['EMAIL'];
            $this->cartAmount = $responseArr['AMT'];
            return $this->do_express_checkout_payment($payerId, $payerEmail);
        } else {
            return false;
        }

    }

    public function do_express_checkout_payment($payerId, $payerEmail)
    {
        $requestArr = array(
            'TOKEN' => $this->token,
            'PAYERID' => $payerId,

            'PAYMENTREQUEST_0_AMT' => $this->cartAmount,
            'PAYMENTREQUEST_0_CURRENCYCODE' => $this->currencyCodeType,
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Authorization',
        );
        $responseArr = $this->hash_call("DoExpressCheckoutPayment", $requestArr);
        if ($responseArr['ACK'] == 'Success' || $responseArr['ACK'] == 'SuccessWithWarning') {
            $authorizationId = $responseArr['PAYMENTINFO_0_TRANSACTIONID'];

            if ($responseArr['BILLINGAGREEMENTID']) {
                $this->billingAgreementId = $responseArr['BILLINGAGREEMENTID'];

                // save billingAgreementId to tb_paypal__token
                $db_fields = array(
                    'user_id' => $this->userId,
                    'title' => 'Paypal: ' . $payerEmail,
                    'cardMask' => $payerEmail,
                    'payer_id' => $payerId,
                    'billing_agreement_id' => $this->billingAgreementId,
                    'save_card' => $this->save_card,
                    'creditCompany' => 'paypal',
                    'last_update' => time(),
                );
                $this->db->insert('tb_paypal__token', $db_fields); //  in index.php move it to modules.functions
            }
            return $authorizationId; // $this->send_order_and_do_capture($authorizationId);
        } else {
            return false;
        }
    }

    public function send_order_and_do_capture($authorizationId, $orderId, $total = '', $token_id = 0)
    {
        //$orderArr = orderManager::get_order_information($orderId);
        //$store_title = str_replace(',',array(''),mcdonaldsManager::get_store_txt($orderArr['store_id']));
        //$desc = substr("{$orderArr['store_id']} {$store_title}",0,22);
        $desc = 'Desc';
        // after the order success in frs
        $requestArr = array(
            'AUTHORIZATIONID' => $authorizationId,
            'AMT' => ($total) ? $total : $this->cartAmount,
            'CURRENCYCODE' => $this->currencyCodeType,
            'COMPLETETYPE' => 'Complete',
            //'INVNUM' => "",
            'INVNUM' => urlencode($orderId), // new field
            'NOTE' => urlencode($desc), // new field
            //	'SOFTDESCRIPTOR' => urlencode($desc), // new field
        );
        $responseArr = $this->hash_call("DoCapture", $requestArr);

        if ($responseArr['ACK'] == 'Success' || $responseArr['ACK'] == 'SuccessWithWarning') {  //SuccessWithWarning
            $transactionId = $responseArr['TRANSACTIONID'];
            return $this->save_order_to_db($transactionId, $orderId, $token_id);
        } else {
            return false;
        }
    }



    /*----------------------------------------------------------------------------------*/
    /**
     * https://developer.paypal.com/docs/classic/api/merchant/DoVoid_API_Operation_NVP/
     *
     */
    public function return_to_customer_line_of_credit($authorizationId, $orderId)
    {

        // todo finish this method
        $requestArr = array(
            'AUTHORIZATIONID' => $authorizationId,
        );
        $responseArr = $this->hash_call("DoVoid ", $requestArr);

    }

    /*----------------------------------------------------------------------------------*/
    public function string_to_ascii($string)
    {
        $ascii = NULL;

        for ($i = 0; $i < strlen($string); $i++) {
            $ascii += ord($string[$i]);
        }

        return ($ascii);
    }
    /*----------------------------------------------------------------------------------*/
    /**
     * do_reference_transaction: pay to paypal without go to paypal page. only by billingId
     *
     * expect_values:
     *  $billingId - get from db. save_order_to_db() function save this parameter in the db.
     *    $orderItemsArr -    array(array('price' => '9.00','name' => 'בקבוק קולה','desc' => 'בקבוק 1.5 ליטר'),array('price' => '7.00','name' => 'פחית קולה','desc' => ' פחית קוקה קולה'));
     *
     * @return :    true / false
     *
     */
    public function do_reference_transaction($billingId, $orderItemsArr)
    {

        // Make array with all items details in paypal format
        $itemsArr = $this->replace_order_to_paypal_format($orderItemsArr);
        $this->cartAmount = (string)$itemsArr['cartAmount']; // sum of the order

        $this->billingAgreementId = $billingId;
        $requestArr = array(
            'REFERENCEID' => urlencode($this->billingAgreementId),

            'AMT' => urlencode($this->cartAmount),
            'CURRENCYCODE' => urlencode($this->currencyCodeType),
            'PAYMENTACTION' => 'Authorization',

        );

        $responseArr = $this->hash_call("DoReferenceTransaction", $requestArr);

        if ($responseArr['ACK'] == 'Success') {
            $authorizationId = $responseArr['TRANSACTIONID'];
            return $authorizationId; // $this->send_order_and_do_capture($authorizationId);
        } else {
            return false;
        }
    }

    /*----------------------------------------------------------------------------------*/

    public function save_order_to_db($transactionId, $orderId, $token_id = 0)
    {

        $db_fields = array(
            'user_id' => $this->userId,
            'order_id' => $orderId,
            'amount' => $this->cartAmount,
            'token' => $this->token,
            'transaction_id' => $transactionId,
            'token_id' => $token_id,  // 06.09.2016 - netanel: save token id to paypal__orders
            'status_id' => 1,
            'last_update' => time(),
        );
        $this->db->insert('tb_paypal__orders', $db_fields); //  in index.php move it to modules.functions

        return true;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * cancel_order: find the order and call to refund
     *
     *
     * expect_values:
     *  $order_id - order id in
     *
     * @return :    true / false
     *
     */
    public function cancel_order($order_id)
    {
        $query = $this->db->query("SELECT transaction_id,status_id FROM `tb_paypal__orders` WHERE `order_id`='{$order_id}'");
        $result = $this->db->get_stream($query);

        if ($result['status_id'] == 2) { // payment fail
            return array("err" => 'Payment not received');
        } else if ($result['status_id'] == 3) {
            return array("err" => 'Order all ready canceled');
        } else if ($result['status_id'] == 1) {
            $refund = $this->refund_transaction($result['transaction_id']);
            if ($refund) {
                $db_fieldsArr = array(
                    'status_id' => 3,
                );
                $this->db->update('tb_paypal_orders', $db_fieldsArr, 'order_id', $order_id);
                return true;
            } else {
                return array("err" => 'Refund failed');
            }
        }

    }

    /*----------------------------------------------------------------------------------*/
    /**
     * refound_order: find the order and call to refund
     *
     *
     * expect_values:
     *  $order_id - order id in salat
     *  $amount - (int) Optional, if refund is not on all the transaction.
     *
     * @return :    true / false
     *
     */
    public function refund_order($order_id, $amount = 0)
    {
        $query = $this->db->query("SELECT transaction_id,status_id FROM `tb_paypal__orders` WHERE `order_id`='{$order_id}'");
        $result = $this->db->get_stream($query);

        if ($result['status_id'] == 2) { // payment fail
            return array("err" => 'Payment not received');
        } else if ($result['status_id'] == 3) {
            return array("err" => 'Order all ready canceled');
        } else {
            $refund = $this->refund_transaction($result['transaction_id'], $amount);
            if ($refund) {
                return true;
            } else {
                return array("err" => 'Refund failed');
            }
        }
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * refund_transaction: refund the money to the user
     *
     * // **  before you refund in sandbox account you must to add shekel to your balance.
     *
     * expect_values:
     *  $transactionId - get from db. save_order_to_db() function save this parameter in the db.
     *    $amount - (int) Optional, if refound is not on all the transaction.
     *
     * @return :    true / false
     *
     */
    public function refund_transaction($transactionId, $amount = 0)
    {
        $requestArr = array(
            'TRANSACTIONID' => urlencode($transactionId),

            'REFUNDTYPE' => 'Full',
            'CURRENCYCODE' => urlencode($this->currencyCodeType),

        );

        // 07/02/2017 - netanel: add Partial refound
        if ($amount > 0) {
            $requestArr['REFUNDTYPE'] = 'Partial';
            $requestArr['AMT'] = $amount;
        }

        $responseArr = $this->hash_call("RefundTransaction", $requestArr);

        if ($responseArr['ACK'] == 'Success') {

            $refundTransactionId = $responseArr['REFUNDTRANSACTIONID'];
            $feeRefundAmt = $responseArr['FEEREFUNDAMT'];
            $grossRefundAmt = $responseArr['GROSSREFUNDAMT'];
            $netRefundAmt = $responseArr['NETREFUNDAMT'];
            $totalRefundedAmount = $responseArr['TOTALREFUNDEDAMOUNT'];
            $correlationId = $responseArr['CORRELATIONID'];

            $db_fields = array(
                'refund_transaction_id' => $refundTransactionId,
                'fee_refund_amt' => $feeRefundAmt,
                'gross_refund_amt' => $grossRefundAmt,
                'net_refund_amt' => $netRefundAmt,
                'total_refunded_amount' => $totalRefundedAmount,
                'correlation_id' => $correlationId,
                'last_update' => time(),
            );
            $this->db->insert('tb_paypal__refound', $db_fields); //  in index.php move it to modules.functions
            return true;
        } else {
            return false;
        }
    }


    /*----------------------------------------------------------------------------------*/
    /**
     * @param $token_id - id value in table tb_paypal__token
     */
    public function get_user_token_information($token_id)
    {

        $query = $this->db->query("SELECT billing_agreement_id FROM `tb_paypal__token` WHERE `id`='{$token_id}'");
        $result = $this->db->get_stream($query);
        return $result['billing_agreement_id'];
    } /*----------------------------------------------------------------------------------*/

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
        $query = "SELECT * FROM `tb_paypal__token` WHERE `id`={$token_id} AND `user_id`={$user_id}";
        $result = $this->db->query($query);
        if ($result->num_rows) {
            return true;
        }
        return false;
    }


    /**
     * hash_call: Function to perform the API call to PayPal using API signature
     * @methodName is name of API  method.
     * @nvpStr is nvp string.
     * returns an associtive array containing the response from the server.
     */
    public function hash_call($methodName, $requestArr)
    {
        //setting the curl parameters.
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->endPoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);

        //turning off the server and peer verification(TrustManager Concept).
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $nvpStr = $this->nvpHeader . "&" . http_build_query($requestArr);

        //if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
        //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php
        //if(USE_PROXY)
        if ($this->useProxy) {
            curl_setopt($ch, CURLOPT_PROXY, $this->proxyHost . ":" . $this->proxyPort);
        }

        //check if version is included in $nvpStr else include the version.
        if (!preg_match("/version=/i", $nvpStr)) {
            $nvpStr .= "&VERSION=" . urlencode($this->version);
        }

        $nvpRequest = "METHOD=" . urlencode($methodName) . $nvpStr;

        $_SESSION['ppl-sec-req'] = $nvpRequest;

        //setting the nvpreq as POST FIELD to curl
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpRequest);

        //getting response from server
        $response = curl_exec($ch);

        // insert to log table
        $db_fields = array(
            'user_id' => $this->userId,
            'request' => $nvpRequest,
            'response' => $response,
            'last_update' => time(),
        );
        $this->db->insert('tb_paypal__log', $db_fields);

        //convrting NVPResponse to an Associative Array
        $nvpResponseArray = $this->parseNVP($response);

        if (curl_errno($ch)) {
            // moving to display page to display curl errors
            $_SESSION['curl_error_no'] = curl_errno($ch);
            $_SESSION['curl_error_msg'] = curl_error($ch);
            $nvpResponseArray['error_no'] = curl_errno($ch);
            $nvpResponseArray['error_msg'] = curl_error($ch);
//		  $location = "APIError.php";
//		  header("Location: $location");
        } else {
            //closing the curl
            curl_close($ch);
        }

        // insert to error log table
        if ($nvpResponseArray['ACK'] != 'Success') {
            $db_fields = array(
                'user_id' => $this->userId,
                'request' => $nvpRequest,
                'response' => $response,
                'last_update' => time(),
            );
            $this->db->insert('tb_paypal__error_log', $db_fields); //  in index.php move it to modules.functions
            //mail($this->emailToSendDebug, 'error in paypal ' . __FILE__, print_r(array('nvpRequest' => $this->parseNVP($nvpRequest), 'nvpResponseArray' => $nvpResponseArray, 'Sess' => $_SESSION), true), 'MIME-Version: 1.0' . "\r\n" . 'Content-type: text/plain; charset=UTF-8' . "\r\n");
        }
        return $nvpResponseArray;
    }

    /** This function will take NVPString and convert it to an Associative Array and it will decode the response.
     * It is usefull to search for a particular key and displaying arrays.
     * @nvpstr is NVPString.
     * @nvpResponse is Associative Array.
     */
    public function parseNVP($nvpstr)
    {
        $nvpResponse = array();
        parse_str($nvpstr, $nvpResponse);
        return $nvpResponse;
    }
}


?>
