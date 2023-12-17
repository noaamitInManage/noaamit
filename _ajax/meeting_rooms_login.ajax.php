<?php

$act = ($_REQUEST['action']) ? strtolower(trim($_REQUEST['action'])) : strtolower(trim($_REQUEST['act']));
$responseArr = array("status" => 1, "err" => "", "msg" => "", "relocation" => "", "html" => "");

switch ($act) {

    case 'get_sms_token':
        $phone_prefix = (isset($_REQUEST['phone_prefix']) && $_REQUEST['phone_prefix']) ? siteFunctions::safe_value($_REQUEST['phone_prefix'], 'text') : '';
        $phone_number = (isset($_REQUEST['phone_number']) && $_REQUEST['phone_number']) ? siteFunctions::safe_value($_REQUEST['phone_number'], 'text') : '';
        $country_code = (isset($_REQUEST['country_code']) && $_REQUEST['country_code']) ? siteFunctions::safe_value($_REQUEST['country_code'], 'text') : '';

        if ($phone_prefix == '' || $phone_number == '') {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(152); // Required data missing
            break;
        }

        $cellphone = $phone_prefix . $phone_number;

        $result = SmsAuthManager::start_auth_process($cellphone, 1);
        if ($result !== true) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message($result);
            break;
        }

        $responseArr['html'] = draw_meeting_rooms_sms_login_form($cellphone, $country_code);

        break;

    case 'resend_sms_token':
        $cellphone = (isset($_REQUEST['cellphone']) && $_REQUEST['cellphone']) ? siteFunctions::safe_value($_REQUEST['cellphone'], 'text') : '';

        if ($cellphone == '') {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(152); // Required data missing
            break;
        }

        $result = SmsAuthManager::start_auth_process($cellphone, 1);
        if ($result !== true) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message($result);
            break;
        }

        break;

    case 'get_sms_token_form_html':
        $cellphone = (isset($_REQUEST['cellphone']) && $_REQUEST['cellphone']) ? siteFunctions::safe_value($_REQUEST['cellphone'], 'text') : '';
        $country_code = (isset($_REQUEST['country_code']) && $_REQUEST['country_code']) ? siteFunctions::safe_value($_REQUEST['country_code'], 'text') : '';

        $numberArr = PhoneNumberManager::get_numberArr($cellphone, $country_code);

        $responseArr['html'] = draw_meeting_rooms_login_get_sms_token_form($numberArr['number'], $numberArr['prefix']);

        break;

    case 'login':
        $cellphone = isset($_REQUEST['cellphone']) && $_REQUEST['cellphone'] ? siteFunctions::safe_value($_REQUEST['cellphone'], 'text') : '';
        $token = isset($_REQUEST['token']) && $_REQUEST['token'] ? siteFunctions::safe_value($_REQUEST['token'], 'number') : 0;
        $remember = isset($_REQUEST['remember']) && $_REQUEST['remember'] ? siteFunctions::safe_value($_REQUEST['remember'], 'number_flag') : 0;

        if ($cellphone == '' || $token == 0) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(152); // Required data missing
            break;
        }

        $responseArr = SmsAuthManager::check_token($token, $cellphone, 1);
        if (!$responseArr['status']) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message($responseArr['error']);
            break;
        } else {
            $user_id = $responseArr['user_id'];
        }

        $login_result = User::login_as_user($user_id, 'id', configManager::$login_platformsArr['website'], $remember);
        if (!$login_result['status']) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(171); // Invalid login details
            break;
        }

        $responseArr['relocation'] = '/' . $Seo->getStaticUrl(4, 3);

        break;

    default:

        break;
}
exit(json_encode($responseArr));