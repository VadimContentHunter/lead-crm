<?php

    $current = is_int($current ?? 0) ? $current : 0;
    $drain = is_int($drain ?? 0) ? $drain : 0;
    $potential = is_int($potential ?? 0) ? $potential : 0;
?>

<section class="component component--medium">
    <h2>Общие сведения Balance</h2>
    <form class="base-form edit-balance-form" balance-form-id>
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
        method: 'balance.edit',
        endpoint: '/api/balances'
    });

    ComponentFunctions.attachJsonRpcInputTrigger({
        triggerSelector: '.edit-balance-form[balance-form-id] .form-actions .update',
        containerSelector: '.edit-balance-form[balance-form-id]',
        method: 'balance.edit',
        endpoint: '/api/balances'
    });

</script>