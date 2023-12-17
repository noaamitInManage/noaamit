<?php
/**
 * Created by JetBrains PhpStorm.
 * User: gal
 * Date: 28/04/14
 * Time: 19:52
 *
 */

/**
 * @author : gal zalait
 * @desc :
 * @var : 1.0
 * @last_update :
 */
class errorManager extends BaseManager
{

    function __construct()
    {
        parent::__construct(false);
    }

    /*----------------------------------------------------------------------------------*/

    function __destruct()
    {

    }

    /*----------------------------------------------------------------------------------*/

    public function __set($var, $val)
    {
        $this->$var = $val;
    }

    /*----------------------------------------------------------------------------------*/

    public function __get($var)
    {
        return $this->$var;
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_error($error_code)
    {
        global $errorsArr;
        if (!isset($errorsArr)) {
            include($_SERVER['DOCUMENT_ROOT'] . '/_static/errors.' . $_SESSION['lang'] . '.inc.php');//$errorsArr
        }
        $def_error = 'אין נתונים';
        $error = ($errorsArr[$error_code]['content']) ? $errorsArr[$error_code]['content'] : $def_error;
        return array(
            "id" => $error_code,
            "content" => $errorsArr[$error_code]['content']
        );
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_cg_error($error_code)
    {
        global $cg_errorsArr, $throw_to_showLoginDialogArr;

        if (!isset($cg_errorsArr)) {
            include($_SERVER['DOCUMENT_ROOT'] . '/_static/cg_errors.' . $_SESSION['lang'] . '.inc.php');//$cg_errorsArr
        }

        if ($cg_errorsArr[$error_code]['content']) {
            $answerArr = array(
                "id" => $error_code,
                "content" => $cg_errorsArr[$error_code]['content'],
                "hide_dialog" => 0,
            );
        } else {
            $answerArr = self::get_error(500);
        }

        return $answerArr;
    }

    /*----------------------------------------------------------------------------------*/

    public static function get_message($error_code)
    {
        global $errorsArr;
        if (!isset($errorsArr)) {
            include($_SERVER['DOCUMENT_ROOT'] . '/_static/errors.' . $_SERVER['lang'] . '.inc.php');//$errorsArr
        }
        $def_error = 'אין נתונים';
        $error = ($errorsArr[$error_code]['content']) ? $errorsArr[$error_code]['content'] : $def_error;
        //return "$error_code. {$errorsArr[$error_code]['content']}";

        return $errorsArr[$error_code]['content'];
    }
}

?>