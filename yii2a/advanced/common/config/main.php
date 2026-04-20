<?php
return [
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        // Shared by frontend, client, etc. (Momentus SVG/PNG uploads use Yii::$app->get('s3')).
        's3' => [
            'class' => 'frostealth\yii2\aws\s3\Service',
            'credentials' => [
                'key' => '',
                'secret' => '',
            ],
            'region' => 'ap-southeast-2',
            'defaultBucket' => 'eventdraw01syd',
            'defaultAcl' => 'bucket-owner-full-control',
        ],
    ],
];
