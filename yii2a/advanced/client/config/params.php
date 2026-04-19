<?php
return [
    'adminEmail' => 'admin@eventdraw.com.au',    
    'momentus' => [
        'baseUrl' => getenv('MOMENTUS_BASE_URL') ?: 'https://api-sandbox.gomomentus.com/enterprise/connect/api',
        'apiToken' => getenv('MOMENTUS_API_TOKEN') ?: '',
        'subscriptionKey' => getenv('MOMENTUS_SUBSCRIPTION_KEY') ?: '',
        'orgCode' => getenv('MOMENTUS_ORG_CODE') ?: '10',
        'timeout' => getenv('MOMENTUS_TIMEOUT') ?: 2000,
    ],
];
