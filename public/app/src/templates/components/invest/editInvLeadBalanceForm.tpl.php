<?php

    $current = isset($current) && is_numeric($current) ? (float)$current : 0.0;
    $deposit = isset($deposit) && is_numeric($deposit) ? (float)$deposit : 0.0;
    $potential = isset($potential) && is_numeric($potential) ? (float)$potential : 0.0;
    $active = isset($active) && is_numeric($active) ? (float)$active : 0.0;
    $leadId = isset($leadId) && is_numeric($leadId) ? (int)$leadId : 0;
    $id = isset($id) && is_numeric($id) ? (int)$id : 0;
?>

<section class="component-wrapper">
    <h2>Общие сведения Balance</h2>
    <div class="edit-balance-form" balance-form-id>
        <input type="text" name="leadId" value="<?= $leadId ?>" hidden>
        <div class="form-messages-container">
            <!-- <div class="form-message">
                <p>Введите данные, что бы создать лида.</p>
            </div> -->
        </div>
        <form class="form-stretch component">
            <div class="form-group">
                <label>Current</label>
                <input type="number" name="current" step="0.01" value="<?= $current; ?>">
            </div>

            <div class="form-group">
                <label>Deposit</label>
                <input type="number" name="deposit" step="0.01" value="<?= $deposit; ?>">
            </div>

            <div class="form-group">
                <label>Potential</label>
                <input type="number" name="potential" step="0.01" value="<?= $potential; ?>">
            </div>

            <div class="form-group">
                <label>Active</label>
                <input type="number" name="active" step="0.01" value="<?= $active; ?>">
            </div>

            <div class="form-actions">
                <button type="button" class="form-button submit">Сохранить</button>
                <button type="reset" class="form-button">Сбросить</button>
            </div>
        </form>
    </div>
</section>
