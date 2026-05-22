<?php



use yii\helpers\Html;

use yii\data\Sort;

use yii\grid\GridView;

use yii\widgets\Pjax;
use common\models\User;
use common\models\Client;

use yii\helpers\Url;

/* @var $this yii\web\View */

/* @var $searchModel common\models\TemplateSearch */

/* @var $dataProvider yii\data\ActiveDataProvider */



$this->title = 'Bulletin Template';

$this->params['breadcrumbs'][] = $this->title;



?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"/>

<div class="row-full">



    <h1><?= Html::encode($this->title) ?></h1>
    <table class="table table-striped" id="email_template_table">

        <thead>

            <tr>

                <th>Sr.No</th>
                <th>Template Name</th>
                <th>Client Name</th>
                <th>Action</th>

            </tr>

        </thead>

        <tbody>

            <?php $count=1; if(!empty($dataProvider)){foreach($dataProvider as $key=> $obj){
                $client   =   new Client();
                $userInfo = $client::find()->where(['id'=>$obj->client_id])->one();
                // echo '<pre>';
                // print_r($userInfo);
            ?>

            <tr>

                <td><?= $count++;?></td>
                <td><?= $obj->template_name;?></td>
                <td><?= $userInfo->clientName;?></td>
                <td>
                    <a class="btn btn-default btn-sm" href="<?= Url::to(['/bulletin-template/update/?id='.$obj->id])?>"><i class="fa fa-pencil"></i></a>
                </td>

            </tr>

            <?php $count++; } }?>

        </tbody>

    </table>

</div>



<script type="text/javascript">

    //$(document).ready( function () {

        $('#email_template_table').DataTable();

    //} );

</script>