<?php

    $drain = is_int($drain ?? 0) ? $drain : 0;
    $txid = $txid ?? '';
?>

<section class="component component--medium">
    <h2>Общие сведения Deposit</h2>
    <form class="base-form edit-deposit-form" deposit-form-id>
        <div class="form-messages-container">
            <!-- <div class="form-message">
                <p>Введите данные, что бы создать лида.</p>
            </div> -->
        </div>
        <form class="base-form">
            <div class="form-group">
                <label for="number-input">Drain Amount</label>
                <input type="number" id="number-input" name="drain" step="0.01" value="<?= $drain; ?>">
            </div>

            <div class="form-group">
                <label for="number-input">TxID</label>
                <input type="text" name="txid" value="<?= $fullName ?? '' ?>">
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
        triggerSelector: '.edit-deposit-form[deposit-form-id] .form-actions .submit',
        containerSelector: '.edit-deposit-form[deposit-form-id]',
        method: 'deposit.edit',
        endpoint: '/api/deposits'
    });

</script>