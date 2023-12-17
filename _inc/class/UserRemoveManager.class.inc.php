<?php

/**
 * @author  Or Shemesh
 * @desc  Generic user class
 * @var : 1.0
 * @last_update :  28/06/2022
 */

class UserRemoveManager extends User
{
    public function remove_user()
    {
        $db_fieldsArr = array('active' => 0);
        if ($this->email) {
            $shuffle_email = $this->shuffle_str($this->email);
            $db_fieldsArr['email'] = $shuffle_email;
        }
        if ($this->cellphone) {
            $shuffle_cell_phone = $this->shuffle_number($this->cellphone);
            $db_fieldsArr['cellphone'] = $shuffle_cell_phone;
        }
        if ($this->apple_id) {
            $shuffle_apple_id = $this->shuffle_str($this->apple_id);
            $db_fieldsArr['apple_id'] = $shuffle_apple_id;
        }

        if ($this->fbid) {
            $shuffle_facebook_id = $this->shuffle_str($this->fbid);
            $db_fieldsArr['fbid'] = $shuffle_facebook_id;
        }
        if ($this->first_name) {
            $db_fieldsArr['first_name'] = str_shuffle($this->first_name);
        }
        if ($this->last_name) {
            $db_fieldsArr['last_name'] = str_shuffle($this->last_name);
        }
        $Db = Database::getInstance();
        $Db->update('tb_users', $db_fieldsArr, 'id', '=', $this->id);

        if ($this->cellphone && $this->email) {
            //unsubscribe if exists
        }
        $Browser = new Browser();
        $db_fieldsArr = array(
            'user_id' => $this->id,
            'email' => Encryption::encrypt($this->email),
            'cellphone' => Encryption::encrypt($this->cellphone),
            'facebook_id' => Encryption::encrypt($this->fbid),
            'apple_id' => Encryption::encrypt($this->apple_id),
            "user_agent" => $Browser->getUserAgent(),
            "platform" => $Browser->getPlatform(),
            "version" => $Browser->getVersion(),
            'ip' => $_SERVER['REMOTE_ADDR'],
            'last_update' => time()
        );
        return $Db->insert('tb_users__remove__log', $db_fieldsArr);
    }

    /*----------------------------------------------------------------------------------*/

    private function shuffle_str($str): string
    {
        //example - email 'Ors@inmanage.net' => 'Ors@inmanage.net_1656424974'
        $now = time();
        return $str . $now;
    }

    /*----------------------------------------------------------------------------------*/

    private function shuffle_number($number): int
    {
        //example - cell phone '0547202175' and user_id = 717 => '7177202175'
        $len = strlen($number);
        $cut = strlen($number) - strlen($this->id);
        return (int)($this->id . substr($number, $len - $cut, $len));
    }
}