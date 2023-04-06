<?php
class TunlEmbedSDK
{
    // Properties
    protected $url = null;

    function __construct()
    {
        $this->url = getenv('API_URL') ? getenv('API_URL') : "https://test-payment.tunl.com";
    }

    function set_production_url()
    {
        $this->url = "https://payment.tunl.com";
    }

    // Methods
    function post($url, $data)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt(
            $curl,
            CURLOPT_HTTPHEADER,
            array("Content-type: application/json")
        );
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));

        $json_response = curl_exec($curl);

        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if (!($status == 201 || $status == 200)) {
            http_response_code($status);
            die("Error: call to URL $url failed with status $status, response $json_response, curl_error " . curl_error($curl) . ", curl_errno " . curl_errno($curl));
        }

        curl_close($curl);

        $response = json_decode($json_response, true);
        return $response;
    }
    function get_form_url($options)
    {
        return $this->post($this->url . "/embed/get-card-form-url.php", $options);
    }
}
?>
