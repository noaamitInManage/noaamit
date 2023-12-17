<?php
/**
 * Created by PhpStorm.
 * User: ilan
 * Date: 12-Sep-21
 * Time: 4:08 PM
 */

class registerEnum
{
    const MANUAL = 1;
    const FACEBOOK = 2;
    const GOOGLE = 3;
    const APPLE = 4;

    const TRANSLATE_ENUM_TO_PLATFORM_NAME = [
        self::MANUAL => 'Email',
        self::FACEBOOK => 'Facebook',
        self::GOOGLE => 'Google',
        self::APPLE => 'Apple'
    ];

    const ALLOWED_TYPES = [self::MANUAL, self::FACEBOOK, self::GOOGLE, self::APPLE];
    const MUST_PHONE_NUMBER = [self::MANUAL];
    const CAN_EDIT_EMAIL_ADDRESS = [self::FACEBOOK, self::MANUAL];
    const SOCIAL_LOGIN_TYPES = [self::FACEBOOK, self::GOOGLE, self::APPLE];

    const REQUIRED_FIELDS_FOR_REGISTRATION = [
        self::MANUAL => ['email', 'firstName', 'lastName', 'password','phoneNumber' ,'phoneNumberPrefix'],
        self::FACEBOOK => ['firstName', 'lastName','email'],
        self::GOOGLE => ['email','firstName', 'lastName'],
        self::APPLE => ['email', 'firstName', 'lastName'],
    ];

    const REGISTER_TYPE_TO_REQUEST_FIELD = [
        self::FACEBOOK => 'fbId',
        self::GOOGLE => 'googleId',
        self::APPLE => 'appleId',
    ];

    const REGISTER_TYPE_TO_DB_COLUMN = [
        self::FACEBOOK => 'fb_id',
        self::GOOGLE => 'google_id',
        self::APPLE => 'apple_id',
    ];
}
