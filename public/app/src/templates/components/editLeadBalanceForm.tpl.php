<?php

    $current = isset($current) && is_numeric($current) ? (float)$current : 0.0;
    $drain = isset($drain) && is_numeric($drain) ? (float)$drain : 0.0;
    $potential = isset($potential) && is_numeric($potential) ? (float)$potential : 0.0;
    $leadId = isset($leadId) && is_numeric($leadId) ? (int)$leadId : 0;
    $id = isset($id) && is_numeric($id) ? (int)$id : 0;
?>

<section class="component-wrapper">
    <h2>Общие сведения Balance</h2>
    <form class="base-form edit-balance-form" balance-form-id>
        <input type="text" name="leadId" value="<?= $leadId ?>" hidden>
        <div class="form-messages-container">
            <!-- <div class="form-message">
                <p>Введите данные, что бы создать лида.</p>
            </div> -->
        </div>
        <form class="base-form component">
            <div class="form-group">
                <label>Current</label>
                <input type="number" name="current" step="0.01" value="<?= $current; ?>">
            </div>

            <div class="form-group">
                <label>Drain</label>
                <input type="number" name="drain" step="0.01" value="<?= $drain; ?>">
            </div>

            <div class="form-group">
                <label>Potential</label>
                <input type="number" name="potential" step="0.01" value="<?= $potential; ?>">
            </div>

            <div class="form-actions">
                <button type="button" class="form-button submit">Сохранить</button>
                <button type="reset" class="form-button">Сбросить</button>
            </div>
        </form>
</section>

<script type="module">
    import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
                        
    ComponentFunctions.attachJsonRpcInputTrigger({
        triggerSelector: '.edit-balance-form[balance-form-id] .form-actions .submit',
        containerSelector: '.edit-balance-form[balance-form-id]',
        method: 'balance.create.edit',
        endpoint: '/api/balances'
    });

</script>
