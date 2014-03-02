<?php

class API_Cloudflare extends API {

    const APIKEY = "";
    const MAIL = "";
    const URL = "https://www.cloudflare.com/api_json.html";

    public function Update($domain, $workingIPs, $extra = Array()) {
        if (!isset($extra['zone'])) {
            echo "ERROR: You must configure the zone for Cloudflare";
            return false;
        }

        $zones = $this->GetRecords($domain, $workingIPs, $extra['zone']);
        if ($zones === false)
            return false;
    }

    private function GetRecords($domain, $ips, $zone) {
        $zones = $this->CallAPI('rec_load_all', Array('z' => $zone));

        if ($zones->result != "success")
            return false;

        $entries = $zones->response->recs->objs;

        $updateUseless = Array();

        foreach ($entries as $entry) {
            if ($entry->name != $domain)
                continue;

            if ($entry->type != 'A' || !in_array($entry->content, $ips)) {
                $this->DropEntry($entry->rec_id, $zone);
                continue;
            }

            $updateUseless[] = $entry->content;
        }

        $ips = array_diff($ips, $updateUseless);

        foreach ($ips as $ip) {
            $this->AddEntry($zone, $domain, $ip);
        }
    }

    private function AddEntry($zone, $domain, $ip) { 
        $this->CallAPI('rec_new', Array(
            'z' => $zone,
            'type' => 'A',
            'name' => $domain,
            'content'=>$ip,
            'ttl'=>300
        ));
    }

    private function DropEntry($id, $zone) { 
        $this->CallAPI('rec_delete', Array('z' => $zone, 'id' => $id));
    }

    private function CallAPI($action, $params = Array()) {
        $params['a'] = $action;
        $params['tkn'] = self::APIKEY;
        $params['email'] = self::MAIL;

        return json_decode(HTTP::SendPost(self::URL, $params));
    }

}

?>
