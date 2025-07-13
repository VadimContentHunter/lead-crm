<?php

    $sum = !empty($sum) && is_numeric($sum) ? $sum : 0;
    $txid = $txid ?? '';
    $leadId = isset($leadId) && is_numeric($leadId) ? (int)$leadId : 0;
?>

<section class="component-wrapper">
    <h2>Общие сведения Deposit</h2>
    <div class="edit-deposit-form" deposit-form-id>
        <input type="text" name="leadId" value="<?= $leadId ?>" hidden>
        <div class="form-messages-container">
            <!-- <div class="form-message">
                <p>Введите данные, что бы создать лида.</p>
            </div> -->
        </div>
        <form class="form-stretch component">
            <div class="form-group">
                <label>Drain Amount</label>
                <input type="number" name="sum" step="0.01" value="<?= $sum ?>">
            </div>

            <div class="form-group">
                <label>TxID</label>
                <input type="text" name="tx_id" value="<?= $txid ?>">
            </div>

            <div class="form-actions">
                <button type="button" class="form-button submit">Сохранить</button>
                <button type="reset" class="form-button">Сбросить</button>
            </div>
        </form>
    </div>
</section>
