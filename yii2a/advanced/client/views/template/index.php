<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $searchModel common\models\TemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Templates';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row-full">

    <h1><?= Html::encode($this->title) ?></h1>


    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
         'columns' => [
            'id',

            'templateName',
             [
    'attribute' => 'momentusSpaceDescr',
    'label' => 'Momentus space',
    'format' => 'raw',
    'value' => function($model) {

        $searchUrl = 'https://momentusadmin.eventdrawus.com/frontend/web/momentus/search-spaces';
        $saveUrl = Url::to(['template/ajax-set-momentus-space']);

        $initText = $model->momentusSpaceCode
            ? ($model->momentusSpaceDescr
                ? ($model->momentusSpaceCode . ' — ' . $model->momentusSpaceDescr)
                : $model->momentusSpaceCode)
            : '';

        return Select2::widget([
            'name' => 'momentus_space_'.$model->id,
            'value' => $model->momentusSpaceCode,     
            'initValueText' => $initText,               
            'options' => [
                'placeholder' => 'Type to search Space Description or Code ...',
                'data-template-id' => $model->id,
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 1,
                'ajax' => [
                    'url' => $searchUrl,
                    'dataType' => 'json',
                    'delay' => 250,
                    'data' => new JsExpression('function(params){ return {q: params.term}; }'),
                    'processResults' => new JsExpression('function(data){
                        var items = (data || []).map(function(x){
                            return {
                                id: x.code,                 
                                text: x.text || (x.code + " — " + x.description),
                                desc: x.description || ""
                            };
                        });
                        return {results: items};
                    }'),

                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            ],
            'pluginEvents' => [
                // select value
                "select2:select" => new JsExpression("function(e){
                    var el = $(this);
                    var templateId = el.data('template-id');
                    var item = e.params.data || {};
                    $.post('$saveUrl', {
                        id: templateId,
                        code: item.id || '',
                        desc: item.desc || ''
                    });
                }"),

                // clear
                "select2:clear" => new JsExpression("function(e){
                    var el = $(this);
                    var templateId = el.data('template-id');
                    $.post('$saveUrl', {id: templateId, code: '', desc: ''});
                }"),
            ],
        ]);
    },
    'contentOptions' => ['style' => 'min-width:320px;'],
],

            
        ],
    ]); ?>


</div>
