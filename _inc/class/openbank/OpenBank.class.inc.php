<?php

use GuzzleHttp\Client;

class OpenBank extends BaseManager
{
    private static $instance;

    private $base_uri = 'https://apisandbox.openbankproject.com';
    private $api_version = 'v3.0.0';
    
    private $consumer_key = 'l1ohrrztsvhsvqbmwlwy53n2ge324ila33aycrp0';
    private $username = 'inmanage';
    private $password = 'Asdkjk2daa@';

    private $access_token = null;

    private $configArr = array(
        'base_uri' => '',
        'timeout' => 2.0,
    );

    /**
     * @var Client
     */
    private $client = null;

    /*----------------------------------------------------------------------------------*/
    /**
     * OpenBank constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->access_token = $this->get_access_token();

        $this->client = new Client(array(
            'base_uri' => $this->base_uri,
            'timeout' => 2.0,
            'headers' => array(
                'Content-Type' => 'application/json',
                'Authorization' => 'DirectLogin token="' . $this->access_token .'"',
            ),
        ));
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name getInstance
     * @description Retrieves the instance of this class
     * @return OpenBank|null
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_base_uri
     * @description Returns base url
     * @return string
     */
    public function get_base_uri()
    {
        return $this->base_uri . '/obp/' . $this->api_version;
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name get_access_token
     * @description
     * @return int|string
     */
    public function get_access_token()
    {
        $Client = new Client(array(
            'base_uri' => $this->base_uri,
            'timeout' => 2.0,
        ));

        try {
            $Response = $Client->post('/my/logins/direct', array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                    'Authorization' => 'DirectLogin username="'. $this->username .'", password="'. $this->password .'", consumer_key="'. $this->consumer_key .'"',
                ),
            ));
        } catch (Exception $e) {
            return 0;
        }

        $status_code = $Response->getStatusCode();
        $response_bodyArr = json_decode($Response->getBody()->getContents(), true);
        if (!($status_code >= 200 && $status_code <= 299) || !isset($response_bodyArr['token'])) {
            return 0;
        }

        return $response_bodyArr['token'];
    }

    /*----------------------------------------------------------------------------------*/
    /**
     * @name request
     * @description Sends a request
     * @param $method
     * @param $action
     * @param array $bodyArr
     * @return array
     */
    private function request($method, $action, $bodyArr = array())
    {
        try {
            $Response = $this->client->request($method, 'obp/' . $this->api_version . '/' . $action, array('json' => $bodyArr));
        } catch (Exception $e) {
            return $this->response(0, 600); // Events Service Error
        }

        $status_code = $Response->getStatusCode();
        $response_bodyArr = json_decode($Response->getBody()->getContents(), true);
        if (!($status_code >= 200 && $status_code <= 299)) {
            return $this->response(0, 600); // Events Service Error
        }
        return $this->response(1, $response_bodyArr);
    }
    /*----------------------------------------------------------------------------------*/
    /**
     * @name response
     * @description returns a response for the request method
     * @param $status
     * @param $data
     * @return array
     */
    private function response($status, $data)
    {
        $err = array();
        if (!$status) {
            $err = errorManager::get_error($data);
            $data = array();
        }
        return array(
            'status' => $status,
            'data' => $data,
            'err' => $err,
        );
    }

    /*----------------------------------------------------------------------------------*/

    public function get_transactions($account_id)
    {
        $transactionsArr = $this->request('get', 'my/banks/rbs/accounts/' . $account_id . '/transactions');
        die('<hr /><pre>' . print_r(array($transactionsArr, '<br />Here: ' . __LINE__ . ' at ' . __FILE__), true) . '</pre><hr />');
    }

    /*----------------------------------------------------------------------------------*/
}