<?php
include($_SERVER['DOCUMENT_ROOT'] . '/index.class.include.php');
$act = ($_REQUEST['action']) ? strtolower(trim($_REQUEST['action'])) : strtolower(trim($_REQUEST['act']));
$site_id = intval($_REQUEST['site_id']);
$answer = array("err" => "", "msg" => "", "status" => "", "html" => "");
$ts = time();

switch ($act) {
    case 'filter_meeting_rooms_bookings':
        $user_id = isset($_REQUEST['user_id']) && $_REQUEST['user_id'] ? siteFunctions::safe_value($_REQUEST['user_id'], 'number') : 0;
        $month = isset($_REQUEST['month']) && $_REQUEST['month'] ? siteFunctions::safe_value($_REQUEST['month'], 'number') : 0;
        $year = isset($_REQUEST['year']) && $_REQUEST['year'] ? siteFunctions::safe_value($_REQUEST['year'], 'number') : 0;

        $answer['html'] = draw_user_meeting_rooms_bookings($user_id, false, $month, $year);

        break;

    case 'export_meeting_rooms_bookings':
        ini_set('memory_limit','1024M');
        set_time_limit(600);

        $user_id = isset($_REQUEST['user_id']) && $_REQUEST['user_id'] ? siteFunctions::safe_value($_REQUEST['user_id'], 'number') : 0;
        $month = isset($_REQUEST['month']) && $_REQUEST['month'] ? siteFunctions::safe_value($_REQUEST['month'], 'number') : 0;
        $year = isset($_REQUEST['year']) && $_REQUEST['year'] ? siteFunctions::safe_value($_REQUEST['year'], 'number') : 0;

        $User = User::get_user($user_id);
        $bookingsArr = $User->get_meeting_rooms_booking_history_by_month($month, $year);

        $csv_titlesArr = array(
            'Order ID',
            'Meeting Room',
            'Title',
            'Date',
            'From',
            'To',
            'Cost',
            'Use Credit',
            'Booked By Admin',
            'Booking Time',
            'Status',
        );

        $csvArr = array(
            $csv_titlesArr
        );
        foreach ($bookingsArr as $bookingArr) {
            $MeetingRoom = new meetingRoomsManager($bookingArr['meeting_room_id']);
            $csvArr[] = array(
                $bookingArr['id'],
                $MeetingRoom->title,
                $bookingArr['title'],
                $bookingArr['dateArr']['date'],
                $bookingArr['dateArr']['start_hour'],
                $bookingArr['dateArr']['end_hour'],
                $bookingArr['cost'],
                ($bookingArr['use_credits'] ? "Yes" : "No"),
                ($bookingArr['by_admin'] ? "Yes" : "No"),
                $bookingArr['dateArr']['booking_time'],
                $bookingArr['status'],
            );
        }

        $answer['html'] = array_to_csv($csvArr, "Meeting_Room_Bookings_Report_" . time());

        break;
}

echo json_encode($answer);