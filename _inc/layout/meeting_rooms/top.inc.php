<div class="nav">
    <div class="js-user_panel avatar">
        <img src="<?= $User->get_user_picture()['picture'] ?>" class="avatar-icon"/>
        <span class="js-username"><?= $User->get_full_name() ?></span>
        <div class="user-panel">
            <div class="credits">Current Credits (Hours) <span class="js-header-credits"><?= meetingRoomsManager::convert_to_credit(meetingRoomsManager::get_company_available_credits($User->company_id)) ?> / <?= meetingRoomsManager::convert_to_credit($Company->get_monthly_meeting_room_credit()) ?></span></div>
            <a href="javascript:;" class="js-logout">Log Out</a>
        </div>
    </div>
    <div class="logo">
        <img src="/_media/images/booking/salat_logo.svg"/>
    </div>
</div>