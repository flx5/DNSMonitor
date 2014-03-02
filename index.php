<?php
set_time_limit(0);

define('INC', realpath(dirname(__FILE__))."/inc/");
include('config.php');

require_once(INC.'HTTP.php');
require_once(INC.'check.php');
require_once(INC.'api.php');

define('TTL', 10);

foreach($domains as $domainName=>$domain) {
    
    if(strpos($domain['check'], ".") !== false || !file_exists(INC.'check/'.$domain['check'].'.php'))
    {
        echo "ERROR: There is no check named ".$domain['check']."<br>\n";
        continue;
    }
    
    if(strpos($domain['api'], ".") !== false || !file_exists(INC.'api/'.$domain['api'].'.php'))
    {
        echo "ERROR: There is no API named ".$domain['api']."<br>\n";
        continue;
    }
    
    require_once(INC.'check/'.$domain['check'].'.php');
    require_once(INC.'api/'.$domain['api'].'.php');
    
    $checkName = 'Check_'.$domain['check']; 
    $check = new $checkName();
    /* @var $check Check */
    
    $apiName = 'API_'.$domain['api'];
    $api = new $apiName();
    /* @var $api API */
    
    $workingIPs = Array();
    
    foreach($domain['ip'] as $ip) {
        $success = $check->Execute($domainName, $ip);
        if($success)
            $workingIPs[] = $ip;
    }
    
    if(count($workingIPs) == 0)
        $workingIPs = $domain['fallback'];
    
    $api->Update($domainName, $workingIPs, $domain['extra']);
}
?>
