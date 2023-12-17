<?php
/**
 * Created by PhpStorm.
 * User: assaf
 * Date: 8/12/2019
 * Time: 02:08 PM
 */

class UsersTypesEnum
{
    // all values must be in bitwise spectrum
    const SHOW_TOASTS = 1;
    const SHOW_CALLS_LOG = 2;
    const ENVIRONMENTS_ACCESS = 4;

    public static $typesTranslationsArr = array(
        UsersTypesEnum::SHOW_TOASTS => "הצג toasts (inmanage only)",
        UsersTypesEnum::SHOW_CALLS_LOG => "הצג לוג קריאות (inmanage only)",
        UsersTypesEnum::ENVIRONMENTS_ACCESS => "משתמש בעל גישה לסביבת בדיקות(מובייל בלבד)",
    );
}