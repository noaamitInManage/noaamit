<?
/**
 * @author : gal zalait
 * @desc :
 * @var : 1.0
 * @last_update :
 *
 */

include($_SERVER['DOCUMENT_ROOT'] . '/_inc/class/orderManager.class.inc.php');

class cartManager extends BaseManager
{


    private static $instnace = null;

    public $numberOfPayments = 10;

    public $total = 0;
    public $price = 0;
    public $extra_optionsArr = array();

    function __construct()
    {
        parent::__construct();

    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @return cartManager|null
     * @dep
     */
    public static function getInstance()
    {
        if (self::$instnace === null) {
            //	echo "new";
            self::$instnace = new cartManager();
        }
        return self::$instnace;
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

    public function get_total()
    {
        $price = 0;

        if (isset($_SESSION['cart']['itemsArr'])) {
            foreach ($_SESSION['cart']['itemsArr'] as $itemArr) {
                $price += $itemArr['price'] * $itemArr['count'];
            }
        }

        if (isset($_SESSION['cart']['delivery_id'])) {
            $price += $this->get_cart_delivery_price($_SESSION['cart']['delivery_id']);
        }

        if ($price < 0) {
            $price = 0;
        }

        return $price;
    }


    /*----------------------------------------------------------------------------------*/
    /**
     * Method Name: do_payment
     *    $token_id
     * $delivery_id
     * @param $payment_id 1.cg ,2.paypal
     * @param $token_id tb__[cg|paypal]_token id
     * @param $delivery_id 1.cg ,2.paypal
     * @param $gift 1.yes 0. no
     * @param $groupon_money
     * @param $number_of_payments only for cg, if user want to split the payment
     * @param $checkout_process_type
     *
     */
    public function do_payment($payment_id, $token_id = '', $delivery_id = '', $obligo = 0, $number_of_payments = 0)
    {
        global $Seo, $website_url;

        $payment_status = false;
        $answerArr = array(
            "url" => '',
            "payment_method" => $payment_id,
            "error" => '',
        );

        $is_website_payment = (isset($_SESSION['payment_from_website']) && $_SESSION['payment_from_website'] == 1) ? 1 : 0;
        $User = User::getInstance();

//lock inventory of cart's items
        $lock_inventory_idsArr = $this->lock_inventory_cart_items();
        $_SESSION['cart']['lock_inventory_idsArr'] = $lock_inventory_idsArr;
        $_SESSION['cart']['delivery_id'] = $delivery_id;
        $_SESSION['cart']['obligo'] = $obligo;

        $order_id = orderManager::open_order_record($payment_id, $delivery_id, $obligo, $number_of_payments);

        // if the order already paid
        if(!$order_id) {
            $paymentsArr = orderManager::get_order_payments($_SESSION['open_order_id']);
            $total_money = $paymentsArr[$payment_id]['sum']; //only from cg or paypal
            $success_payment_answerArr = $this->get_success_payment_answer($_SESSION['open_order_id'], false, false, $checkout_process_type);

            if ($is_website_payment) {
                $_SESSION['order_information'] = $success_payment_answerArr;
                $_SESSION['order_information']['total_billed'] = $total_money;

                $answerArr = array(
                    "url" => $website_url . '/' . $Seo->getStaticUrl(4, 60),
                    "payment_method" => $payment_id,
                    "total_money" => $total_money,
                    "error" => '',
                );
            } else {
                $answerArr = array(
                    "url" => 'http://' . $_SERVER['HTTP_HOST'] . '/resource/payment/credit-guard/success/?json=' . json_encode($success_payment_answerArr, 1),
                    "payment_method" => $payment_id,
                    "total_money" => $total_money,
                    "error" => '',
                );
            }

            return $answerArr;
        }

        if ($is_website_payment || $checkout_process_type) {
            if (isset($_SESSION['cart']['cart_optionsArr']['giftArr']) && count($_SESSION['cart']['cart_optionsArr']['giftArr'])) {
                $giftArr = $_SESSION['cart']['cart_optionsArr']['giftArr'];
                cartManager::send_gift($order_id, $giftArr['from'], $giftArr['to_name'], $giftArr['to_email'], $giftArr['message']);
            }

            if (isset($_SESSION['cart']['cart_optionsArr']['delivery_address']) && $_SESSION['cart']['cart_optionsArr']['delivery_address'] > 0) {
                $address_id = siteFunctions::safe_value($_SESSION['cart']['cart_optionsArr']['delivery_address'], 'number');
                if ($address_id > 0) {
                    orderManager::set_address($order_id, $address_id);
                }
            }

        }

        if ($order_id) {
            $paymentsArr = orderManager::get_order_payments($order_id);
            $total_money = $paymentsArr[$payment_id]['sum']; //only from cg or paypal
        }

        $Cart = cartManager::getInstance();
        $cartArr = $Cart->get_cart();

        switch ($payment_id) {  // bitwise options
            case 1: // credit card (cg)
                if ($token_id) {
                    $Cg = new credit_guardManager();
                    if ($number_of_payments > 1) {
                        $Cg->numberOfPayments = $number_of_payments;
                    }

                    $tokenArr = $Cg->get_user_token_information($token_id);

                    if ($order_id) {
                        $auth_number = 0; // do the transcation without j5

                        if ($paymentsArr[1]['sum']) {
                            $answer = $Cg->j4($order_id, $tokenArr['token'], $auth_number, $tokenArr['cardExpiration'], $order_id, $paymentsArr[$payment_id]['sum']);
                        }

                        if ($groupon_money && $paymentsArr[4]['sum']) {
                            $this->use_groupon_money($order_id, $paymentsArr[4]['sum']);
                        }
                    }
                    $status = intval($answer['response']['result']);

                    //lock inventory log
                    if ($lock_inventory_idsArr) {
                        orderManager::update_order_status($order_id, configManager::$order_statusArr['inventory_update']);
                    }

                    if (isset($answer['response']['result']) && $status == 0) {
                        // order success
                        // make J4
                        // call j4 with the auth_number that we get in j5 call
                        $auth_number = $answer['response']['doDeal']['authNumber'];

                        $db_fields = array(
                            "order_id" => $order_id,
                            "terminal_id" => $Cg->terminal_id,
                            "token" => $tokenArr['token'],
                            "auth_number" => $auth_number,
                            "card_expiration" => $tokenArr['cardExpiration'],
                            "customer_x_field" => $order_id,
                            "sum" => $paymentsArr[1]['sum'],
                            "last_update" => time(),
                        );

                        siteFunctions::insert_to_db('tb_cg__charge_queue', $db_fields);

                        $j4_status = intval($answer['response']['result']);
                        if ($j4_status > 0 || !isset($answer['response']['result'])) {//error
                            // re load the payment form
                            $Cg = new credit_guardManager();
                            $answerArr = array(
                                "url" => $Cg->get_payment_form($paymentsArr[1]['sum'],$order_id) . '&ErrorCode=j4',
                                "payment_method" => $payment_id,
                                "total_money" => $total_money,
                                "error" => '',
                            );
                        } else { // OK
                            $payment_status = true;
                        }
                    } elseif ($paymentsArr[1]['sum'] == 0) {
                        $payment_status = true;
                    } else {
                        $Cg = new credit_guardManager();
                        $answerArr = array(
                            "url" => $Cg->get_payment_form($paymentsArr[1]['sum'],$order_id) . '&ErrorCode=j4',
                            "payment_method" => $payment_id,
                            "total_money" => $total_money,
                            "error" => '',
                        );
                    }
                } else {
                    $Cg = new credit_guardManager();
                    $answerArr = array(
                        "url" => $Cg->get_payment_form($paymentsArr[1]['sum'],$order_id),
                        "payment_method" => $payment_id,
                        "total_money" => $total_money,
                        "error" => '',
                    );
                }

                break;

            case 2: // paypal option
                $PaypalObj = new PaypalManager();
                $paymentsArr = orderManager::get_order_payments($order_id);

                $Cart = cartManager::getInstance();
                $cartArr = $Cart->get_cart();
                $itemArr = reset($cartArr['itemsArr']);

                $cartItemsArr = array(
                    array(
                        'name' => $itemArr['title'],
                        'desc' => $itemArr['sub_title'],
                        'total_price' => $paymentsArr[$payment_id]['sum'],
                    )
                );


                if ($order_id) {
                    if ($paymentsArr[$payment_id]['sum']) {
                        if ($token_id) {
                            $billingCheck = true; // check if token id is good
                            $orderCheckSeccuss = 1; // check if authorizationId is good
                            $billingAgreementId = $PaypalObj->get_user_token_information($token_id); // get store Ternial

                            $order_sum = $paymentsArr[$payment_id]['sum'];

                            if ($billingAgreementId) {
                                $authorizationId = $PaypalObj->do_reference_transaction($billingAgreementId, $cartItemsArr);
                                if ($authorizationId) {

                                    $db_fields = array(
                                        "order_id" => $order_id,
                                        "authorization_id" => $authorizationId,
                                        "token_id" => $token_id,
                                        "sum" => $order_sum,
                                        "last_update" => time(),
                                    );

                                    siteFunctions::insert_to_db('tb_paypal__charge_queue', $db_fields);
                                    $successPaypal = $PaypalObj->send_order_and_do_capture($authorizationId, $order_id);

                                    if ($successPaypal) { // success 17.5.2015

//                                        if ($paymentsArr[4]['sum']) {
//                                            $Cart->use_groupon_money($order_id, $paymentsArr[4]['sum']);
//                                        }
                                        if ($lock_inventory_idsArr) {
                                            orderManager::update_order_status($order_id, configManager::$order_statusArr['inventory_update']);
                                        }
                                        $payment_status = true;

                                        $answerArr = array(
                                            "url" => siteFunctions::get_base_url() . '/resource/payment/ppl/success/?json=' . json_encode($this->get_success_payment_answer($order_id), 1),
                                            "payment_method" => $payment_id,
                                            "total_money" => $total_money,
                                            "error" => '',
                                        );
                                    } else { // do capture failed
                                        $orderCheckSeccuss = -214;
                                    }

                                } else {
                                    $billingCheck = false;
                                }
                            } else {
                                $billingCheck = false;
                            }

                            // the proccess failed. if is the token return to paypal page. if is capture or set order return failure message
                            if (!$billingCheck) {
                                $answerArr = array(
                                    "url" => $PaypalObj->set_express_checkout($cartArr['itemsArr'], $_SESSION["lang_id"]),
                                    "payment_method" => $payment_id,
                                    "total_money" => $total_money,
                                    "error" => '',
                                );
                            } else {
                                if ($orderCheckSeccuss < 0) {
                                    $answerArr = array(
                                        "url" => 'http://' . $_SERVER['HTTP_HOST'] . '/resource/payment/ppl/failure/?json=' . json_encode(errorManager::get_error(500)),
                                        "payment_method" => $payment_id,
                                        "error" => strval(abs($orderCheckSeccuss)),// no connection to the restaurant || do capture failed
                                    );
                                }
                            }

                        } else {
                            // Redirect to paypal
                            $answerArr = array(
                                "url" => $PaypalObj->set_express_checkout($cartItemsArr, $_SESSION["lang_id"]),
                                "payment_method" => $payment_id,
                                "total_money" => $total_money,
                                "error" => '',
                            );
                        }
                    }

//                    if ($groupon_money) {
//                        $this->use_groupon_money($order_id, $paymentsArr[4]['sum']);
//                    }
                }


                // add order happen in payments.php file
                break;

            case 4: //groupon money / obligo
                if ($groupon_money && $paymentsArr[4]['sum']) {
                    //charge
                    $this->use_groupon_money($order_id, $paymentsArr[4]['sum']);

                    //lock inventory log
                    if ($lock_inventory_idsArr) {
                        orderManager::update_order_status($order_id, configManager::$order_statusArr['inventory_update']);
                    }

                    $payment_status = true;
                    $total_money = 0;
                } else {
                    $payment_status = false;
                    $answerArr = array(
                        "url" => 'http://' . $_SERVER['HTTP_HOST'] . '/resource/payment/obl/failure/?json=' . json_encode(errorManager::get_error(501)),
                        "payment_method" => $payment_id,
                        "error" => 501,
                    );
                }
                break;

            case 8: //100% coupon payment
                $Cart = self::getInstance();
                $cartArr = $Cart->get_cart();
                $total_money = 0;// coupon, no money

                if ($cartArr['total'] == 0) {
                    //lock inventory log
                    if ($lock_inventory_idsArr) {
                        orderManager::update_order_status($order_id, configManager::$order_statusArr['inventory_update']);
                    }

                    $payment_status = true;
                } else {
                    $payment_status = false;
                    $answerArr = array(
                        "url" => 'http://' . $_SERVER['HTTP_HOST'] . '/resource/payment/obl/failure/?json=' . json_encode(errorManager::get_error(502)),
                        "payment_method" => $payment_id,
                        "error" => 502,
                    );
                }

                break;
        }

        if ($answerArr['error'] || !$payment_status) {
            // cancel some changes
        } else {
            $complete_order_status = $this->complete_order($order_id, $lock_inventory_idsArr, $delivery_id, $paymentsArr);

            if ($is_website_payment) {
                $_SESSION['order_information'] = $this->get_success_payment_answer($order_id);
                $_SESSION['order_information']['total_billed'] = $total_money;

                if ($complete_order_status !== true) {
                    $_SESSION['order_information']['order_status'] = $complete_order_status;
                }

                $answerArr = array(
                    "url" => $website_url . '/' . $Seo->getStaticUrl(4, 60),
                    "payment_method" => $payment_id,
                    "total_money" => $total_money,
                    "error" => '',
                );

            } else {
                $answerArr = array(
                    "url" => 'http://' . $_SERVER['HTTP_HOST'] . '/resource/payment/credit-guard/success/?json=' . json_encode($this->get_success_payment_answer($order_id, false, true, 0, $complete_order_status), 1),
                    "payment_method" => $payment_id,
                    "total_money" => $total_money,
                    "error" => '',
                );
            }
        }

        return $answerArr;
    }


    /*----------------------------------------------------------------------------------*/
    public function empty_cart()
    {
        if (isset($_SESSION['cart'])) {
            unset($_SESSION['cart']);
        }
    }

    /*----------------------------------------------------------------------------------*/

    public function complete_order($order_id, $lock_inventory_idsArr, $delivery_id, $paymentsArr)
    {
        $first_item = reset($_SESSION['cart']['itemsArr']);
        $item_id = $first_item['item_id'];
        $total_items = $first_item['count'];
        $total_items = $total_items > 0 ? $total_items : 0;

        $cda_infoArr = self::get_cda_info($first_item['item_id'], $first_item['cda']);

        $verify_inventory = self::verify_item($first_item['item_id'], $first_item['cda'], $first_item['extra_params']);

        // set order status as paid anyway
        orderManager::update_order_status($order_id, configManager::$order_statusArr['paid']);

        // check inventory berfore place an order
        if ($verify_inventory !== true) {
            orderManager::cancel_order($order_id, 0, 0, 'out of stock');
            return $verify_inventory;
        }

        // tracking
        $total_paid = 0;
        foreach($paymentsArr as $payment_id => $paymentArr) {
            $total_paid += $paymentArr['sum'];
        }

        if($this->has_coupon()){
            $total_paid+= couponManager::get_real_discount($order_id);
        }

        $Opportunity = new opportunitiesManager($item_id);
        $curr = configManager::$default_currency;

        $desc = $first_item['title'] . ', ' . $first_item['sub_title'];
        Tracking::boosttrack_conversion($Opportunity->category_id, $total_paid, $curr, $order_id, $desc);

        // update total purchases counter
        $query_item = "UPDATE `tb_opportunity` SET `total_purchases` = `total_purchases` + '" . $total_items . "' WHERE `id` = '" . $item_id . "'";
        $result_item = mysql_unbuffered_query($query_item);

        orderManager::create_order_vouchers($order_id);

        //release all locked inventory
        foreach ($lock_inventory_idsArr AS $lock_id) {
            self::release_inventory($lock_id);
        }

        $cda_infoArr = self::get_cda_info($first_item['item_id'], $first_item['cda']);

        if($cda_infoArr['inventory'] <= 0) {
            opportunitiesManager::send_alert_out_of_stock($first_item['item_id'], $first_item);
        }

        //send delivery
        if ($delivery_id) {

        }

        if (isset($_SESSION['open_order_id'])) {
            unset($_SESSION['open_order_id']);
        }

        if (isset($_SESSION['already_success_payment'])) {
            unset($_SESSION['already_success_payment']);
        }

        if (isset($_SESSION['j4_answer'])) {
            unset($_SESSION['j4_answer']);
        }

        return true;
    }

    /*----------------------------------------------------------------------------------*/
    public function get_cart()
    {
        $this->price = $this->get_total();

        $answerArr = array(
            "itemsArr" => isset($_SESSION['cart']['itemsArr']) && ($_SESSION['cart']['itemsArr']) ? $_SESSION['cart']['itemsArr'] : array(),
            "couponArr" => $this->has_coupon() ? array($_SESSION['cart']['couponArr']) : array(),
            "total" => $this->price,
            "number_of_payments" => $this->get_number_of_payments(),
            "extra_fieldsArr" => $this->get_extra_fields(),
            //"address_id" => ($_SESSION['cart']['address_id']) ? $_SESSION['cart']['address_id'] : 0,
        );

        $answerArr['deliveryArr'] = array();
        $items_idsArr = array_keys($_SESSION['cart']['itemsArr']);
        $deliveryArr = $this->get_items_delivery_methods($items_idsArr);

        if ($deliveryArr) {
            $answerArr['deliveryArr'] = array_values($deliveryArr);
        }

        return $answerArr;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @param $itemsArr  array with item id array(20,25,30)
     */
    public function get_items_delivery_methods($items_idsArr, $num_of_items = 0)
    {
        include($_SERVER['DOCUMENT_ROOT'] . '/_static/delivery_methods.' . $_SESSION['lang'] . '.inc.php'); //$delivery_methodsArr
        $first_item = reset($items_idsArr);
        $Opportunity = new opportunitiesManager($first_item);
        $num_of_items = $_SESSION['cart']['itemsArr'][$first_item]['count'];

        foreach ($delivery_methodsArr as $key => $delivery_methodArr) {
            if (!in_array($key, array_keys($Opportunity->delivery_methodsArr))) {
                unset($delivery_methodsArr[$key]);
            } else {
                $delivery_methodsArr[$key]['price'] = $this->get_delivery_price($delivery_methodsArr, $key, $Opportunity->delivery_methodsArr[$key], $num_of_items);
            }
        }

        return $delivery_methodsArr;
    }

    /*----------------------------------------------------------------------------------*/

    public function get_delivery_price($delivery_methodsArr, $delivery_id, $current_price, $num_of_items)
    {
        $delivery_price = 0;

        if ($current_price != '') {
            $delivery_price = $current_price;
        } else {
            $delivery_price = $delivery_methodsArr[$delivery_id]['price'];
        }

        if ($delivery_methodsArr[$delivery_id]['multiply_by_item'] && $num_of_items) {
            $delivery_price *= $num_of_items;
        }

        return $delivery_price;

    }

    /*----------------------------------------------------------------------------------*/

    public function get_cart_delivery_price($delivery_id)
    {
        $total_with_delivery = $_SESSION['cart']['deliveryArr'][$delivery_id]['price'];
        return $total_with_delivery;
    }

    /*----------------------------------------------------------------------------------*/

    public function del_item($item_key)
    {
        if (isset($_SESSION['cart']['itemsArr'][$item_key])) {
            unset($_SESSION['cart']['itemsArr'][$item_key]);
        }
    }

    /*----------------------------------------------------------------------------------*/


    public function add_coupon_to_cart($coupon_id, $coupon_code)
    {
        $Coupon = new couponManager($coupon_id);
        $_SESSION['cart']['couponArr'] = array(
            'id' => $Coupon->id,
            'title' => $Coupon->title,
            'code' => $coupon_code,
            'discount_amount' => $Coupon->discount_amount,
            'discount_type' => $Coupon->discount_type_id,
        );

        return true;
    }

    public function remove_coupon_from_cart($coupon_id)
    {
        $return = true;

        try {
            unset($_SESSION['cart']['couponArr']);
        } catch (Expresion $e) {
            $return = false;
        }

        return $return;
    }

    /*----------------------------------------------------------------------------------*/


    public function add_to_cart($item_id, $cda, $extra_params, $cid=0)
    {
        if (configManager::$enable_only_one_item_in_cart) {
            self::empty_cart();
        }

        $verify_status = self::verify_item($item_id, $cda, $extra_params);

        if ($verify_status === true) {
            $_SESSION['cart']['itemsArr'][$item_id] = self::get_session_item($item_id, $cda, $extra_params);

            $this->set_item_amount($item_id, $_SESSION['cart']['itemsArr'][$item_id]['count']);

            if ($deliveryArr = $this->get_items_delivery_methods(array($item_id))) {
                $_SESSION['cart']['deliveryArr'] = $deliveryArr;
            }
            //$this->send_add_to_cart_hit($cid);
        } else {
            return $verify_status;
        }

        return true;
    }

    /*----------------------------------------------------------------------------------*/
//hit for google analytics
    public function send_add_to_cart_hit($cid){


    }

    /*----------------------------------------------------------------------------------*/

    public function set_address_to_cart($address_id)
    {
        $_SESSION['cart']['address_id'] = $address_id;
    }

    /*----------------------------------------------------------------------------------*/

    public function set_item_amount($item_id, $amount)
    {
        $cda = $_SESSION['cart']['itemsArr'][$item_id]['cda'];
        $extra_params = $_SESSION['cart']['itemsArr'][$item_id]['extra_params'];
        $current_amount = intval($_SESSION['cart']['itemsArr'][$item_id]['count']);

        $cda_infoArr = self::get_cda_info($item_id, $cda);

        if ($amount < $cda_infoArr['minimum_coupons'] || $amount > ($cda_infoArr['minimum_coupons'] + configManager::$opportunity_max_amount)) {
            return 415;
        }

        if ($cda_infoArr['inventory'] + $current_amount < $amount) {
            return 416;
        }

        $_SESSION['cart']['itemsArr'][$item_id]['count'] = $amount;

        if ($deliveryArr = $this->get_items_delivery_methods(array($item_id))) {
            $_SESSION['cart']['deliveryArr'] = $deliveryArr;
        }

        $User = User::getInstance();
        if($User->id) {
            self::lock_inventory($item_id, $cda, $extra_params, $amount);
        }

        return true;
    }

    /*----------------------------------------------------------------------------------*/




    /*----------------------------------------------------------------------------------*/

    public static function verify_item($item_id, $extra_params = '')
    {

        return true;
    }

    /*----------------------------------------------------------------------------------*/

    public function get_success_payment_answer($order_id, $saved_card = false, $first_success_payment = true, $checkout_process_type = 0, $complete_order_status = true)
    {

    }


    /*----------------------------------------------------------------------------------*/

    public function verify_cart()
    {
        $verify = true;

        if (!$_SESSION['cart']['itemsArr']) {
            return 412;
        }

        if($this->get_number_of_payments() < $this->numberOfPayments) {
            return 419;
        }

        foreach ($_SESSION['cart']['itemsArr'] AS $itemArr) {
            $verity_status = self::verify_item($itemArr['item_id'], $itemArr['cda'], $itemArr['extra_params']);
            if ($verity_status !== true) {
                return $verity_status;
            }

            if (isset($_REQUEST["deliveryId"]) && $_REQUEST["deliveryId"]) {
                $items_idsArr = array_keys($_SESSION['cart']['itemsArr']);

                if (!array_key_exists($_REQUEST["deliveryId"], $this->get_items_delivery_methods($items_idsArr))) {
                    return 411;
                }
            }

            return true;
        }
    }


    /*----------------------------------------------------------------------------------*/

    public function use_obligo_money($order_id, $amount)
    {
        $Money = new obligoManager();
        $User = User::getInstance();

        if ($amount > 0) {
            return $Money->charge_obligo_card($amount, $order_id, $User->id, '');
        }

        return true;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method Name: lock_inventory_cart_items
     *
     * @return array
     */
    public function lock_inventory_cart_items()
    {

    }


    /**
     * Method Name: lock_inventory
     *
     * lock inventory of specific item
     *
     * @param $opportunity_id
     * @param $cda
     * @param $amount
     * @return int
     */
    public static function lock_inventory($item_id, $cda, $extra_params, $amount)
    {

    }

    /*----------------------------------------------------------------------------------*/

    public static function lock_products_inventory($user_id, $opportunity_id, $cda, $extra_params, $amount)
    {

    }

    /*----------------------------------------------------------------------------------*/

    public static function release_inventory($lock_id)
    {

    }

    /*----------------------------------------------------------------------------------*/

    public static function release_product_inventory($lock_id)
    {

    }

    /*----------------------------------------------------------------------------------*/

    public function get_usable_groupon_money()
    {
        $User = User::getInstance();
        $groupon_money = $User->getGrouponMoney(false);

        if ($groupon_money == 0) {
            $usable_groupon_money = 0;
        } elseif ($groupon_money >= $this->get_total()) {
            $usable_groupon_money = $this->get_total();
        } else {
            $usable_groupon_money = $groupon_money;
        }

        $usable_groupon_money = $usable_groupon_money > 0 ? $usable_groupon_money : 0;

        return $usable_groupon_money;
    }

    /*----------------------------------------------------------------------------------*/

    public function has_coupon()
    {
        $has_coupon = false;

        foreach ($_SESSION['cart']['couponArr'] as $couponArr) {
            if ($couponArr['id']) {
                $has_coupon = true;
            }
        }

        return $has_coupon;
    }

    /*----------------------------------------------------------------------------------*/

    public static function send_gift($order_id, $from, $to_name, $to_email, $message)
    {
        $err = 0;
        $ts = time();

        $htmlArr = array(
            'from' => $from,
            'to_name' => $to_name,
            'to_email' => $to_email,
            'order_id' => $order_id,
            'message' => $message,
            'last_update' => $ts,
        );

        siteFunctions::insert_to_db('tb_orders__sent_gift', $htmlArr);

//		voucherManager::send_vouchers_on_mail($order_id, true);
    }

    /*----------------------------------------------------------------------------------*/

    /**
     * Method Name: get_number_of_payments
     *
     *  Sets the length of the payments by the sum of the cart
     * (if it call from payment_form method, its calculate without the groupon money
     *
     * @param $sum
     * @return int
     */
    public function get_number_of_payments($sum = 0)
    {
        if (!$sum) {
            $sum = $this->get_total();
            /* if(isset($_SESSION['cart']['groupon_money']) && $_SESSION['cart']['groupon_money']) {
                 $usable_groupon_money = $this->get_usable_groupon_money();
                 $sum = $sum - $usable_groupon_money;
             }*/
        }

        if ($sum <= 298) {
            return 1;
        } else if ($sum <= 748) {
            return 3;
        } else {
            return 12;
        }
    }

    public function get_order_information(){
        return $_SESSION['order_information'];
    }

    public function set_use_groupon_money($use_groupon_moneyFlg){
        $_SESSION['cart']['groupon_money'] = $use_groupon_moneyFlg;
    }

    public function set_delivery_id($delivery_method_id){
        $_SESSION['cart']['delivery_id'] = $delivery_method_id;
    }

    public static function save_cart($opportunity_id, $cda, $extra_params){
        $_SESSION["saved_opportunity"] = array(
            "opportunity_id" => $opportunity_id,
            "cda" => $cda,
            "extra_params" => $extra_params,
            "saved_ts" => time()
        );
    }

    public static function unsave_cart(){
        unset($_SESSION["saved_opportunity"]);
    }
}

?>