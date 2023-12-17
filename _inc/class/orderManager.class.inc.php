<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 30/10/13
 * Time: 20:15
 *
 */

/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 30/10/13
 * Time: 20:15
 * @author : gal zalait
 * @desc :
 * @var : 1.0
 * @last_update :
 */
class orderManager extends BaseManager
{


    public $RestOb = '';
    public $save_json_log = true;

    //* the time that we need to wait to get the macdonald's order id from the server */
    public $confirm_max_timeout = 20;
    /*
     * check if we get the order id every X seconds
     */
    public $confirm_interval = 2;

    private static $instnace = null;

    /*----------------------------------------------------------------------------------*/

    function __construct($rest_id, $lang = 'he')
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


    public static function getInstance()
    {
        if (null === self::$instnace) {
            //	echo "new";
            self::$instnace = new orderManager();
        } else {
            //	echo "memory";
        }
        return self::$instnace;
    }


    /*----------------------------------------------------------------------------------*/
    /**
     * @param $order_id id from tb_orders
     * @return array
     */
    public function build_json_order($order_id, $payment_id)
    {

    }


    /*----------------------------------------------------------------------------------*/

    public static function get_payment_json_array($payment_id, $order_id = '', $total_price = 0.0)
    {


    }

    /*----------------------------------------------------------------------------------*/

    public function get_test_order()
    {


    }

    /*----------------------------------------------------------------------------------*/

    public static function get_order_address_id()
    {
        return (isset($_SESSION['cart']['delivery']['address_id'])) ? $_SESSION['cart']['delivery']['address_id'] : 0;
    }

    /*----------------------------------------------------------------------------------*/


    /*----------------------------------------------------------------------------------*/

