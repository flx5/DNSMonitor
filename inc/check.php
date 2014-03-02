<?php
abstract class Check {
    abstract function Execute($domain, $ip, $port = 80);
}
?>
