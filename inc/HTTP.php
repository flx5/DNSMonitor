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

class HTTP {

    const UseCURL = false;

    public static function SendPost($url, $post = Array()) {

        $post = http_build_query($post);

        if (self::UseCURL)
            return self::SendPostCurl($url, $post);

        return self::SendPostFsock($url, $post);
    }

    private static function SendPostFsock($url, $query) {
        $url = parse_url($url);

        if (!isset($url['port'])) {
            switch ($url['scheme']) {
                case 'http':
                    $url['port'] = 80;
                    break;
                case 'https':
                    $url['port'] = 443;
                    $url['scheme'] = "ssl";
                    break;
            }
        }

        if ($url['scheme'] == "https")
            $url['scheme'] = "ssl";

        if (!isset($url['path']))
            $url['path'] = "/";

        $fp = fsockopen($url['scheme'] . "://" . $url['host'], $url['port']);
        if (!$fp)
            return null;

        fputs($fp, "POST " . $url['path'] . " HTTP/1.0\r\n");
        fputs($fp, "Host: " . $url['host'] . "\r\n");
        fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
        fputs($fp, "Content-length: " . strlen($query) . "\r\n");
        fputs($fp, "Connection: close\r\n\r\n");
        fputs($fp, $query);

        $res = "";

        while (!feof($fp)) {
            $res .= fgets($fp, 128);
        }

        fclose($fp);

        $res = substr($res, strpos($res, "\r\n\r\n") + 4);
        return $res;
    }

    private static function SendPostCurl($url, $query) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSLVERSION, 3);
       // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Uncomment this if you are having issues with ssl/https and you want to stick to curl

        $response = curl_exec($ch);
        die($response);
        return $response;
    }

}
?>