<?php
return [
    'momentus' => [
        'baseUrl' => 'https://api-sandbox.gomomentus.com/enterprise/connect/api',
        'apiToken' => '',
        'subscriptionKey' => '',
        'orgCode' => '10',
        'timeout' => 20,
        'floorplanWorkflow' => [
            'enabled' => true,
            'defaultMomentusEventId' => 9427,
            'defaultOrgCode' => '10',
        ],
    ],
    'adminEmail' => 'support@eventdraw.com.au',
    'supportEmail' => 'support@eventdraw.com.au',
    'senderEmail' => 'support@eventdraw.com.au',
    'senderName' => 'EventDraw support',
    'user.passwordResetTokenExpire' => 86400,
    'microsoft' => [
        'tenant_id' => 'efbdcbf7-4ed4-4c69-9d7a-d9391905bdbd',
        'client_id' => 'f1aa6f15-6503-4fb7-b4c8-0ee67ebd3029',
        'client_secret' => '',
        'redirect_uri' => 'https://momentusstaging.eventdrawus.com/frontend/web/site/sso-login',
        'scope' => 'openid profile email User.Read',
    ],
];
