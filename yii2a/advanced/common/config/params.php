<?php
return [
    /**
     * Momentus Connect (gomomentus). Used by frontend + client (MomentusClient reads Yii::$app->params['momentus']).
     * Prefer env vars on staging/production; optional fallbacks match client/config/params.php for local/dev parity.
     */
    'momentus' => [
        'baseUrl' => getenv('MOMENTUS_BASE_URL') ?: 'https://api-sandbox.gomomentus.com/enterprise/connect/api',
        'apiToken' => getenv('MOMENTUS_API_TOKEN') ?: '',
        'subscriptionKey' => getenv('MOMENTUS_SUBSCRIPTION_KEY') ?: '',
        'orgCode' => getenv('MOMENTUS_ORG_CODE') ?: '10',
        'timeout' => (int) (getenv('MOMENTUS_TIMEOUT') ?: 20),
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
        'tenant_id' => getenv('MICROSOFT_TENANT_ID') ?: '',
        'client_id' => getenv('MICROSOFT_CLIENT_ID') ?: '',
        'client_secret' => getenv('MICROSOFT_CLIENT_SECRET') ?: '',
        'redirect_uri' => getenv('MICROSOFT_REDIRECT_URI') ?: 'https://momentusstaging.eventdrawus.com/frontend/web/site/sso-login',
        'scope' => 'openid profile email User.Read',
    ],
];
