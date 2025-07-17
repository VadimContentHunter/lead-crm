<?php

    $sourcesTitle = $sourcesTitle ?? [];
    $statusesTitle = $statusesTitle ?? [];
    $managersLogin = $managersLogin ?? [];
    $selectedData = $selectedData ?? [];
    $fullName = $fullName ?? '';
    $contact = $contact ?? '';
    $address = $address ?? '';
    $leadId = $leadId ?? '';
    $phone = $phone ?? '';
    $email = $email ?? '';
?>

<section class="component-wrapper">
    <h2>Общие сведения лида</h2>
    <div class="edit-lead-form" id="inv-lead-form-1">
        <input type="text" name="leadId" value="<?= $leadId ?>" hidden>
        <div class="form-messages-container">
            <div class="form-message">
                <p>Введите данные, что бы создать лида.</p>
            </div>
        </div>
        <form class="form-stretch component">
            <div class="form-group">
                <label>Полное имя</label>
                <input type="text" name="full_name">
            </div>

            <div class="form-group">
                <label>Контакты *</label>
                <input type="text" name="contact">
            </div>

            <div class="form-group">
                <label>Номер телефона</label>
                <input type="text" name="phone">
            </div>

            <div class="form-group">
                <label>Электронная почта</label>
                <input type="mail" name="email">
            </div>

            <div class="form-group">
                <label>Выбор источника</label>
                <select name="source_id">
                    <option value="">Выберите...</option>
                    <?php foreach ($sourcesTitle as $source) : ?>
                    <option value="<?= htmlspecialchars($source['id'] ?? '0') ?>">
                        <?= htmlspecialchars($source['title'] ?? 'error') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="form-group">
                <label>Выбор статусов</label>
                <select name="status_id">
                    <option value="">Выберите...</option>
                    <?php foreach ($statusesTitle as $source) : ?>
                    <option value="<?= htmlspecialchars($source['id'] ?? '0') ?>">
                        <?= htmlspecialchars($source['title'] ?? 'error') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Выбор менеджера</label>
                <select name="account_manager_id">
                    <option value="">Выберите...</option>
                    <?php foreach ($managersLogin as $manager) : ?>
                    <option value="<?= htmlspecialchars($manager['id'] ?? '0') ?>">
                        <?= htmlspecialchars($manager['login'] ?? 'error') ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button class="form-button submit">Сохранить</button>
                <button type="reset" class="form-button">Сбросить</button>
            </div>
        </form>
    </div>
</section>