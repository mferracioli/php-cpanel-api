<?php
class CPanel {

    private $_username;
    private $_password;
    private $_host;
    private $_port;
    private $_baseurl;

    function __construct($username, $password, $host, $port=2087) {
        $this->_baseurl = "https://". $host .":" . $port;
        $this->_username = $username;
        $this->_password = $password;
    }

    public function dorequest($query) {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST,0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER,0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,1);

        $header[0] = "Authorization: Basic " . base64_encode($this->_username.":".$this->_password) . "\n\r";

        curl_setopt($curl,CURLOPT_HTTPHEADER,$header);
        curl_setopt($curl, CURLOPT_URL, $query);

        $result = curl_exec($curl);

        curl_close($curl);

        $json = json_decode($result);

        return $json->cpanelresult->data;
    }

    public function __call($method, $args) {
        $params = array();

        foreach ($args[1] as $key => $value) {
            $params[] = urlencode($key) . "=" . urlencode($value);
        }

        $params = implode("&" , $params);

        $query = $this->_baseurl . "/json-api/cpanel?cpanel_jsonapi_module=". $args[0] ."&cpanel_jsonapi_func=$method&cpanel_jsonapi_version=2&" . $params;

        return $this->dorequest($query);
    }
}
?>