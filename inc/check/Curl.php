<?php

class Check_Curl extends Check {

    public function Execute($domain, $ip, $port = 80) {
        $c = curl_init('http://'.$ip.'/');
        curl_setopt($c, CURLOPT_HTTPHEADER, array('Host: '.$domain));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_CONNECTTIMEOUT, TTL);
        $response = curl_exec($c);
        curl_close($c);
        
        if($response === false)
            return false;
        
        return true;
    }

}

?>
