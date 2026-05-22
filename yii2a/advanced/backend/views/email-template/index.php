<?php



use yii\helpers\Html;

use yii\data\Sort;

use yii\grid\GridView;

use yii\widgets\Pjax;



use yii\helpers\Url;

/* @var $this yii\web\View */

/* @var $searchModel common\models\TemplateSearch */

/* @var $dataProvider yii\data\ActiveDataProvider */



$this->title = 'Email Template';

$this->params['breadcrumbs'][] = $this->title;



?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css"/>

<div class="row-full">



    <h1><?= Html::encode($this->title) ?></h1>



    <p>

        <?= Html::a('Create Template', ['create'], ['class' => 'btn btn-success']) ?>

    </p>

    <table class="table table-striped" id="email_template_table">

        <thead>

            <tr>

                <th>Sr.No</th>
                <th>Type</th>
                <th>Template Name</th>

                <th>Email Subject</th>
                <th>Bulletin Heading</th>
                <th>Action</th>

            </tr>

        </thead>

        <tbody>

            <?php $count=1; if(!empty($dataProvider)){foreach($dataProvider as $key=> $obj){?>

            <tr>

                <td><?= $count++;?></td>
                <td>
                    <?php if($obj->template_type=='email-template'){
                        echo 'Email Template';
                    }elseif($obj->template_type=='bulletin-template'){
                        echo 'Bulletin Template';
                    }elseif($obj->template_type=='welcome-template'){
                        echo 'Welcome Template';
                    }else{
                        echo 'no-assigned';
                    }?>
                </td>
                <td><?= $obj->template_name;?></td>

                <td><?= $obj->email_subject;?></td>
                <td><?= $obj->bulletin_heading;?></td>
                <td>

                    <a class="btn btn-default btn-sm" href="<?= Url::to(['/email-template/update/?id='.$obj->id])?>"><i class="fa fa-pencil"></i></a>
                    
                    <a class="btn btn-default btn-sm" href="<?= Url::to(['/email-template/copy-template/?id='.$obj->id])?>"><i class="fa fa-copy"></i></a>


                    <a class="btn btn-default btn-sm" href="<?= Url::to(['/email-template/delete/?id='.$obj->id])?>"><i class="fa fa-trash"></i></a>

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