<?php
class TunlEmbedSDK
{
    // Properties
    protected $url = null;
    protected $tunl_api = null;
    protected $token = null;
    protected $authToken = null;
    protected $vaultToken = null;

    function __construct()
    {
        $this->url = getenv('API_URL') ? getenv('API_URL') : "https://test-payment.tunl.com";
        $this->tunl_api = getenv('TUNL_API_URL') ? getenv('TUNL_API_URL') : "https://test-api.tunl.com";
    }

    function set_production_url()
    {
        $this->url = "https://payment.tunl.com";
    }

    function set_production_tunl_api()
    {
        $this->tunl_api = "https://api.tunl.com";
    }

    function get($url)
    {
        return $this->request($url, null, 'GET');
    }

    function post($url, $data)
    {
        return $this->request($url, $data);
    }

    function patch($url, $data)
    {
        return $this->request($url, $data, 'PATCH');
    }

    function request($url, $data, $method = "POST")
    {
        $authHeader = $this->token ? "Authorization: Bearer $this->token" : "";
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array("Content-type: application/json", $authHeader)
        );
        ($method === "POST")
            ? curl_setopt($curl, CURLOPT_POST, true)
            : curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        $json_response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (!($status == 201 || $status == 200)) {
            http_response_code($status);
            http_response_code($status);
            die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }

        curl_close($curl);

        $response = json_decode($json_response, true);
        // return $response;
        return array(
            'data' => $response,
            'status' => $status,
            'curl_error' => curl_error($curl),
            'curl_errno' => curl_errno($curl)
        );
    }

    function auth($payload, $handleErr = true)
    {
        $results = $this->post($this->tunl_api . "/api/auth", $payload);
        if ($results['status'] !== 200 && $handleErr) {
            http_response_code(401);
            error_log(json_encode($results));
            echo json_encode($results);
            exit();
        }
        $this->token = $results['data']['token'];
        return $results;
    }

    function processSaleWithVault($payload){
        $auth = array(
            "username" => $payload['api_key'],
            "password" => $payload['secret'],
            "lifespan" => 1,
            "scope" => "PAYMENT_WRITE"
        );

        $this->auth($auth);

        $vaultToken = $payload['vault_token'];
        $vaultPayload = array(
            "amount" => $payload['amount'],
            "action" => "sale",
            "ordernum" => $payload['ordernum'] ?? time()
        );
        $vaultSale = $this->post($this->tunl_api . "/api/vault/token/$vaultToken/payments", $vaultPayload);

        return $vaultSale;
    }

    function completePreAuthorization($payload){
        $auth = array(
            "username" => $payload['api_key'],
            "password" => $payload['secret'],
            "lifespan" => 1,
            "scope" => "PAYMENT_WRITE"
        );

        $this->auth($auth);

        // Get Transaction
        $ttid = $payload['ttid'];
        $transaction = $this->get($this->tunl_api . "/api/payments/$ttid");

        $results = $this->patch($this->tunl_api . "/api/payments/$ttid", $transaction);

        return $results;
    }

    function get_form_url($options)
    {
        return $this->post($this->url . "/embed/get-card-form-url.php", $options)['data'];
    }
}
?>
