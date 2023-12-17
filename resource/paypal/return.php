<?php
/**
 * Created by PhpStorm.
 * User: Gal Zalait
 * Date: 6/5/14
 * Time: 12:18 PM
 *
 */

session_start();
set_time_limit(0);
include_once($_SERVER['DOCUMENT_ROOT']."/salat2/_inc/project.inc.php"); // load server, domain paths
include_once($_project_server_path.$_includes_path."dblayer.inc.php"); // load database connection
include_once($_project_server_path.$_includes_path."modules.array.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."html.functions.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."mobile_html.functions.inc.php");  // load module functions
include_once($_project_server_path.$_includes_path."modules.functions.inc.php");  // load module functions
include_once($_project_server_path.$_salat_path.$_includes_path."functions.inc.php"); // load various functions
include_once($_project_server_path.'/salat2/'.$_includes_path."metaindex.php"); // load meta functions
include_once($_project_server_path.$_includes_path."site.array.inc.php");  // load module functions   TODO: [CHANGE THE 'site' TO SITE NAME]
include_once($_SERVER["DOCUMENT_ROOT"] . "/_inc/autoloader.config.php");
include_once($_project_server_path."index.class.include.php");  // load class index

$status =(isset($_REQUEST['status']) && ($_REQUEST['status'])) ? strtolower(trim($_REQUEST['status'])) : '';
$payment_id = 2; // paypal

// In case the user lost it's session during the payment process, restore it from the db
if($_REQUEST['userData7']) {
    siteFunctions::load_order_session_from_db($_REQUEST['userData7']);
}

siteFunctions::write_payment_return_log($payment_id, $_SESSION['open_order_id'], $_REQUEST['userData7'], $_REQUEST['userData8'], $status);

$url = "https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
if(isset($_REQUEST['error']) && ($_REQUEST['error']=="token_failed")){
	die('...');
}

$Seo = new Seo();

$is_website_payment = false;
if (isset($_SESSION['payment_from_website']) && $_SESSION['payment_from_website']) {
    $is_website_payment = true;
}

/* ---- Sessions that set in do_payment ----- */
$order_id = $_REQUEST['userData7'] ? $_REQUEST['userData7'] : $_SESSION['open_order_id'];
$lock_inventory_idsArr = $_SESSION['cart']['lock_inventory_idsArr'];
$delivery_id = $_SESSION['cart']['delivery_id'];

switch($status){
	case 'success':
	case 'successwithwarning':
        $PaypalObj = new PaypalManager();
        if(isset($_REQUEST['token'])) {
            $PaypalObj->set_token($_REQUEST['token']);
        }

        $Cart = cartManager::getInstance();

		// check if transaction id is already register
		if(!is_transaction_is_unique($order_id)){
			siteFunctions::send_mail(configManager::$developersEmailsArr,"important - ERROR 505",print_r(array($token_row_id,$order_id,$_SESSION,$_REQUEST,$_SERVER,__FILE__,__LINE__,__CLASS__,__METHOD__,__FUNCTION__),true));
			header("location:" . siteFunctions::get_base_url(1) . '/resource/payment/ppl/failure/?json=' .json_encode(array("err"=>errorManager::get_error(500))));
			exit();
		}

        $authorizationId = $PaypalObj->get_express_checkout_details();

		$paymentsArr = orderManager::get_order_payments($order_id);
        if($authorizationId && $PaypalObj->userId){


			$db_fields=array(
				"order_id"=>$order_id,
				"authorization_id"=>$authorizationId,
				"token"=>$_REQUEST['token'],
                "token_id" => 0,
				"sum"=>$paymentsArr[2]['sum'],
				"last_update"=>time(),
			);

			siteFunctions::insert_to_db('tb_paypal__charge_queue',$db_fields);
			$successPaypal = $PaypalObj->send_order_and_do_capture($authorizationId,$order_id); // 17.5.2015
			if($successPaypal){ // OK
				if($paymentsArr[4]['sum']) {
					$Cart->use_groupon_money($order_id, $paymentsArr[4]['sum']);
				}
				if($lock_inventory_idsArr){
					orderManager::update_order_status($order_id, configManager::$order_statusArr['inventory_update']);
				}

				$Cart = cartManager::getInstance();

				$complete_order_status = $Cart->complete_order($order_id, $lock_inventory_idsArr, $delivery_id, $paymentsArr);

                if ($is_website_payment) {
	                $_SESSION['order_information'] = $Cart->get_success_payment_answer($order_id);
	                $_SESSION['order_information']['total_billed'] = $paymentsArr[2]['sum'];

                    $Seo = new Seo();
	                if ($complete_order_status !== true) {
		                $_SESSION['order_information']['order_status'] = $complete_order_status;
	                }

	                header("location: /" . $Seo->getStaticUrl(4, 60));
                } else {
	                if ($complete_order_status !== true) {
		                header("location: " . '/resource/payment/cg/failure/?json=' . json_encode(array("err" => errorManager::get_error($complete_order_status))) . '');
	                } else {
		                header("location:".siteFunctions::get_base_url(1).'/resource/payment/cg/success/?json='.json_encode($Cart->get_success_payment_answer($order_id),1)); //
	                }
                }
				exit();
			} else { // error
				header("location:".$url.'&error=capture_failed');
			}
			exit();
		} else {

	        $Cart = cartManager::getInstance();
	        $cartArr = $Cart->get_cart();
	        $itemArr = reset($cartArr['itemsArr']);

	        $cartItemsArr = array(
		        array(
			        'name' => $itemArr['title'],
			        'desc' => $itemArr['sub_title'],
			        'total_price' => $paymentsArr[2]['sum'],
		        )
	        );


	        $url = $PaypalObj->set_express_checkout($cartItemsArr,$_SESSION["lang_id"]);
			header("location:".$url.'&error=token_failed');
	        exit();
		}
		break;

	case 'error':
	default:
		header("location:" . siteFunctions::get_base_url(1) . '/resource/payment/ppl/failure/?json=' .json_encode(array("err"=>errorManager::get_error(500))));
		exit();
	break;

}

/*----------------------------------------------------------------------------------*/
function is_transaction_is_unique($order_id) {
	$Db= Database::getInstance();
	$query="
		SELECT * FROM `tb_paypal__orders` 
			WHERE `order_id` = {$order_id}
				ORDER BY `last_update`
					LIMIT 1
	";
	$result=$Db->query($query);
	if(($result->num_rows) && $order_id > 0){
		$rowArr=$Db->get_stream($result);
		$query = "
			SELECT * FROM `tb_paypal__orders` 
				WHERE 
					`transaction_id` LIKE '{$rowArr['transaction_id']}'
					OR `order_id` = '{$order_id}'
		";
		$result=$Db->query($query);
		if(($result->num_rows) > 1){
			return false;
		}
	}
	return true;

}
?>