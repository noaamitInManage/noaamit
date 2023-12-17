<?php

function draw_meeting_rooms_login_get_sms_token_form($number = '', $prefix = '')
{
    $countriesArr = collect(countriesManager::get_gdArr())->sortBy('order_num');

    $prefixes_html = '';
    $selected_prefixArr = array();
    foreach ($countriesArr as $countryArr) {
        $selected_prefixArr = ($countryArr['phone_prefix'] == $prefix) ? array('prefix' => $countryArr['phone_prefix'], 'country_code' => $countryArr['country_code']) : array();
        $prefixes_html .= '
            <li data-value="' . $countryArr['phone_prefix'] . '">' . $countryArr['phone_prefix'] . '</li>
        ';
    }
    if (empty($selected_prefixArr)) {
        $prefixArr = $countriesArr->where('selected', 1)->first();
        $selected_prefixArr = array(
            'prefix' => $prefixArr['phone_prefix'],
            'country_code' => $prefixArr['country_code'],
        );
    }

    $html = '
        <p class="summary">' . lang('meeting_rooms_login_intro') . '</p>
        <div class="form">
            <div class="group-form">
                <div class="select">
                    <button class="selector js-phone-prefix" title="Country Code" data-country-code="' . $selected_prefixArr['country_code'] . '">' . $selected_prefixArr['prefix'] . '</button>
                    <ul>
                        ' . $prefixes_html . '
                    </ul>
                    <input type="hidden" name="country_code" value="">
                </div>
                <input type="number" name="phone" title="Phone Number" class="js-phone-number" autocomplete="off" value="' . $number . '" />
            </div>
            <p class="error-notification medium-carmine"></p>
            <button name="submit" class="send js-login-submit-button">
                <div class="small">
                    <div></div>
                </div>
                <span class="js-login-button-text">' . lang('meeting_rooms_login_button_text') . '</span>                
            </button>
        </div>
        <p class="issues">' . lang('meeting_rooms_login_issues_link') . '</p>
        <a href="mailto:' . param('contact_email') . '" class="contact muddy-waters">' . lang('meeting_rooms_login_contact_us') . '</a>
    ';

    return $html;
}

function draw_meeting_rooms_sms_login_form($number)
{
    $html = '
        <div class="summary">
            ' . lang('meeting_rooms_login_sent_title') . '
            <p>
                <span class="black bold">' . $number . '</span>
                <a href="javascript:;" class="medium-carmine bold js-edit-cellphone" data-cellphone="' . $number . '">' . lang('meeting_rooms_login_edit_number_btn_text') . '</a>
            </p>
        </div>
        <div class="code">
            <h2>Enter the Code</h2>
            <input type="number" name="first" title="First number" class="js-login-token-input" maxlength="1"/>
            <input type="number" name="second" title="Second number" class="js-login-token-input" maxlength="1"/>
            <input type="number" name="third" title="Third number" class="js-login-token-input" maxlength="1"/>
            <input type="number" name="fourth" title="Fourth number" class="js-login-token-input" maxlength="1"/>            
            <p class="notification">Sending might take 10 seconds...</p>
            <p class="error-notification"></p>
        </div>
        <p class="issues">' . lang('meeting_rooms_login_didnt_receive_title') . '</p>
        <span><a href="javascript:;" class="contact muddy-waters bold js-resend-login-token" data-cellphone="'. $number .'">' . lang('meeting_rooms_login_resend_btn_txt') . '</a> ' . lang('or') . ' <a href="mailto:' . param('contact_email') . '" class="contact muddy-waters">' . lang('meeting_rooms_login_contact_us') . '</a></span>
    ';

    return $html;
}

function draw_meeting_rooms_login_bottom_links()
{
    $phrase = lang('meeting_rooms_login_terms_of_use_links');
    $phrase = str_replace(['{Terms of Service}', '{Privacy Policy}'], ['<a href="' . param('terms_of_service_url') . '" target="_blank">Terms of Service</a>', '<a href="' . param('privacy_policy_url') . '" target="_blank">Privacy Policy</a>'], $phrase);

    return $phrase;
}