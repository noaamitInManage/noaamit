<?php

use Carbon\Carbon;

$act = ($_REQUEST['action']) ? strtolower(trim($_REQUEST['action'])) : strtolower(trim($_REQUEST['act']));
$responseArr = array("status" => 1, "data" => array(), "err" => "", "msg" => "", "relocation" => "", "html" => "");

switch ($act) {

    case 'get_day':
        $site_id = ($_REQUEST["siteId"] && isset($_REQUEST["siteId"])) ? siteFunctions::safe_value($_REQUEST["siteId"], "number") : 0;
        $floor = (isset($_REQUEST["floor"])) ? siteFunctions::safe_value($_REQUEST["floor"], "text") : "all";
        $date = ($_REQUEST["date"] && isset($_REQUEST["date"])) ? siteFunctions::safe_value($_REQUEST["date"], "text") : 0;

        $User = User::getInstance();
        if (!$User->id) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(150); // You have to be logged in to perform this action
            break;
        }

        if (!$User->meeting_rooms) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(300); // You are not allowed to book meeting rooms
            break;
        }

        if ($date == 0) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(152); // Required data missing
            break;
        }

        if (!$site_id) {
            $Company = new companiesManager($User->company_id);
            $site_id = $Company->site_id;
        }

        $Site = new sitesManager($site_id);
        if (!$Site->id) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(260); // Site doesn't exist
            break;
        }

        $dayArr = meetingRoomsManager::get_day($date, $site_id, $floor);
        if (is_numeric($dayArr)) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message($dayArr);
            break;
        }

        $responseArr['data'] = $dayArr;

        break;

    case 'book':
        $room_id = ($_REQUEST["meetingRoomId"] && isset($_REQUEST["meetingRoomId"])) ? siteFunctions::safe_value($_REQUEST["meetingRoomId"], "number") : 0;
        $title = ($_REQUEST["title"] && isset($_REQUEST["title"])) ? siteFunctions::safe_value($_REQUEST["title"], "text") : "";
        $date_ts = ($_REQUEST["dateTs"] && isset($_REQUEST["dateTs"])) ? siteFunctions::safe_value($_REQUEST["dateTs"], "number") : 0;
        $start = ($_REQUEST["start"] && isset($_REQUEST["start"])) ? siteFunctions::safe_value($_REQUEST["start"], "text") : "";
        $end = ($_REQUEST["end"] && isset($_REQUEST["end"])) ? siteFunctions::safe_value($_REQUEST["end"], "text") : "";
        $use_credit = ($_REQUEST["useCredit"] && isset($_REQUEST["useCredit"])) ? siteFunctions::safe_value($_REQUEST["useCredit"], "number_flag") : null;

        $User = User::getInstance();
        if (!$User->id) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(150); // You have to be logged in to perform this action
            break;
        }

        if (!$User->meeting_rooms) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(300); // You are not allowed to book meeting rooms
            break;
        }

        if ($room_id == 0 || $title == "" || $date_ts == 0 || $start == "" || $end == "") {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(152); // Required data missing
            break;
        }

        $Room = new meetingRoomsManager($room_id);
        if (!$Room->id) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(301); // Room doesn't exist
            break;
        }

        $Company = new companiesManager($User->company_id);
        if (!$Company->id) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(230); // Company doesn't exist
            break;
        }

        $by_admin = $User->app_admin ? 1 : 0;
        $use_credit = !$User->app_admin ? 0 : ($use_credit === null ? ($by_admin ? 0 : 1) : $use_credit);

        $result = $Room->book($Company->id, $User->id, $date_ts, $start, $end, $title, $use_credit);
        if ($result !== true) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message($result);
            break;
        }

        $date = Carbon::createFromTimestamp($date_ts, $Room->get_timezone())->format('d/m/Y');
        $floor = $Room->floor;

        $dayArr = meetingRoomsManager::get_day($date, $Room->site_id, $floor);
        if (is_numeric($dayArr)) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message($dayArr);
            break;
        }

        $responseArr['data'] = $dayArr;
        $responseArr['credit'] = meetingRoomsManager::get_company_available_credits($Company);

        break;

    case 'update_booking':
        $meeting_id = ($_REQUEST["meetingId"] && isset($_REQUEST["meetingId"])) ? siteFunctions::safe_value($_REQUEST["meetingId"], "number") : 0;
        $room_id = ($_REQUEST["meetingRoomId"] && isset($_REQUEST["meetingRoomId"])) ? siteFunctions::safe_value($_REQUEST["meetingRoomId"], "number") : 0;
        $title = ($_REQUEST["title"] && isset($_REQUEST["title"])) ? siteFunctions::safe_value($_REQUEST["title"], "text") : "";
        $date_ts = ($_REQUEST["dateTs"] && isset($_REQUEST["dateTs"])) ? siteFunctions::safe_value($_REQUEST["dateTs"], "number") : 0;
        $start = ($_REQUEST["start"] && isset($_REQUEST["start"])) ? siteFunctions::safe_value($_REQUEST["start"], "text") : "";
        $end = ($_REQUEST["end"] && isset($_REQUEST["end"])) ? siteFunctions::safe_value($_REQUEST["end"], "text") : "";
        $use_credit = ($_REQUEST["useCredit"] && isset($_REQUEST["useCredit"])) ? siteFunctions::safe_value($_REQUEST["useCredit"], "number_flag") : null;

        $User = User::getInstance();
        if (!$User->id) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(150); // You have to be logged in to perform this action
            break;
        }

        if (!$User->meeting_rooms) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(300); // You are not allowed to book meeting rooms
            break;
        }

        if ($room_id == 0 || $title == "" || $date_ts == 0 || $start == "" || $end == "") {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(152); // Required data missing
            break;
        }

        $Room = new meetingRoomsManager($room_id);
        if (!$Room->id) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(301); // Room doesn't exist
            break;
        }

        $Booking = new meetingRoomBookingManager($meeting_id);
        if (!$Booking->id) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(310); // Can't update a meeting that doesn't exist yet
            break;
        }

        $Company = new companiesManager($User->company_id);
        if (!$Company->id) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(230); // Company doesn't exist
            break;
        }

        $use_credit = !$User->app_admin ? 0 : ($use_credit === null ? ($by_admin ? 0 : 1) : $use_credit);

        // Determine the timezone
        $timezone = $Room->get_timezone();

        // Convert times
        $start_ts = meetingRoomsManager::convert_to_timestamp($date_ts, $start, $timezone);
        $end_ts = meetingRoomsManager::convert_to_timestamp($date_ts, $end, $timezone);

        $result = $Booking->set_room($Room->id)
            ->set_title($title)
            ->set_start_ts($start_ts)
            ->set_end_ts($end_ts)
            ->set_use_credit($use_credit)
            ->update();
        if ($result !== true) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message($result);
            break;
        }

        $date = Carbon::createFromTimestamp($date_ts, $Room->get_timezone())->format('d/m/Y');
        $floor = $Room->floor;

        $dayArr = meetingRoomsManager::get_day($date, $Room->site_id, $floor);
        if (is_numeric($dayArr)) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message($dayArr);
            break;
        }

        $responseArr['data'] = $dayArr;
        $responseArr['credit'] = meetingRoomsManager::get_company_available_credits($Company);

        break;

    case 'cancel_booking':
        $meeting_id = ($_REQUEST["meetingId"] && isset($_REQUEST["meetingId"])) ? siteFunctions::safe_value($_REQUEST["meetingId"], "number") : 0;

        $User = User::getInstance();
        if (!$User->id) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(150); // You have to be logged in to perform this action
            break;
        }

        if (!$User->meeting_rooms) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(300); // You are not allowed to book meeting rooms
            break;
        }

        if ($meeting_id == 0) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(152); // Required data missing
            break;
        }

        $Booking = new meetingRoomBookingManager($meeting_id);
        if (!$Booking->id) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message(310); // Can't update a meeting that doesn't exist yet
            break;
        }
        $bookingArr = $Booking->toArray();

        $result = $Booking->cancel();
        if ($result !== true) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message($result);
            break;
        }

        $Company = new companiesManager($User->company_id);
        $date = Carbon::createFromTimestamp($Booking->start_ts, $bookingArr['timezone'])->format('d/m/Y');
        $floor = $Room->floor;

        $dayArr = meetingRoomsManager::get_day($date, $Room->site_id, $floor);
        if (is_numeric($dayArr)) {
            $responseArr['status'] = 0;
            $responseArr['err'] = errorManager::get_message($dayArr);
            break;
        }

        $responseArr['data'] = $dayArr;
        $responseArr['credit'] = meetingRoomsManager::get_company_available_credits($Company);

        break;

    default:

        break;
}
exit(json_encode($responseArr));