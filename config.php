<?php

$domains = Array(
    'rdns.example.com' => Array(
        'check'=>'Curl',
        'api'=>'Cloudflare',
        
        'ip' => Array(
            '127.0.0.1',
            '127.0.0.2'
        ),
        'fallback' => Array(
            '127.0.0.3'
        ),
        'extra'=>Array(
            'zone'=>'example.com'
        )
    )
);
?>
