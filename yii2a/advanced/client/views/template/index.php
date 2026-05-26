<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;
use Yii;
use common\models\Client;
use common\models\Template;
use yii\helpers\Json;


/* @var $this yii\web\View */
/* @var $searchModel common\models\TemplateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Templates';
$this->params['breadcrumbs'][] = $this->title;

// Same host as this admin app; swap /client/web → /frontend/web (matches Draw.io Menus.js).
$clientWebBase = rtrim(Yii::$app->request->hostInfo . Yii::$app->request->baseUrl, '/');
$searchSpacesBaseUrl = str_replace('/client/web', '/frontend/web', $clientWebBase) . '/momentus/search-spaces';

$momentusOrgForSearch = '';
if (!Yii::$app->user->isGuest && !empty(Yii::$app->user->identity->clientid)) {
    $clientRow = Client::findOne((int) Yii::$app->user->identity->clientid);
    if ($clientRow !== null) {
        $momentusOrgForSearch = trim((string) $clientRow->momentusOrgCode);
    }
}
$momentusOrgJson = json_encode($momentusOrgForSearch);

$momentusSpaceOwners = [];
$templateOwnerQuery = Template::find();
if (Client::usesClientResourceScoping()) {
    $clientId = !Yii::$app->user->isGuest ? (int) Yii::$app->user->identity->clientid : 0;
    $templateOwnerQuery->where(['clientid' => $clientId]);
}
foreach ($templateOwnerQuery->asArray()->all() as $row) {
    $c = trim((string) ($row['momentusSpaceCode'] ?? ''));
    if ($c !== '') {
        $momentusSpaceOwners[$c] = (int) $row['id'];
    }
}
$momentusSpaceOwnersJson = Json::encode($momentusSpaceOwners);
$saveUrlSpaceJs = Json::encode(Url::to(['template/ajax-set-momentus-space']));

$this->registerJs(
    'window.__momentusSpaceCodeOwner = ' . $momentusSpaceOwnersJson . ";\n" .
    "$(document).on('select2:open', 'select[name^=\"momentus_space_\"]', function() {\n" .
    "  var m = (this.name || '').match(/momentus_space_(\\d+)/);\n" .
    "  window.__mpsCurrentTid = m ? parseInt(m[1], 10) : 0;\n" .
    "});\n",
    \yii\web\View::POS_READY
);
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
            // [
            //     'attribute' => 'momentusSpaceCode',
            //     'label' => 'Momentus Space Code',
            //     'contentOptions' => ['style' => 'white-space:nowrap;'],
            // ],
            // [
            //     'attribute' => 'momentusEventSpaceDiagramId',
            //     'label' => 'EventSpaceDiagram ID',
            //     'format' => 'raw',
            //     'value' => function($model) {
            //         $saveUrl = Url::to(['template/ajax-set-momentus-diagram-id']);
            //         return Html::input('number', 'diagram_id_' . $model->id,
            //             $model->momentusEventSpaceDiagramId,
            //             [
            //                 'class'            => 'form-control js-diagram-id',
            //                 'data-template-id' => $model->id,
            //                 'data-save-url'    => $saveUrl,
            //                 'style'            => 'width:130px',
            //                 'placeholder'      => 'e.g. 2110',
            //                 'min'              => '1',
            //             ]
            //         );
            //     },
            //     'contentOptions' => ['style' => 'min-width:150px;'],
            // ],
            [
                'attribute' => 'momentusSpaceDescr',
                'label' => 'Momentus space',
                'format' => 'raw',
                'value' => function ($model) use ($searchSpacesBaseUrl, $momentusOrgJson, $saveUrlSpaceJs) {

                    $searchUrl = $searchSpacesBaseUrl;

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
                                'data' => new JsExpression('function(params){ return { q: params.term, org_code: ' . $momentusOrgJson . ' }; }'),
                                'processResults' => new JsExpression('function(data){
                                    var owners = window.__momentusSpaceCodeOwner;
                                    if (!owners) { return { results: [] }; }
                                    var myTid = parseInt(window.__mpsCurrentTid, 10) || 0;
                                    var arr = Array.isArray(data) ? data : [];
                                    var filtered = arr.filter(function (x) {
                                        if (!myTid) { return true; }
                                        if (!x || x.code === undefined || x.code === null) { return true; }
                                        var code = String(x.code);
                                        var oid = owners[code];
                                        if (oid === undefined) { return true; }
                                        return parseInt(oid, 10) === myTid;
                                    });
                                    return {
                                        results: filtered.map(function(x) {
                                            return {
                                                id: x.code,
                                                text: x.text || (x.code + " — " + (x.description || "")),
                                                desc: x.description || ""
                                            };
                                        })
                                    };
                                }'),
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        ],
                        'pluginEvents' => [
                            'select2:select' => new JsExpression('function (e) {
    var el = jQuery(this);
    var templateId = el.data("templateId");
    var item = e.params.data || {};
    jQuery.post(' . $saveUrlSpaceJs . ', { id: templateId, code: item.id || "", desc: item.desc || "" })
        .done(function (resp) {
            if (resp && resp.ok) {
                var o = window.__momentusSpaceCodeOwner;
                if (!o) { o = window.__momentusSpaceCodeOwner = {}; }
                var tid = parseInt(templateId, 10);
                jQuery.each(o, function (k, v) { if (parseInt(v, 10) === tid) { delete o[k]; } });
                if (item.id) { o[item.id] = tid; }
                return;
            }
            if (resp && resp.message) { window.alert(resp.message); }
            el.val(null).trigger("change");
        })
        .fail(function () { window.alert("Save failed"); el.val(null).trigger("change"); });
}'),
                            'select2:clear' => new JsExpression('function (e) {
    var el = jQuery(this);
    var templateId = el.data("templateId");
    jQuery.post(' . $saveUrlSpaceJs . ', { id: templateId, code: "", desc: "" })
        .done(function (resp) {
            if (resp && resp.ok) {
                var o = window.__momentusSpaceCodeOwner;
                if (!o) { return; }
                var tid = parseInt(templateId, 10);
                jQuery.each(o, function (k, v) { if (parseInt(v, 10) === tid) { delete o[k]; } });
            }
        });
}'),
                        ],
                    ]);
                },
                'contentOptions' => ['style' => 'min-width:320px;'],
            ],
        ],
    ]); ?>

<?php
$js = <<<JS
$(document).on('change', '.js-diagram-id', function () {
    var el = $(this);
    var templateId = el.data('template-id');
    var saveUrl    = el.data('save-url');
    var diagramId  = parseInt(el.val(), 10) || 0;
    $.post(saveUrl, {id: templateId, diagram_id: diagramId})
        .done(function(resp) {
            if (resp && resp.ok) {
                el.css('border-color', '#5cb85c');
                setTimeout(function(){ el.css('border-color', ''); }, 1500);
            } else {
                el.css('border-color', '#d9534f');
            }
        })
        .fail(function() {
            el.css('border-color', '#d9534f');
        });
});
JS;
$this->registerJs($js);
?>


</div>