    public function get_usable_obligo()
    {
        $User = User::getInstance();
        $groupon_money = $User->getObligo(false);
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

    public static function build_payments_table($order_id, $payment_id, $groupon_money = 0,$total_payments = 1)
    {
        $Cart = cartManager::getInstance();
        $cartArr = $Cart->get_cart();
        $paymentArr = array();
        $obligo_sum = $Cart->get_usable_obligo();
        $ts = time();
        if ($groupon_money && $payment_id != 4 && $obligo_sum) {
            $paymentArr[] = array(
                "order_id" => $order_id,
                "payment_id" => 4,
                "sum" => $obligo_sum,
                "total_payments" => 1,
                "last_update" => $ts,
            );
            $paymentArr[] = array(
                "order_id" => $order_id,
                "payment_id" => $payment_id,
                "sum" => $cartArr['total'] - $obligo_sum,
                "total_payments" => $total_payments,
                "last_update" => $ts,
            );

        } else {
            if ($payment_id == 4 && $obligo_sum == $cartArr['total']) { // calc amount per card
                $amount = $obligo_sum;
            } else {
                $amount = $cartArr['total'];
            }
            $paymentArr[] = array(
                "order_id" => $order_id,
                "payment_id" => $payment_id,
                "sum" => $amount,
                "total_payments" => $total_payments,
                "last_update" => $ts,
            );
        }

        foreach ($paymentArr AS $payArr) {
            siteFunctions::insert_to_db('tb_orders_payment_split', $payArr);
        }
    }

    /*----------------------------------------------------------------------------------*/

    public static function save_user_info($order_id)
    {
        $db_fieldsArr = array(
            'order_id' => $order_id,
            'resolution' => $_SESSION['api']['resolution'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'platform' => $_SESSION['api']['platform'],
            'server_version' => $_SESSION['api']['version'],
            'app_version' => $_SESSION['api']['application_version'],
            'ip' => $_SERVER['REMOTE_ADDR'],
            'lang_id' => $_SESSION['lang_id'],
            'last_update' => time(),
        );

        siteFunctions::insert_to_db('tb_orders_user_info', $db_fieldsArr);
    }

    /*----------------------------------------------------------------------------------*/

    public static function use_cart_coupon($order_id, $user_id, $cartArr)
    {
        $first_coupon = reset($cartArr['couponArr']);
        $coupon_id = $first_coupon['id'];

        if ($coupon_id) {
            $ts = time();
            $Coupon = new couponManager($coupon_id);
            $db_fieldsArr = array(
                'coupon_type' => $Coupon->discount_type_id,
                'coupon_id' => $Coupon->id,
                'coupon_code' => $first_coupon['code'],
                'user_id' => $user_id,
                'used_ts' => $ts,
                'order_id' => $order_id,
                'real_discount' => $Coupon->discount_amount,
                'last_update' => $ts,
            );
            siteFunctions::insert_to_db('tb_coupons__user_log', $db_fieldsArr);

            $db_fieldsArr = array(
                'active' => 2,
            );

            $db_filterArr = array(
                'coupon_id' => $Coupon->id,
                'coupon_code' => $first_coupon['code'],
            );
            siteFunctions::update_db('tb_coupons__codes', $db_fieldsArr, '', '', $db_filterArr);
        }
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_order_payments($order_id)
    {
    	$Db = Database::getInstance();
        $paymentsArr = array();
        $query = "SELECT * FROM `tb_orders_payment_split` WHERE `order_id`=$order_id";
        $result = $Db->query($query);
        while ($row = $Db->get_stream($result)) {
            $paymentsArr[$row['payment_id']] = $row;
        }
        return $paymentsArr;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * Method Name: open_order_record
     *
     * open a record in tb_order and also insert the order items to tb_order_items
     *
     * @param $payment_id 1.credit card , 2.paypal 4.cash
     * @return int
     */
    public static function open_order_record($payment_id, $delivery_id, $obligo = 0, $number_of_payments = 1)
    {
        $Db = Database::getInstance();

        if (isset($_SESSION['open_order_id']) && $_SESSION['open_order_id']) {
            $order_rowArr = self::get_order($_SESSION['open_order_id']);
            $open_order_status = $order_rowArr['status'];

            if ($open_order_status != configManager::$order_statusArr['paid'] && $open_order_status != configManager::$order_statusArr['inventory_update']) {
                self::update_order_status(intval($_SESSION['open_order_id']), configManager::$order_statusArr['incomplete'], false);
                //return $_SESSION['cart']['open_order_id'];
            }
        }

        $User = User::getInstance();
        $Cart = cartManager::getInstance();
        $cartArr = $Cart->get_cart(); // take the order items

        $salat_user_id = isset($_SESSION['userArr']['salat_user_id']) && $_SESSION['userArr']['salat_user_id'] ? $_SESSION['userArr']['salat_user_id'] : 0;

        $ts = time();
        $db_fields = array(
            "user_id" => $User->id,
            "area_id" => $_SESSION['area_id'],
            "source_id" => configManager::$sourceArr[$_SESSION['api']['platform']],
            "address_id" => self::get_order_address_id(),
            "payment_id" => $payment_id,
            "total_sum" => $Cart->get_total(),
            "status" => configManager::$order_statusArr['open'],
            "delivery_id" => $delivery_id,
            "delivery_sum" => $Cart->get_cart_delivery_price($delivery_id),
            "ip" => $_SERVER['REMOTE_ADDR'],
            "lang_id" => $_SESSION['lang_id'],
            "on_date_ts" => $ts,
            "salat_user_id"=> $salat_user_id,
            "last_update" => $ts,
        );
        $order_id = $Db->insert('tb_orders', $db_fields);

        self::build_payments_table($order_id, $payment_id, $obligo ,$number_of_payments);
        self::save_user_info($order_id);
        self::use_cart_coupon($order_id, $User->id, $cartArr);

        /* factorization cart items and insert them to tb_order_items*/
        if (isset($cartArr['itemsArr']) && $cartArr['itemsArr']) {
            foreach ($cartArr['itemsArr'] AS $item_key => $valueArr) {
                if(intval($valueArr['item_id']) == 0) {
                    continue;
                }

                $Opportunity = new opportunitiesManager($valueArr['item_id']);

                // 26/12/2016 - netanel: build title by cda
                $Category = new categoriesManager($Opportunity->category_id);
                $opportunity_type = $Category->getCategoryType();
                if ($opportunity_type == 1) { // Dynamic Fields
                    $titlesArr = $Opportunity->get_dynamic_field_title($valueArr['cda']);
                    $title = $titlesArr['title'];
                    $sub_title = $titlesArr['sub_title'];
                } else {  // Deal Options
                    $title = $Opportunity->title;
                    $sub_title = $Opportunity->optionsArr[$valueArr['cda']]['title'];
                }

                $db_fields = array(
                    "order_id" => $order_id,
                    "supplier_id" => $Opportunity->supplier_id,
                    "date_feature_end" => $Opportunity->date_feature_end,
                    "valid_until_ts" => $Opportunity->get_expiration_ts($valueArr['cda']),
                    "title" =>  $title,
                    "sub_title" => $sub_title,
                    "item_id" => $valueArr['item_id'],
                    "cda" => $valueArr['cda'],
                    "extra_params" => serialize($valueArr['extra_params']),
                    "fields" => serialize($valueArr),
                    "sum" => $valueArr['price'],
                    "fine_prints_id" => $Opportunity->fine_prints_id,
                    "creation_ts" => $ts,
                    "last_update" => $ts,
                );

                for ($i = 0; $i < $valueArr['count']; $i++) {
                    $order_item_id = siteFunctions::insert_to_db('tb_orders_items', $db_fields);

                    self::save_order_item_info($order_item_id, $valueArr['item_id'], $valueArr['cda']);

                    /*if ($Opportunity->generate_vouchers == 1) {
                        //create voucher linked to order item id
                        $generate_qr_code = $Opportunity->generate_qr_code ? true : false;
                        $voucher_id = voucherManager::create_voucher($order_item_id, $valueArr['cda'], $generate_qr_code, $Opportunity->supplier_voucher_type, $Opportunity->external_barcode_type);

                        //update voucher id
                        $db_fields_update = array(
                            'voucher_id' => $voucher_id
                        );

                        siteFunctions::update_db('tb_orders_items', $db_fields_update, 'id', $order_item_id);
                    }*/

                    if ($opportunity_type == 2) { // Deal Options
                        // order products by cda
                        opportunitiesManager::insert_order_products($order_item_id, $valueArr['cda']);
                    }
                }
            }
        }

        self::update_order_status($order_id, configManager::$order_statusArr['open']);
        $_SESSION['open_order_id'] = $order_id;

        return $order_id;
    }


    /*----------------------------------------------------------------------------------*/

    public static function save_order_item_info($order_item_id, $opportunity_id, $cda)
    {

    }

    /*----------------------------------------------------------------------------------*/

    /**
     * Method Name: get_order_status
     * Returns an order status
     * @param $order_id
     * @return bool|int
     */
    public static function get_order_status($order_id)
    {

    }

    /*----------------------------------------------------------------------------------*/
    /**
     * save order lifecycle log - on every order status change write to this table
     * @param $order_id
     * @param $status_id
     * @param $refund_user
     */
    public static function update_order_status($order_id, $status_id, $refund_user = true, $note = '')
    {
        $ts = time();

        //save log
        $db_fields = array(
            "order_id" => $order_id,
            "status_id" => $status_id,
            "note" => $note,
            "ip" => $_SERVER['REMOTE_ADDR'],
            "server_host" => array_search($_SERVER['SERVER_ADDR'], configManager::$web_servers_internal_ipsArr) ?: $_SERVER['HTTP_HOST'] . ' - ' . $_SERVER['SERVER_ADDR'],
            "last_update" => $ts,
        );
        siteFunctions::insert_to_db('tb_orders_status_log', $db_fields);

        //update order record
        $db_fields = array(
            'status' => $status_id,
            'last_update' => $ts,
        );

        if($status_id == configManager::$order_statusArr['cancel']) {
            $db_fields['cancel_ts'] = $ts;
        }

        if($status_id == configManager::$order_statusArr['incomplete'] && $refund_user) {
            $orderArr = orderManager::get_order($order_id);
            $paymentsArr = orderManager::get_order_payments($order_id);
            $Obligo = new obligoManager();
            $Obligo->refound_user($order_id, $paymentsArr, $orderArr);
        }

        siteFunctions::update_db('tb_orders', $db_fields, 'id', $order_id);
    }



    /*----------------------------------------------------------------------------------*/

    public static function set_address($order_id, $address_id)
    {
        $ts = time();

        $User = User::getInstance();
        if (!$User->id) {
            return 150; // על מנת לבצע פעולה זו עליך להיות מחובר
        }

        if (!array_key_exists($address_id, $User->getUserAddress())) {
            return 520;
        }

        //update order address_id
        $db_fields = array(
            'address_id' => $address_id,
            'last_update' => $ts,
        );

        siteFunctions::update_db('tb_orders', $db_fields, 'id', $order_id);
        $_SESSION['cart']['address_id'] = $address_id;

        return 0;
    }

    /*----------------------------------------------------------------------------------*/

    public function update_order($order_id, $db_fields = array(), $change_status_from = '')
    {


    }


    /*----------------------------------------------------------------------------------*/

    public function write_user_info($order_id)
    {

        $db_fields = array(
            "order_id" => $order_id,
            "resolution" => $_SESSION['api']['resolution'],
            "user_agent" => $_SERVER['HTTP_USER_AGENT'],
            "platform" => $_SESSION['api']['platform'],
            "server_version" => $_SESSION['api']['version'],
            "app_version" => (isset($_SESSION['api']['application_version']) && $_SESSION['api']['application_version']) ? $_SESSION['api']['application_version'] : 10,
            "ip" => $_SERVER['REMOTE_ADDR'],
            "lang_id" => $_SESSION['lang_id'],
            "last_update" => time(),
        );
        siteFunctions::insert_to_db('tb_order_user_info', $db_fields);

    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @param $payment_id (options : 1.credit card , 2. PayPal , 4.cash)
     * @return array
     */
    public function add_order($payment_id)
    {


    }


    /*----------------------------------------------------------------------------------*/

    public function write_json_to_log($order_id, $json)
    {
		$Db = Database::getInstance();
        $db_fields = array(
            "order_id" => $order_id,
            "json" => $json,
        );
        foreach ($db_fields AS $key => $value) {
            $db_fields[$key] = $Db->make_escape($value);
        }
        $query = "INSERT INTO `tb_orders_json_log` (`" . implode("`,`", array_keys($db_fields)) . "`) VALUES ('" . implode("','", array_values($db_fields)) . "')";
        $res = $Db->query($query);

    }

    /*----------------------------------------------------------------------------------*/

    public function order_faild_notice($order_id, $ansArr, $detailsArr = array(), $mail_title = 'ההזמנה נכשלה')
    {
        global $website_url;;
        global $order_fail_notice_emailArr;
        $mail_header = lang('mail_header');
        $mail_footer = lang('mail_footer');
        $mail_style = lang('mail_style');
        $Cart = Cart::getInstance();
        $User = User::getInstance();
        $store_id = $Cart->get_store();
        $site_link = $website_url . '/';
        $logo_link = $site_link . '/mobile/_media/images/general/mcd_logo.png';
        $mail_header = str_replace('{$logo_link}', $logo_link, $mail_header);
        $date = date('d/m/y [H:i]');
        $ansArr['err'] = ($ansArr['err']) ? $ansArr['err'] : $ansArr['err'];
        $ans = print_r($ansArr, true);
        $ex_content = print_r($detailsArr, true);
        $content = <<<HTML
		<div class="content">
			<table bgcolor="#fcfcfc">
			<caption>{$mail_title}</caption>
				<tr>
					<td class="small" width="20%" style="vertical-align: top; padding-right:10px;"></td>
					<td width="60%">
						<h4>
							<strong>
						הזמנה מספר : {$order_id}

						<br>
						קוד מסעדה :
						{$store_id}
						<br>
						סיבת הכישלון:
						{$ansArr['err']}
						{$ans}
						<br>

						קוד משתמש :
						{$User->id}

						<br>
						שם משתמש :
						{$User->user_name}
						<br>
						תאריך:
						{$date}



						{$ex_content}
							</strong>
						</h4>
						<p class=""> &nbsp;</p>
					</td>
					<td width="20%" align="left">
						<strong>

						</strong>
					</td>
				</tr>
			</table>
		</div><!-- /content -->
HTML;


        $html = <<<HTML

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
{$mail_style}
</style>
</head>
	<body>
				{$mail_header}
				{$content}
				{$mail_footer}
	</body>
</html>
HTML;

        if (in_array($_SERVER['REMOTE_ADDR'], array('62.219.212.139', '81.218.173.175', '37.142.40.96'))) {
            $order_fail_notice_emailArr = array(
                'david@inmanage.net',
                'netanel@inmanage.net',
                'raanan@inmanage.net',
                //'gal@inmanage.co.il',
            );
        }

        siteFunctions::send_mail($order_fail_notice_emailArr, $mail_title, $html);
        //siteFunctions::send_mail('gal@inmanage.net','ההזמנה נכשלה',$html);
    }

    /*----------------------------------------------------------------------------------*/

    public function send_order_mail($order_id, $payment_id)
    {
        $Cart = Cart::getInstance();
        $User = User::getInstance();
        $cartArr = $Cart->get_cart();
        $html = get_cart_mail_html_new($cartArr, $order_id, $payment_id, $Cart->get_store());
        $email_subject = lang('order_email_subject');
        if ($User->id == 1) {
            //	die('<hr /><pre>' . print_r($html, true) . '</pre><hr />');
        }

        return siteFunctions::send_mail($User->email, $email_subject . $order_id, $html);
    }


    /*----------------------------------------------------------------------------------*/

    public static function check_if_payment_already_taken($order_id, $payment_id)
    {
        //already
		$Db = Database::getInstance();
        switch ($payment_id) {
            case 1: // credit card (cg)
                $query = "SELECT * FROM `tb_cg__orders` WHERE `order_id` = {$order_id} AND  `status_id`=1";
                $result = $Db->query($query);
                if (($result->num_rows)) {
                    return true;

                }
                return false;
                break;

            case 2:// PayPal
                $query = "SELECT * FROM `tb_paypal__orders` WHERE `order_id` = {$order_id} AND  `status_id`=1";
                $result = $Db->query($query);
                if (($result->num_rows)) {
                    return true;

                }
                return false;
                break;


        }


    }

    /*----------------------------------------------------------------------------------*/

    public static function get_order_information($order_id)
    {

    }

    /*----------------------------------------------------------------------------------*/

    public static function get_order($order_id)
    {
    	$Db = Database::getInstance();
        $query = "
			SELECT * FROM `tb_orders`
				WHERE `id` = {$order_id}
		";
        $result = $Db->query($query);
        if (($result->num_rows)) {
            $row = $Db->get_stream($result);
            return $row;
        } else {
            return false;
        }
    }

    /*----------------------------------------------------------------------------------*/
    public static function cancel_old_order($security_code) {

    }


    /*----------------------------------------------------------------------------------*/

    public static function cancel_order($order_id, $send_email = 1, $from_user = 1, $note = '')
    {
        $orderArr = orderManager::get_order($order_id);

        $ex_error = false;
        $paymentsArr = orderManager::get_order_payments($order_id);
        $total_money = $paymentsArr[$orderArr['payment_id']]['sum']; //only from cg or paypal
        if ($total_money > 0) {
            if ($orderArr['status'] == 2) { // only paid
                switch ($orderArr['payment_id']) {
                    case 1: //CG
                        $Cg = new credit_guardManager();
                        $answerArr = $Cg->cancel_order($order_id);
                        $status = intval($answerArr['response']['refundDeal']['status']);
                        if ($status != 0) {
                            $ex_error = $answerArr['response']['message'];
                        } else {
                            $ex_error = false;
                        }

                        break;

                    case 2: // paypal

                        $PaypalObj = new PaypalManager();
                        $refund = $PaypalObj->cancel_order($order_id);
                        if (!$refund || is_array($refund)) { // array = error
                            $ex_error = $refund['err'];
                        } else {
                            $ex_error = false;
                        }
                        break;

                }
            } else {
                $ex_error = true;
            }
        }

        // refund money success
        if (!$ex_error) {

            $Obligo = new obligoManager();
            $Obligo->refound_user($order_id, $paymentsArr, $orderArr);

            $user_infoArr = User::get_user_info($orderArr['user_id']);
            if ($from_user) {
                $title = lang('user_cancel_order_email_title');
                $message = lang('user_cancel_order_email_msg');
                siteFunctions::send_mail($user_infoArr['email'], $title, $message);
            }

            self::cancel_order_vouchers($order_id);
            self::update_order_status($order_id, configManager::$order_statusArr['cancel'], true, $note);
            self::decrease_opportunity_total_purchases_by_order($order_id);

            $return = true;
        } else {
            $return = false;
        }

        return array(
            'status' => $return,
            'err' => $ex_error,
        );
    }

    /*----------------------------------------------------------------------------------*/


    /*----------------------------------------------------------------------------------*/
    /**
     * refund_order
     *
     * @param $type_id (int) 1. cg / 2. paypal / 4. groupon money
     * @param $order_id
     * @param $amount (int!) Sum to refund
     * @return bool|resource
     */
    public static function refund_order($type_id,$order_id,$amount) {
        $return = false;

        switch ($type_id) {
            case 1: // cg
                $Cg = new credit_guardManager();
                $answerArr = $Cg->refund_order($order_id,$amount);

                $status = isset($answerArr['response']['refundDeal']['status']) ? intval($answerArr['response']['refundDeal']['status']) : 1;
                if ($status == 0) {
                    $return = true;
                }
                break;
            case 2: // paypal
                $PaypalObj = new PaypalManager();
                $refund = $PaypalObj->refund_order($order_id,$amount);

                if ($refund && !is_array($refund)) { // array = error
                    $return = true;
                }
                break;
            case 4: // groupon money
                $orderArr = orderManager::get_order($order_id);

                // time to valid(2 years)
                $valid_until_ts = strtotime("+ ".obligoManager::$refound_valid_until_years." years");

                // just details for groupon_money table
                $from = 'Refound from order id: '.$order_id;
                $sys_user_id = $_SESSION['salatUserID'];
                $ip = $_SERVER["REMOTE_ADDR"];

                $Obligo = new obligoManager();
                $return = $Obligo->load_to_obligo_card($orderArr['user_id'],$amount, $valid_until_ts, $from, $sys_user_id, $ip);
                break;
        }
        // save to log
        if($return) {
            $db_fieldsArr = array(
                'order_id' => $order_id,
                'type_id' => $type_id, // 1. cg / 2. paypal / 4. groupon money
                'amount' => $amount,
                'ip' => $_SERVER['REMOTE_ADDR'],
                'last_update' => time(),
            );
            siteFunctions::insert_to_db('tb_orders_refund', $db_fieldsArr);
        }

        return $return;
    }

    /*----------------------------------------------------------------------------------*/


    /*----------------------------------------------------------------------------------*/

    public static function get_order_item_details($order_id){

    }

    /*----------------------------------------------------------------------------------*/

    public static function get_additional_products($extra_params)
    {
    	$Db = Database::getInstance();
        $sub_productsArr = array();
        $sub_products_idsArr = array();
        $extra_paramsArr = unserialize($extra_params);

        foreach ($extra_paramsArr as $product_id => $sub_productsArr) {
            foreach ($sub_productsArr as $sub_product_id) {
                $sub_products_idsArr[] = $sub_product_id;
            }
        }
        $sub_products_ids = implode(',', $sub_products_idsArr);

        if (count($sub_products_idsArr)) {
            $sql = "
                SELECT Main.*, Lang.*, Product.`name` AS `product_name` FROM `tb_opportunity__sub_products` AS Main
                  LEFT JOIN `tb_opportunity__sub_products_lang` AS Lang ON Lang.`obj_id` = Main.`id`
                  LEFT JOIN `tb_opportunity__products_lang` AS Product ON Product.`obj_id` = Main.`product_id`
                WHERE Main.`id` IN ({$sub_products_ids}) AND Lang.`lang_id` = '{$_SESSION['lang_id']}' AND Product.`lang_id` = '{$_SESSION['lang_id']}'
            ";
            $result = $Db->query($sql);
            while ($row = $Db->get_stream($result)) {
                $sub_productsArr[] = $row;
            }
        }

        return $sub_productsArr;
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_additional_products_text($extra_params)
    {
        $productsArr = self::get_additional_products($extra_params);
        $additional_products_stringArr = array();

        foreach ($productsArr as $productArr) {
            if (!is_array($productArr) || empty($productArr)) {
                continue;
            }
            $additional_products_stringArr[] = $productArr['product_name'] . ': ' . $productArr['name'] . '.';
        }

        return implode(' ', $additional_products_stringArr);
    }

    /*----------------------------------------------------------------------------------*/

    public static function order_paid($order_id) {

    }

    /*----------------------------------------------------------------------------------*/

}

?>