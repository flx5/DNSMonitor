<?php
 /*=============================================================================*\
|                          The MIT License (MIT)                                  |
|---------------------------------------------------------------------------------|
|                                                                                 |
|   Copyright (c) 2014 flx5                                                       |
|                                                                                 |
|   Permission is hereby granted, free of charge, to any person obtaining a copy  |
|   of this software and associated documentation files (the "Software"), to deal |
|   in the Software without restriction, including without limitation the rights  |
|   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell     |
|   copies of the Software, and to permit persons to whom the Software is         |
|   furnished to do so, subject to the following conditions:                      |
|                                                                                 |
|   The above copyright notice and this permission notice shall be included in    |
|   all copies or substantial portions of the Software.                           |
|                                                                                 |
|   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR    |
|   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,      |
|   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE   |
|   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER        |
|   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, |
|   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN     |
|   THE SOFTWARE.                                                                 |
|                                                                                 |
 \*==============================================================================*/

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