<?php

    $leads = $leads ?? [];
    $types = $types ?? [];
    $pairs = $pairs ?? [];
    $directions = $directions ?? [];
?>

<section class="component-wrapper-line">
    <section class="component-wrapper"  id="add-inv-activity-form">
            <div class="form-messages-container">
                <div class="form-message">
                    <p>Введите данные, что бы создать Активность.</p>
                </div>
            </div>
            <form class="form-stretch component">
                <div class="form-group">
                    <label>Выберите лида</label>
                    <select name="lead_uid">
                        <option value="">Выберите...</option>
                        <?php foreach ($leads as $lead) : ?>
                            <option value="<?= htmlspecialchars($lead['id'] ?? '0') ?>">
                                <?= htmlspecialchars($lead['title'] ?? 'error') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Выберите тип сделки</label>
                    <select name="type">
                        <option value="">Выберите...</option>
                        <?php foreach ($types as $type) : ?>
                            <option value="<?= htmlspecialchars($type['id'] ?? '0') ?>">
                                <?= htmlspecialchars($type['title'] ?? 'error') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Введите валютную пару</label>
                    <input name="pair">
                    <!-- <select name="pair">
                        <option value="">Выберите...</option>
                        <?php foreach ($pairs as $pair) : ?>
                            <option value="<?= htmlspecialchars($pair['id'] ?? '0') ?>">
                                <?= htmlspecialchars($pair['title'] ?? 'error') ?>
                            </option>
                        <?php endforeach; ?>
                    </select> -->
                </div>

                <div class="form-group">
                    <label>Объём сделки</label>
                    <input type="number" name="amount">
                </div>

                <div class="form-group">
                    <label>Выберите направление</label>
                    <select name="direction">
                        <option value="">Выберите...</option>
                        <?php foreach ($directions as $direction) : ?>
                            <option value="<?= htmlspecialchars($direction['id'] ?? '0') ?>">
                                <?= htmlspecialchars($direction['title'] ?? 'error') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Цена при открытии</label>
                    <input type="number" name="open_price">
                </div>

                <div class="form-group">
                    <label>Цена при закрытии (если есть)</label>
                    <input type="number" name="close_price">
                </div>

                <div class="form-actions">
                    <button class="form-button submit">Сохранить</button>
                    <button type="reset" class="form-button">Сбросить</button>
                </div>
            </form>
    </section>
</section>
