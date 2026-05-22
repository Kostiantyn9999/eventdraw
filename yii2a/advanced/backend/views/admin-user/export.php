<?php

$model = \common\models\User::find()->All();
$filename = 'Users-'.Date('d-M-Y').'.csv';
header("Content-type: text/csv");
header("Content-Disposition: attachment; filename=".$filename);

$output = fopen( 'php://output', 'w' );
fwrite( $output, "\xEF\xBB\xBF" );
fputcsv( $output, [ 'ID', 'Client','First Name','Surname','Email','Login',
    'Last login','Total sessions','Stencil','Status','Expiry Date',
    'Admin','Support','Payment','Company Admin',
    'Created','Updated','Max Sessions','Company Name','Country'], ';' );

foreach($model as $data){
    if ($data['last_login'] != 0)
    {
        $last_login_good =  \Yii::$app->formatter->asDatetime($data['last_login'], "php:d-M-Y H:i:s");
    }
    else
    {
        $last_login_good = '';
    }

    if ($data['expiry_date'] != 0)
    {
        $expiry_date_good =  \Yii::$app->formatter->asDatetime($data['expiry_date'], "php:d-M-Y H:i:s");
    }
    else
    {
        $expiry_date_good = '';
    }

    if ($data['created_at'] != 0)
    {
        $created_at_good =  \Yii::$app->formatter->asDatetime($data['created_at'], "php:d-M-Y H:i:s");
    }
    else
    {
        $created_at_good = '';
    }

    if ($data['updated_at'] != 0)
    {
        $updated_at_good =  \Yii::$app->formatter->asDatetime($data['updated_at'], "php:d-M-Y H:i:s");
    }
    else
    {
        $updated_at_good = '';
    }

    if ($data['userIsAdmin'] == 1)
    {
        $userIsAdmin_good =  'Yes';
    }
    else
    {
        $userIsAdmin_good = 'No';
    }

    if ($data['userIsSupport'] == 1)
    {
        $userIsSupport_good =  'Yes';
    }
    else
    {
        $userIsSupport_good = 'No';
    }

    if ($data['userPayment'] == 1)
    {
        $userPayment_good =  'Yes';
    }
    else
    {
        $userPayment_good = 'No';
    }

    if ($data['company_admin'] == 1)
    {
        $company_admin_good =  'Yes';
    }
    else
    {
        $company_admin_good = 'No';
    }


    fputcsv( $output, [ $data['id'],
        $data->getClientName(),
        $data['firstname'],
        $data['surname'],
        $data['email'],
        $data['username'],
        $last_login_good,
        $data['totSession'],
        $data->getStencilName(),
        $data->getStatusName(),
        $expiry_date_good,
        $userIsAdmin_good,
        $userIsSupport_good,
        $userPayment_good,
        $company_admin_good,
        $created_at_good,
        $updated_at_good,
        $data['maxSession'],
        $data['UserCompanyName'],
        $data->getCountryName(),

    ], ';' );
}

?>