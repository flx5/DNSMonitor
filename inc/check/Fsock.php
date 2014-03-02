<?php

class Check_Fsock extends Check {

    public function Execute($domain, $ip, $port = 80) {
        $fp = fSockOpen($ip, $port, $errno, $errstr, TTL);
        if ($fp) {
            fclose($fp);
            return true;
        }
        return false;
    }

}

?>
