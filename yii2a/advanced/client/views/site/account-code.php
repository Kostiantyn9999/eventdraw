<?php

/* @var $this yii\web\View */
/* @var $client common\models\Client */

use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'Org Code';
$this->params['breadcrumbs'][] = $this->title;

$saveUrl          = Url::to(['/site/ajax-set-account-code']);
$orgNameUrl       = Url::to(['/site/ajax-get-org-name']);
$organizationsUrl = Url::to(['/site/ajax-get-organizations?search=OrganizationCode ne null']);

$currentCode   = $client->momentusOrgCode ?? '';
$effectiveCode = $client->getMomentusOrgCode();
$inputValue    = $currentCode !== '' ? $currentCode : ($effectiveCode !== '' ? $effectiveCode : '10');
?>

<div class="site-account-code">

    <h1>Org Code</h1>

    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-top:20px;">
        <label for="account-code-select" style="margin:0; white-space:nowrap;">Org Code</label>
        <select id="account-code-select"
                class="form-control"
                style="width:auto; min-width:280px; max-width:100%;">
            <option value="<?= Html::encode($inputValue) ?>"><?= Html::encode($inputValue) ?> — Loading…</option>
        </select>
        <button id="account-code-save-btn" class="btn btn-primary">Apply</button>
        <span id="account-code-org-name" style="font-weight:600;"></span>
    </div>
    <div id="account-code-load-status" style="margin-top:6px; font-size:13px; color:#737373;"></div>
    <div id="account-code-status" style="margin-top:6px; font-size:13px;"></div>

</div>

<?php
$js = <<<JS
(function () {
    var select       = document.getElementById('account-code-select');
    var btn          = document.getElementById('account-code-save-btn');
    var status       = document.getElementById('account-code-status');
    var loadStatus   = document.getElementById('account-code-load-status');
    var orgNameEl    = document.getElementById('account-code-org-name');
    var orgNameUrl   = '$orgNameUrl';
    var saveUrl      = '$saveUrl';
    var orgsUrl      = '$organizationsUrl';
    var currentValue = '$inputValue';

    function selectedCode() {
        return (select.value || '').trim();
    }

    function formatOptionLabel(org) {
        return org.name ? (org.code + ' — ' + org.name) : org.code;
    }

    function populateSelect(organizations) {
        var knownCodes = {};
        select.innerHTML = '';

        organizations.forEach(function (org) {
            knownCodes[org.code] = true;
            var opt = document.createElement('option');
            opt.value = org.code;
            opt.textContent = formatOptionLabel(org);
            select.appendChild(opt);
        });

        if (currentValue && !knownCodes[currentValue]) {
            var customOpt = document.createElement('option');
            customOpt.value = currentValue;
            customOpt.textContent = currentValue + ' — (current)';
            select.insertBefore(customOpt, select.firstChild);
        }

        if (currentValue) {
            select.value = currentValue;
        } else if (select.options.length) {
            select.selectedIndex = 0;
        }
    }

    function lookupOrgName(code) {
        orgNameEl.textContent = '';
        if (!code) { return; }

        var selectedOption = select.options[select.selectedIndex];
        if (selectedOption && selectedOption.textContent.indexOf(' — ') !== -1) {
            var parts = selectedOption.textContent.split(' — ');
            if (parts.length >= 2 && parts[1] !== '(current)') {
                orgNameEl.textContent = parts.slice(1).join(' — ');
                return;
            }
        }

        $.get(orgNameUrl, { code: code }).done(function (resp) {
            if (resp && resp.name) {
                orgNameEl.textContent = resp.name;
            }
        });
    }

    function loadOrganizations() {
        loadStatus.textContent = 'Loading available org codes…';
        $.get(orgsUrl).done(function (resp) {
            loadStatus.textContent = '';
            if (resp && resp.ok && resp.organizations && resp.organizations.length) {
                populateSelect(resp.organizations);
                lookupOrgName(selectedCode());
            } else {
                loadStatus.style.color = '#8a6d3b';
                loadStatus.textContent = 'Could not load org codes from Momentus. You can still apply the current value.';
                lookupOrgName(selectedCode());
            }
        }).fail(function () {
            loadStatus.style.color = '#a94442';
            loadStatus.textContent = 'Failed to load org codes. You can still apply the current value.';
            lookupOrgName(selectedCode());
        });
    }

    loadOrganizations();

    select.addEventListener('change', function () {
        lookupOrgName(selectedCode());
    });

    btn.addEventListener('click', function () {
        var code = selectedCode();
        status.innerHTML = '';
        btn.disabled = true;

        $.post(saveUrl, { account_code: code })
            .done(function (resp) {
                if (resp && resp.ok) {
                    status.style.color = '#3c763d';
                    status.innerHTML = 'Account Code saved.';
                    currentValue = resp.account_code || code;
                    lookupOrgName(currentValue);
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
})();
JS;
$this->registerJs($js, \yii\web\View::POS_END);
?>
