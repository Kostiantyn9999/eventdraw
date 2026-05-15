<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\grid\ActionColumn;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\MomentusShapeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $orgCode string */

$this->title = 'Shapes';
$this->params['breadcrumbs'][] = $this->title;
$defaultOrgCode = $orgCode ?: (\Yii::$app->params['momentus']['orgCode'] ?? '');

$assignMappingUrl = Url::to(['momentus/assign-mapping']);
$unassignMappingUrl = Url::to(['momentus/unassign-mapping']);
?>

<style>
    .m-indicator {
        display: inline-block;
        background: #007bff;
        color: #fff;
        padding: 2px 7px;
        border-radius: 3px;
        font-size: 11px;
        font-weight: 700;
        margin-left: 6px;
        vertical-align: middle;
        letter-spacing: .5px;
    }
    .no-mapping { color: #999; font-style: italic; }
    .org-toolbar { margin-bottom: 15px; padding: 10px 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 4px; }
    .org-toolbar label { font-weight: 600; margin-right: 8px; }
    .org-toolbar input { padding: 5px 10px; width: 120px; border: 1px solid #ccc; border-radius: 3px; }
    .org-toolbar button { padding: 5px 14px; background: #007bff; color: #fff; border: none; border-radius: 3px; cursor: pointer; margin-left: 8px; }
    .org-toolbar button:hover { background: #0069d9; }
</style>

<div class="row-full">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Shape', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="org-toolbar">
        <?= Html::beginForm(['momentus/shape-manager'], 'get') ?>
            <label for="org_code">Org Code:</label>
            <input type="text" name="org_code" id="org_code" value="<?= Html::encode($defaultOrgCode) ?>" placeholder="e.g. 20">
            <button type="submit">Apply</button>
            <?php if ($defaultOrgCode !== ''): ?>
                <span style="margin-left:12px;color:#155724;font-size:13px;">
                    Showing mappings for org <strong><?= Html::encode($defaultOrgCode) ?></strong>
                </span>
            <?php endif; ?>
        <?= Html::endForm() ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'shapeType',
                'label' => 'Shape Name',
                'format' => 'raw',
                'value' => function ($model) use ($defaultOrgCode) {
                    $name = Html::encode($model->shapeType);
                    if ($defaultOrgCode !== '' && isset($model->mapping_id) && $model->mapping_id) {
                        $name .= ' <span class="m-indicator" title="Linked to Momentus resource for org ' . Html::encode($defaultOrgCode) . '">M</span>';
                    }
                    return $name;
                },
                'contentOptions' => ['data-col' => 'shape-name'],
            ],
            'description',
            [
                'attribute' => 'mapping_resource_description',
                'label' => 'MT Resource Description',
                'format' => 'raw',
                'value' => function ($model) use ($orgCode, $assignMappingUrl, $unassignMappingUrl, $defaultOrgCode) {

                    $searchUrl = Url::to(['momentus/search-resources']);
                    $jsOrgCode = addslashes($defaultOrgCode);

                    $initText = '';
                    if (!empty($model->mapping_id)) {
                        $d = trim((string) ($model->mapping_resource_description ?? ''));
                        $t = trim((string) ($model->mapping_resource_type ?? ''));
                        $c = trim((string) ($model->mapping_resource_code ?? ''));
                        if ($d !== '') {
                            $initText = $d;
                            if ($t !== '') {
                                $initText .= ' (' . $t . ')';
                            }
                            if ($c !== '') {
                                $initText .= ' (' . $c . ')';
                            }
                        } elseif ($c !== '') {
                            $initText = $c;
                            if ($t !== '') {
                                $initText .= ' (' . $t . ')';
                            }
                        }
                    }

                    $initValue = '';
                    if (isset($model->mapping_id) && $model->mapping_id) {
                        $initValue = (isset($model->mapping_resource_code) && $model->mapping_resource_code !== null && $model->mapping_resource_code !== '')
                            ? $model->mapping_resource_code . '-' . ($model->mapping_sequence ?? 1)
                            : 'mapping-' . $model->mapping_id;
                    }
                    
                    return Select2::widget([
                        'name' => 'momentus_resource_' . $model->id,
                        'value' => $initValue ?: null,
                        'initValueText' => $initText,
                        'options' => [
                            'placeholder' => 'Type to search resources...',
                            'data-shape-id' => $model->id,
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 1,
                            'ajax' => [
                                'url' => $searchUrl,
                                'dataType' => 'json',
                                'delay' => 2500,
                                'data' => new JsExpression('function(params){ return {q: params.term, org_code: "' . Html::encode($orgCode) . '", pageSize: 8000}; }'),
                                'processResults' => new JsExpression('function(data){
                                    var items = (data || []).map(function(x){
                                        return {
                                            id: x.id,
                                            text: x.description + " (" + x.type + ")" + " (" + x.code + ")",
                                            description: x.description,
                                            type: x.type,
                                            code: x.code,
                                            sequence: x.sequence
                                        };
                                    });
                                    return {results: items};
                                }'),
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                        ],
                        'pluginEvents' => [
                            "select2:select" => new JsExpression("function(e){
                                var el = $(this);
                                var shapeId = el.data('shape-id');
                                var item = e.params.data || {};
                                var row = el.closest('tr');
                                var orgCode = '$jsOrgCode';

                                if (orgCode) {
                                    $.post('$assignMappingUrl', {
                                        shape_id: shapeId,
                                        org_code: orgCode,
                                        resource_code: item.code || '',
                                        resource_description: item.description || '',
                                        resource_type: item.type || '',
                                        sequence: item.sequence || 1
                                    }, function(resp) {
                                        if (resp && resp.success) {
                                            row.find('td[data-col=res-code]').text(item.code || '');
                                            row.find('td[data-col=res-seq]').text(item.sequence || 1);
                                            if (resp.mapping_id) {
                                                row.find('td[data-col=res-code]').attr('data-mapping-id', resp.mapping_id);
                                            }
                                            var nameCell = row.find('td[data-col=shape-name]');
                                            if (nameCell.length && !nameCell.find('.m-indicator').length) {
                                                nameCell.append(' <span class=\"m-indicator\" title=\"Linked to Momentus resource for org ' + orgCode + '\">M</span>');
                                            }
                                        }
                                    });
                                }
                            }"),
                            "select2:clear" => new JsExpression("function(e){
                                var el = $(this);
                                var row = el.closest('tr');
                                var orgCode = '$jsOrgCode';
                                var mappingId = row.find('td[data-col=res-code]').attr('data-mapping-id');

                                if (orgCode && mappingId) {
                                    $.post('$unassignMappingUrl', {mapping_id: mappingId}, function(){
                                        row.find('td[data-col=res-code]').html('<span class=\"no-mapping\">&mdash;</span>').removeAttr('data-mapping-id');
                                        row.find('td[data-col=res-seq]').html('<span class=\"no-mapping\">&mdash;</span>');
                                        row.find('td[data-col=shape-name] .m-indicator').remove();
                                    });
                                } else {
                                    row.find('td[data-col=res-code]').html('<span class=\"no-mapping\">&mdash;</span>');
                                    row.find('td[data-col=res-seq]').html('<span class=\"no-mapping\">&mdash;</span>');
                                    row.find('td[data-col=shape-name] .m-indicator').remove();
                                }
                            }"),
                        ],
                    ]);
                },
                'contentOptions' => ['style' => 'min-width:320px;'],
            ],
            [
                'attribute' => 'mapping_resource_code',
                'label' => 'MT Resource Code',
                'format' => 'raw',
                'value' => function ($model) use ($defaultOrgCode) {
                    if ($defaultOrgCode === '') {
                        return '<span class="no-mapping">Set org code</span>';
                    }
                    if (isset($model->mapping_resource_code) && $model->mapping_resource_code !== null) {
                        return Html::encode($model->mapping_resource_code);
                    }
                    return '<span class="no-mapping">&mdash;</span>';
                },
                'contentOptions' => function ($model) {
                    $attrs = ['data-col' => 'res-code'];
                    if (isset($model->mapping_id) && $model->mapping_id) {
                        $attrs['data-mapping-id'] = $model->mapping_id;
                    }
                    return $attrs;
                },
                'visible' => true,
            ],
            [
                'attribute' => 'mapping_sequence',
                'label' => 'MT Sequence (RMSN)',
                'format' => 'raw',
                'value' => function ($model) use ($defaultOrgCode) {
                    if ($defaultOrgCode === '') {
                        return '<span class="no-mapping">Set org code</span>';
                    }
                    if (isset($model->mapping_sequence) && $model->mapping_sequence !== null) {
                        return Html::encode($model->mapping_sequence);
                    }
                    return '<span class="no-mapping">&mdash;</span>';
                },
                'contentOptions' => ['data-col' => 'res-seq'],
                'visible' => true,
            ],
            [
                'class' => ActionColumn::class,
                'header' => 'Actions',
                'template' => '{update}',
                'buttons' => [
                    'update' => function ($url, $model) use ($defaultOrgCode) {
                        $params = [
                            'momentus/update',
                            'id' => $model->id,
                            'return_url' => Yii::$app->request->url,
                        ];
                        if ($defaultOrgCode !== '') {
                            $params['org_code'] = $defaultOrgCode;
                        }
                        return Html::a('Edit', Url::to($params), ['class' => 'btn btn-xs btn-primary']);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
