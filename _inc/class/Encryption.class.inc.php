<?

/**
 * @author : Itzhak Hersccovici
 * @desc : encryption and decryption functions
 * @var : 1.0
 * @last_update :  21/07/2022
 * @example : $Encryption = new Encryption();
 *
 */
class Encryption extends BaseManager
{

    public const passphrase = "salathjkvae!inm";
    public const GCM_ALGORITHM_NONCE_SIZE = 12;
    public const GCM_ALGORITHM_TAG_SIZE = 16;
    public const GCM_ALGORITHM_KEY_SIZE = 16;
    public const GCM_PBKDF2_NAME = "sha256";
    public const GCM_PBKDF2_SALT_SIZE = 16;
    public const GCM_PBKDF2_ITERATIONS = 32767;

    private static $algorithm_name = "";

    public function __construct()
    {
        parent::__construct(false);
    }


    //-------------------------------------------------------------------------------------------------------------------//

    function __destruct()
    {

    }

    //-------------------------------------------------------------------------------------------------------------------//

    public function __set($var, $val)
    {
        $this->$var = $val;
    }


    //-------------------------------------------------------------------------------------------------------------------//

    public function __get($var)
    {
        return $this->$var;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function decrypt($value, $algorithm_name = "aes-128-gcm")
    {
        self::$algorithm_name = $algorithm_name;
        switch (self::$algorithm_name) {
            case 'aes_128_old':
                return self::decrypt_aes_128_value($value);
            default:
                return self::decryptStringAES128GCM($value);
        }
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function encrypt($value, $algorithm_name = "aes-128-gcm")
    {
        self::$algorithm_name = $algorithm_name;
        switch ($algorithm_name) {
            case 'aes_128_old':
                return self::encrypt_aes_128_value($value);
            default:
                return self::encryptStringAES128GCM($value);
        }
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function decrypt_aes_128_value($value)
    {
        $decrypted = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, self::passphrase, base64_decode($value), MCRYPT_MODE_ECB);
        $dec_s2 = strlen($decrypted);
        $padding = ord($decrypted[$dec_s2 - 1]);
        $decrypted = substr($decrypted, 0, -$padding);
        return $decrypted;
    }

    //-------------------------------------------------------------------------------------------------------------------//

    public static function encrypt_aes_128_value($dataString, $iv = null, $returnBase64Encoded = true)
    {

        // ensure source file exist
        if (!$dataString || empty($dataString))
            return null;

        try {

            // ===========
            // Ciphering
            $ciphered_data = null;

            //Make sure padding is pkcs7 based
            self::pkcs7Pad($dataString);

            //Encrypt data with AES
            $ciphered_data = @mcrypt_encrypt(MCRYPT_RIJNDAEL_128, self::passphrase, $dataString, MCRYPT_MODE_ECB, $iv);

            return ($returnBase64Encoded ? base64_encode($ciphered_data) : $ciphered_data);

        } catch (Exception $ex) {
            return null;
        }
    }

    //-------------------------------------------------------------------------------------------------------------------//

    /**
     * Pads the data using PKCS7 padding scheme, as described in RFC 5652.
     *
     * We do not want to rely on Mcrypt's zero-padding, because it differs from
     * OpenSSL's PKCS7 padding.
     *
     * Note: $data is passed by reference.
     *
     * @param string &$data
     */
    public static function pkcs7Pad(&$data)
    {
        $blockSize = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_ECB);
        $padding = $blockSize - (strlen($data) % $blockSize);

        $data .= str_repeat(chr($padding), $padding);
    }

    /**-----------------------------------------------------------------------------------------------------------------**/
    // AES 128 GCM    // AES 128 GCM    // AES 128 GCM    // AES 128 GCM    // AES 128 GCM    // AES 128 GCM    // AES 128 GCM
    /**-----------------------------------------------------------------------------------------------------------------**/

    public static function encryptStringAES128GCM($plaintext) {
        // Generate a 128-bit salt using a CSPRNG.
        $salt = random_bytes(self::GCM_PBKDF2_SALT_SIZE);

        // Derive a key.
        $key = hash_pbkdf2(self::GCM_PBKDF2_NAME, self::passphrase, $salt, self::GCM_PBKDF2_ITERATIONS, self::GCM_ALGORITHM_KEY_SIZE, true);

        // Encrypt and prepend salt and return as base64 string.
        return base64_encode($salt . self::encryptAES128GCM($plaintext, $key));
    }

    public static function decryptStringAES128GCM($base64CiphertextAndNonceAndSalt)
    {
        $ciphertextAndNonceAndSalt = base64_decode($base64CiphertextAndNonceAndSalt);

        // Retrieve the salt and ciphertextAndNonce.
        $salt = substr($ciphertextAndNonceAndSalt, 0, self::GCM_PBKDF2_SALT_SIZE);
        $ciphertextAndNonce = substr($ciphertextAndNonceAndSalt,  self::GCM_PBKDF2_SALT_SIZE);

        // Derive the key.
        $key = hash_pbkdf2(self::GCM_PBKDF2_NAME, self::passphrase, $salt, self::GCM_PBKDF2_ITERATIONS, self::GCM_ALGORITHM_KEY_SIZE, true);

        // Decrypt and return result.
        return self::decryptAES128GCM($ciphertextAndNonce, $key);
    }

    public static function encryptAES128GCM($plaintext, $key) {
        // Generate a 96-bit nonce using a CSPRNG.
        $nonce = random_bytes(self::GCM_ALGORITHM_NONCE_SIZE);

        // Encrypt and prepend nonce.
        $ciphertext = openssl_encrypt($plaintext, self::$algorithm_name, $key, OPENSSL_RAW_DATA, $nonce, $tag);

        return $nonce . $ciphertext . $tag;
    }


    public static function decryptAES128GCM($ciphertextAndNonce, $key) {
        // Retrieve the nonce and ciphertext.
        $nonce = substr($ciphertextAndNonce, 0, self::GCM_ALGORITHM_NONCE_SIZE);
        $ciphertext = substr($ciphertextAndNonce, self::GCM_ALGORITHM_NONCE_SIZE, strlen($ciphertextAndNonce) - self::GCM_ALGORITHM_NONCE_SIZE - self::GCM_ALGORITHM_TAG_SIZE);
        $tag = substr($ciphertextAndNonce, strlen($ciphertextAndNonce) - self::GCM_ALGORITHM_TAG_SIZE);

        // Decrypt and return result.
        return openssl_decrypt($ciphertext, self::$algorithm_name, $key, OPENSSL_RAW_DATA, $nonce, $tag);
    }

    /**-----------------------------------------------------------------------------------------------------------------**/
    // AES 128 GCM    // AES 128 GCM    // AES 128 GCM    // AES 128 GCM    // AES 128 GCM    // AES 128 GCM    // AES 128 GCM
    /**-----------------------------------------------------------------------------------------------------------------**/
}

?>
