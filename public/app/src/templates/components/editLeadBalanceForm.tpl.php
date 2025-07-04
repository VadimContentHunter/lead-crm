<?php

    $current = is_numeric($current ?? 0) ? (float)$current : 0.0;
    $drain = is_numeric($drain ?? 0) ? (float)$drain : 0.0;
    $potential = is_numeric($potential ?? 0) ? (float)$potential : 0.0;
    $leadId = is_numeric($leadId ?? 0) ? (int)$leadId : 0;
    $id = is_numeric($id ?? 0) ? (int)$id : 0;
?>

<section class="component component--medium">
    <h2>Общие сведения Balance</h2>
    <form class="base-form edit-balance-form" balance-form-id>
        <input type="text" name="leadId" value="<?= $leadId ?? '' ?>" hidden>
        <div class="form-messages-container">
            <!-- <div class="form-message">
                <p>Введите данные, что бы создать лида.</p>
            </div> -->
        </div>
        <form class="base-form">
            <div class="form-group">
                <label >Current</label>
                <input type="number" name="current" step="0.01" value="<?= $current; ?>">
            </div>

            <div class="form-group">
                <label >Drain</label>
                <input type="number" name="drain" step="0.01" value="<?= $drain; ?>">
            </div>

            <div class="form-group">
                <label >Potential</label>
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
