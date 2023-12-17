<?php

class OpenBankOAuth extends BaseManager
{
    private $consumer_id = 3909;
    private $redirect_url = 'https://salat.inmanage.com/openbank/oauth.php';
    private $consumer_key = 'l1ohrrztsvhsvqbmwlwy53n2ge324ila33aycrp0';
    private $consumer_secret = 'cnfepazvgqspk32eebkz5bmts4zu5umasiu2z5mv';

    private $base_url = 'https://apisandbox.openbankproject.com';
    private $api_version = 'v1.2';

    private $debug = true;

    /**
     * @var OAuth
     */
    private $oauth;

    private $settingsArr = array();

    public function __construct()
    {
        parent::__construct();

        $this->settingsArr = array(
            'consumer' => array(
                'key' => $this->consumer_key,
                'secret' => $this->consumer_secret,
            ),
            'url' => array(
                'token' => array(
                    'request' => $this->base_url . '/oauth/initiate',
                    'access' => $this->base_url . '/oauth/token',
                ),
                'auth' => $this->base_url . '/oauth/authorize',
                'api' => $this->base_url . '/oauth/obp/' . $this->api_version,
            ),
        );

        $this->oauth = new OAuth($this->settingsArr['consumer']['key'], $this->settingsArr['consumer']['secret']);

        if ($this->debug) {
            $this->oauth->enableDebug();
        }
    }

    /**
     * @name handle_oauth_action
     * @description
     * @throws Exception
     */
    private function handle_oauth_action()
    {
        $callback_uri = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
        $callback_uri .= $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?oauthcallback=1';

        // Obtain a request token
        $request_token_info = $this->oauth->getRequestToken($this->settingsArr['url']['token']['request'], $callback_uri);

        if ($request_token_info['oauth_callback_confirmed'] == 'true') {
            $_SESSION['oauth_token'] = $request_token_info['oauth_token'];
            $_SESSION['oauth_token_secret'] = $request_token_info['oauth_token_secret'];
            session_write_close();
        } else {
            $message = 'oauth_callback_confirmed returned from server excepted to be true, got: ';
            $message .= $request_token_info['oauth_callback_confirmed'];
            throw new Exception($message);
        }

        $redirect_uri = $this->settingsArr['url']['auth'] . '?oauth_token=' . $request_token_info['oauth_token'];
        header('location: ' . $redirect_uri);
    }

    /**
     * @name handle_oauth_callback_action
     * @description
     */
    private function handle_oauth_callback_action()
    {
        $access_token_info = $this->oauth->getAccessToken($this->settingsArr['url']['token']['access'], null, $_GET['oauth_verifier']);

        $_SESSION['oauth_token_access'] = $access_token_info['oauth_token'];
        $_SESSION['oauth_token_secret_access'] = $access_token_info['oauth_token_secret'];
        session_write_close();
    }

    public function handle_callback()
    {
        if (!isset($_GET['oauthcallback']) || $_GET['oauthcallback'] != 1 || !isset($_GET['oauth_verifier'])) {
            try {
                $this->handle_oauth_action();
            } catch (Exception $e) {
                die('<hr /><pre>' . print_r(array($e->getMessage(), '<br />Here: ' . __LINE__ . ' at ' . __FILE__), true) . '</pre><hr />'); // TODO: Handle Exception
            }
        } else {
            if (!isset($_SESSION['oauth_token_access']) || !isset($_SESSION['oauth_token_secret_access'])) {
                if (!isset($_SESSION['oauth_token']) || !isset($_SESSION['oauth_token_secret']) || $_SESSION['oauth_token'] != $_GET['oauth_token']) {
                    $message = 'Expecting oauth_token and oauth_token_secret, restart oAuth process';
                    die('<hr /><pre>' . print_r(array($message, '<br />Here: ' . __LINE__ . ' at ' . __FILE__), true) . '</pre><hr />'); // TODO: Handle Exception
                }

                try {
                    $this->oauth->setToken($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

                    $this->handle_oauth_callback_action();
                } catch (OAuthException $e) {
                    $message = $e->lastResponse . '_ : _' . $e->getMessage();
                    die('<hr /><pre>' . print_r(array($message, '<br />Here: ' . __LINE__ . ' at ' . __FILE__), true) . '</pre><hr />'); // TODO: Handle Exception
                }
            }

            $this->oauth->setToken($_SESSION['oauth_token_access'], $_SESSION['oauth_token_secret_access']);
        }
    }

    /**
     * @name oauth_request
     * @description
     * @param $endpoint
     * @param string $method
     * @return mixed
     */
    private function oauth_request($endpoint, $method = 'get')
    {
        return $this->oauth->{$method}($this->base_url . '/' . $endpoint);
    }

    /**
     * @name get_banksArr
     * @description
     * @return mixed
     */
    public function get_banksArr()
    {
        $this->oauth_request('banks', 'fetch');
        $responseArr = json_decode($this->oauth->getLastResponse());

        return $responseArr;
    }
}