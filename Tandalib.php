<?php

class Tandalib {

    //https://my.tanda.co/api/v2/documentation

    private $user = ""; //add more stuff here
    private $pass = "tandahack2016"; //add more stuff here
    private $token = "";
    private $defaultscopes = "roster"; //add more stuff here
    private $checkpoint = "rosters/current";
    private $reqBase = "https://my.tanda.co/api/v2/";

    public function __construct() {

        //this should actually read the database and get the token out of there.
        //implementation below creates a new one every time - bad.

        $this->token = $this->get_db_token();
        
        if ($this->check_auth() == false) {
            print "Reauth";
            $this->re_auth();
        }
        
        //all play
        //$this->re_auth();
    }
    
    private function get_db_token(){
        //rewrite this function to actually read it from your database....
        //you would do this because otherwisie a new user token would be created each time.
        
        return "";
        
        
    }

    public function check_auth() {
        $result = $this->auth_get($this->checkpoint);
        return array_key_exists("error", $result) == false;
    }

    public function re_auth() {
        $token = $this->auth_pw($this->defaultscopes);
        $this->token = $token['access_token'];
    }

    public function auth_pw($scopes) { //return token
        $headers = array("Cache-Control: no-cache");
        $data = array(
            "username" => $this->user,
            "password" => $this->pass,
            "scope" => $scopes,
            "grant_type" => "password"
        );
        
        $auth = $this->_post("https://my.tanda.co/api/oauth/token", $headers, $data);
        return $auth;
    }

    public function auth_get($url) {
        $headers = array(
            "Cache-Control: no-cache",
            sprintf("Authorization: bearer %s", $this->token)
        );

        $fullUrl = $this->reqBase . $url;
        return $this->_get($this->reqBase . $url, $headers);
    }

    public function auth_post($url, $data) {
        //#todo - written but not tested.
        $headers = array(
            "Content-Type: application/json",
            sprintf("Authorization: bearer %s", $this->token)
        );

        return $this->_post($url, $headers, $data);
    }

    private function _post($url, $headers, $data) {
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);


        curl_setopt($curl, CURLOPT_POST, sizeof($data));
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($curl);

        curl_close($curl);

        return json_decode($server_output, true);
    }

    private function _get($url, $headers) {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers
        ));
        $resp = curl_exec($curl);
        curl_close($curl);

        return json_decode($resp, true);
    }

}



$tib = new Tandalib();
$rosters = $tib->curl_auth_get("rosters/on/2016-04-18");
var_dump($rosters);

?>
