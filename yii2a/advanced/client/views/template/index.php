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

// Client app endpoint so the logged-in client session resolves per-client Momentus credentials.
$searchSpacesBaseUrl = Url::to(['momentus/search-spaces']);

$momentusOrgForSearch = '';
if (!Yii::$app->user->isGuest && !empty(Yii::$app->user->identity->clientid)) {
    $clientRow = Client::findOne((int) Yii::$app->user->identity->clientid);
    if ($clientRow !== null) {
        $momentusOrgForSearch = trim((string) $clientRow->momentusOrgCode);
    }
}
$momentusOrgJson = json_encode($momentusOrgForSearch);
$momentusClientIdForSearch = 0;
if (!Yii::$app->user->isGuest && !empty(Yii::$app->user->identity->clientid)) {
    $momentusClientIdForSearch = (int) Yii::$app->user->identity->clientid;
}
$momentusClientIdJson = json_encode($momentusClientIdForSearch);

$momentusSpaceOwners = [];
$momentusSpaceOwnerLabels = [];
$templateOwnerQuery = Template::find();
if (Client::usesClientResourceScoping()) {
    $clientId = !Yii::$app->user->isGuest ? (int) Yii::$app->user->identity->clientid : 0;
    $templateOwnerQuery->where(['clientid' => $clientId]);
}
foreach ($templateOwnerQuery->asArray()->all() as $row) {
    $c = strtoupper(trim((string) ($row['momentusSpaceCode'] ?? '')));
    if ($c !== '') {
        $momentusSpaceOwners[$c] = (int) $row['id'];
        $name = trim((string) ($row['templateName'] ?? ''));
        $momentusSpaceOwnerLabels[$c] = $name !== '' ? $name : ('Template #' . $row['id']);
    }
}
$momentusSpaceOwnersJson = Json::encode($momentusSpaceOwners);
$momentusSpaceOwnerLabelsJson = Json::encode($momentusSpaceOwnerLabels);
$saveUrlSpaceJs = Json::encode(Url::to(['template/ajax-set-momentus-space']));

$this->registerCss(
    '.select2-results__option[aria-disabled=true],'
    . '.select2-results__option--disabled,'
    . '.select2-results__option.momentus-space-taken {'
    . 'color:#888 !important;cursor:not-allowed !important;'
    . 'opacity:0.55;background-color:#f5f5f5 !important;}'
);

$this->registerJs(
    'window.__momentusSpaceCodeOwner = ' . $momentusSpaceOwnersJson . ";\n" .
    'window.__momentusSpaceOwnerLabels = ' . $momentusSpaceOwnerLabelsJson . ";\n" .
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
                'value' => function ($model) use ($searchSpacesBaseUrl, $momentusOrgJson, $momentusClientIdJson, $saveUrlSpaceJs) {

                    $searchUrl = $searchSpacesBaseUrl;

                    $spaceCode = trim((string) $model->momentusSpaceCode);
                    $spaceDescr = trim((string) $model->momentusSpaceDescr);
                    $initText = $spaceCode !== ''
                        ? ($spaceDescr !== '' ? ($spaceCode . ' — ' . $spaceDescr) : $spaceCode)
                        : '';

                    return Select2::widget([
                        'name' => 'momentus_space_'.$model->id,
                        'value' => $spaceCode,
                        'initValueText' => $initText,
                        'data' => $spaceCode !== '' ? [$spaceCode => $initText] : [],
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
                                'cache' => false,
                                'data' => new JsExpression('function(params){ return { q: params.term, org_code: ' . $momentusOrgJson . ', client_id: ' . $momentusClientIdJson . ', _: Date.now() }; }'),
                                'processResults' => new JsExpression('function(data){
                                    var owners = window.__momentusSpaceCodeOwner || {};
                                    var labels = window.__momentusSpaceOwnerLabels || {};
                                    var myTid = parseInt(window.__mpsCurrentTid, 10) || 0;
                                    var arr = Array.isArray(data) ? data : [];
                                    return {
                                        results: arr.map(function (x) {
                                            if (!x) { return null; }
                                            var code = String(x.code != null ? x.code : "").trim();
                                            if (code === "" && x.id != null) {
                                                code = String(x.id).trim();
                                            }
                                            if (code === "") { return null; }
                                            var codeKey = code.toUpperCase();
                                            var oid = owners[codeKey];
                                            var takenByOther = myTid > 0 && oid !== undefined && parseInt(oid, 10) !== myTid;
                                            var text = x.text || (code + " — " + (x.description || ""));
                                            if (takenByOther) {
                                                var ownerLabel = labels[codeKey] || ("Template #" + oid);
                                                text += " (assigned to " + ownerLabel + ")";
                                            }
                                            return {
                                                id: code,
                                                text: text,
                                                desc: x.description || "",
                                                disabled: takenByOther
                                            };
                                        }).filter(function (r) { return r != null; })
                                    };
                                }'),
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                            'templateResult' => new JsExpression('function (item) {
                                if (item.loading) { return item.text; }
                                var $row = jQuery("<span></span>").text(item.text);
                                if (item.disabled) {
                                    $row.addClass("momentus-space-taken").css({color:"#888",cursor:"not-allowed"});
                                }
                                return $row;
                            }'),
                        ],
                        'pluginEvents' => [
                            'select2:selecting' => new JsExpression('function (e) {
    var item = (e.params.args && e.params.args.data) || e.params.data || {};
    if (item.disabled) {
        e.preventDefault();
        var msg = item.text || "This Momentus space is already assigned to another template.";
        window.alert(msg);
    }
}'),
                            'select2:select' => new JsExpression('function (e) {
    var el = jQuery(this);
    var templateId = el.data("templateId");
    var item = e.params.data || {};
    if (item.disabled) {
        el.val(null).trigger("change");
        return;
    }
    jQuery.post(' . $saveUrlSpaceJs . ', { id: templateId, code: item.id || "", desc: item.desc || "" })
        .done(function (resp) {
            if (resp && resp.ok) {
                var o = window.__momentusSpaceCodeOwner;
                if (!o) { o = window.__momentusSpaceCodeOwner = {}; }
                var tid = parseInt(templateId, 10);
                jQuery.each(o, function (k, v) { if (parseInt(v, 10) === tid) { delete o[k]; } });
                if (item.id) { o[String(item.id).trim().toUpperCase()] = tid; }
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
