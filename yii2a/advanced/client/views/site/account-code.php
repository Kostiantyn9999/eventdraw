<?php

/* @var $this yii\web\View */
/* @var $client common\models\Client */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Account Code';
$this->params['breadcrumbs'][] = $this->title;

$saveUrl       = Url::to(['/site/ajax-set-account-code']);
$orgNameUrl    = Url::to(['/site/ajax-get-org-name']);

$currentCode   = $client->momentusOrgCode ?? '';
$effectiveCode = $client->getMomentusOrgCode();
$inputValue    = $currentCode !== '' ? $currentCode : ($effectiveCode !== '' ? $effectiveCode : '10');
?>

<div class="site-account-code">

    <h1>Account Code</h1>

    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-top:20px;">
        <label for="account-code-input" style="margin:0; white-space:nowrap;">Account Code</label>
        <input type="text"
               id="account-code-input"
               class="form-control"
               value="<?= Html::encode($inputValue) ?>"
               placeholder="e.g. 10"
               maxlength="50"
               style="width:120px;">
        <button id="account-code-save-btn" class="btn btn-primary">Apply</button>
        <span id="account-code-org-name" style="font-weight:600;"></span>
    </div>
    <div id="account-code-status" style="margin-top:6px; font-size:13px;"></div>

</div>

<?php
$js = <<<JS
(function () {
    var input      = document.getElementById('account-code-input');
    var btn        = document.getElementById('account-code-save-btn');
    var status     = document.getElementById('account-code-status');
    var orgNameEl  = document.getElementById('account-code-org-name');
    var orgNameUrl = '$orgNameUrl';
    var saveUrl    = '$saveUrl';

    function lookupOrgName(code) {
        orgNameEl.textContent = '';
        if (!code) { return; }
        $.get(orgNameUrl, { code: code }).done(function (resp) {
            if (resp && resp.name) {
                orgNameEl.textContent = resp.name;
            }
        });
    }

    // Show org name for the pre-filled value on page load
    lookupOrgName(input.value.trim());

    btn.addEventListener('click', function () {
        var code = input.value.trim();
        status.innerHTML = '';
        btn.disabled = true;

        $.post(saveUrl, { account_code: code })
            .done(function (resp) {
                if (resp && resp.ok) {
                    status.style.color = '#3c763d';
                    status.innerHTML = 'Account Code saved.';
                    lookupOrgName(resp.account_code || code);
                } else {
                    status.style.color = '#a94442';
                    status.innerHTML = 'Save failed. Please try again.';
                }
            })
            .fail(function () {
                status.style.color = '#a94442';
                status.innerHTML = 'Server error. Please try again.';
            })
            .always(function () {
                btn.disabled = false;
            });
    });

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { btn.click(); }
    });
})();
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>
