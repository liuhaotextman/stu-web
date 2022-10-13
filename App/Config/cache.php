<?php
return [
    'type' => 'file',
    'stores' => [
        'file' => [
            'expire' => 0,
            'path' => ''
        ],
        'redis' => [
            'expire' => 0,
            'host' => '10.5.0.3',
            'port' => 6379,
        ]
    ]
];