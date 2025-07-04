<?php

$sortColumns = $sortColumns ?? [];
$sourcesList = $sourcesList ?? [];
$statusesList = $statusesList ?? [];
$managersList = $managersList ?? [];
$selectedData = $selectedData ?? [];

// Числовые фильтры (по умолчанию null или 0)
$potentialMin = $selectedData['potentialMin'] ?? null;
$balanceMin = $selectedData['balanceMin'] ?? null;
$drainMin = $selectedData['drainMin'] ?? null;

// Сортировка и направление (по умолчанию пустые строки)
$sort = $selectedData['sort'] ?? '';
$dir = $selectedData['dir'] ?? 'asc';

// Поисковая строка
$search = $selectedData['search'] ?? '';
?>

<!-- <th>ID</th>
<th>Contact</th>
<th>Name</th>
<th>Manager</th>
<th>Status</th>
<th>Source</th>
<th>Potential</th>
<th>Balance</th>
<th>Drained</th>
<th>Address</th>
<th>Edit</th>
<th>Drain</th> -->


<form class="base-form lead-filter" lead-filter-id>
    <div class="form-group">
        <label>Поиск</label>
        <input type="text" name="search" placeholder="Поиск по имени или контакту">
    </div>

    <div class="form-group">
        <label>Менеджер</label>
        <select name="managerId">
            <option value="">Все менеджеры</option>
            <?php foreach ($managersList as $manager) : ?>
                <option <?= (isset($selectedData['accountManagerId'])
                            && (int)$selectedData['accountManagerId'] === (int)($manager['id'] ?? 0)
                            ) ? 'selected' : ''
                        ?>
                    value="<?= htmlspecialchars($manager['id'] ?? '0') ?>">
                    <?= htmlspecialchars($manager['login'] ?? 'error') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Статус</label>
        <select name="statusId">
            <option value="">Все статусы</option>
            <?php foreach ($statusesList as $status) : ?>
                <option <?= (isset($selectedData['statusId'])
                            && (int)$selectedData['statusId'] === (int)($status['id'] ?? 0)
                            ) ? 'selected' : ''
                        ?>
                    value="<?= htmlspecialchars($status['id'] ?? '0') ?>">
                    <?= htmlspecialchars($status['title'] ?? 'error') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Источник</label>
        <select name="sourceId">
            <option value="">Все источники</option>
            <?php foreach ($sourcesList as $source) : ?>
                <option <?= (isset($selectedData['sourceId'])
                            && (int)$selectedData['sourceId'] === (int)($source['id'] ?? 0)
                            ) ? 'selected' : ''
                        ?>
                    value="<?= htmlspecialchars($source['id'] ?? '0') ?>">
                    <?= htmlspecialchars($source['title'] ?? 'error') ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label>Potential min</label>
        <input type="number" name="potentialMin" step="0.01" placeholder="Минимум">
    </div>

    <div class="form-group">
        <label>Текущий min</label>
        <input type="number" name="balanceMin" step="0.01" placeholder="Минимум">
    </div>

    <div class="form-group">
        <label>Потери min</label>
        <input type="number" name="drainMin" step="0.01" placeholder="Минимум">
    </div>

    <div class="form-group">
        <label>Сортировка</label>
        <select name="sort">
            <option value="">По умолчанию</option>
            <?php foreach ($sortColumns as $column) : ?>
                <option <?= (isset($selectedData['sortColumn'])
                            && (int)$selectedData['sortColumn'] === (int)($column ?? 0)
                            ) ? 'selected' : ''
                        ?>
                    value="<?= htmlspecialchars($column ?? '0') ?>">
                    <?= htmlspecialchars($column ?? 'error') ?>
                </option>
            <?php endforeach; ?>
            <!-- Добавь поля сортировки -->
        </select>
    </div>

    <div class="form-group">
        <label>Направление</label>
        <select name="dir">
            <option value="asc">По возрастанию</option>
            <option value="desc">По убыванию</option>
        </select>
    </div>

    <div class="form-actions">
        <button type="button" class="form-button submit">Применить</button>
        <button type="reset" class="form-button reset">Сбросить</button>
    </div>
</form>

<script type="module">
    import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
                        
    ComponentFunctions.attachJsonRpcInputTrigger({
        triggerSelector: '.lead-filter[lead-filter-id] .form-actions .submit',
        containerSelector: '.lead-filter[lead-filter-id]',
        method: 'lead.filter.table',
        endpoint: '/api/leads',
        callbackOnData: (response) => {
            ComponentFunctions.replaceLeadTable(response, '[table-r-id]');
        }
    });

    ComponentFunctions.attachJsonRpcInputTrigger({
        triggerSelector: '.lead-filter[lead-filter-id] .form-actions .reset',
        containerSelector: '.lead-filter[lead-filter-id]',
        method: 'lead.filter.table.clear',
        endpoint: '/api/leads',
        callbackOnData: (response) => {
            ComponentFunctions.replaceLeadTable(response, '[table-r-id]');
        },
    });

</script>
