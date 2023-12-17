<?php
/**
 * Created by PhpStorm.
 * User: Shavit
 * Date: 7/27/2020
 * Time: 5:27 PM
 */

include_once ($_SERVER["DOCUMENT_ROOT"]."/_inc/class/ASDecoder.php");
include_once ($_SERVER["DOCUMENT_ROOT"]."/_inc/class/ASPayload.php");
include_once ($_SERVER["DOCUMENT_ROOT"]."/_inc/class/JWK.php");
include_once ($_SERVER["DOCUMENT_ROOT"]."/_inc/class/JWT.php");

class AppleSignInManager
{
    public static function get_apple_payload($apple_id, $identity_token, $apple_api_response_fieldsArr = array()) {
        try {
            $appleSignInPayload = ASDecoder::getAppleSignInPayload($identity_token);

            //Obtain the Sign In with Apple email and user creds.
            $responseArr['email'] = $appleSignInPayload->getEmail();
            $responseArr['client_user'] = $appleSignInPayload->getUser();

            if(!$responseArr['email'] || !$responseArr['client_user']) {
                throw new Exception('Error obtaining user information.');
            }

            //Determine whether the client-provided user is valid.
            if(!$appleSignInPayload->verifyUser($apple_id)) {
                throw new Exception('Invalid user.');
            }

            if(($apple_api_response_fieldsArr["email"]) && ($apple_api_response_fieldsArr["client_user"] != $apple_id)) {
                throw new Exception('Invalid user.');
            }
        } catch (Exception $e) {
            $responseArr = is_int($e->getMessage()) ? $e->getMessage() : 4007; // 4007 - קרתה תקלה במהלך ההתחברות ל-Apple. נסה להתחבר מחדש
        }

        return $responseArr;
    }

    public static function get_apple_user($apple_detailsArr) {
        $Db = Database::getInstance();

        $apple_user_mail = $Db->make_escape($apple_detailsArr["email"]);
        $check_query = "SELECT id, apple_id FROM tb_users WHERE `email` = '{$apple_user_mail}' AND `apple_id` > 0";
        $resource = $Db->query($check_query);
        $userArr = $Db->get_stream($resource);

        if($userArr["id"] && $userArr["apple_id"] != $apple_detailsArr['client_user']){
            $query = "UPDATE tb_users set `apple_id` ='{$apple_detailsArr['client_user']}' where `id` = {$userArr["id"]}"; //old_apid ?
            $Db->query($query);
        }
    }
    public function update_user_id ($user_id)
    {
        return (Database::getInstance())->update(self::TB_TOKEN_NAME,['last_update'=>time(),'user_id'=>$user_id],'apple_id','=',$this->apple_id);
    }
}