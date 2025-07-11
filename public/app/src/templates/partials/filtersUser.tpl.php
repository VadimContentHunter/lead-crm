<?php

$sortColumns = $sortColumns ?? [];
$selectedData = $selectedData ?? [];

// Поисковая строка
$search = $selectedData['search'] ?? '';
?>


<form class="base-form user-filter" user-filter-id>
    <div class="form-group">
        <label>Поиск</label>
        <input type="text" name="search" placeholder="Поиск по имени или контакту">
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
        <button type="button" class="btn-default button-primary submit">Применить</button>
        <button type="reset" class="btn-default button-secondary reset">Сбросить</button>я
    </div>
</form>

<script type="module">
    import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
                        
    ComponentFunctions.attachJsonRpcInputTrigger({
        triggerSelector: '.user-filter[user-filter-id] .form-actions .submit',
        containerSelector: '.user-filter[user-filter-id]',
        method: 'user.filter.table',
        endpoint: '/api/users',
        callbackOnData: (response) => {
            ComponentFunctions.replaceLeadTable(response, '[table-r-id]');
        }
    });

    ComponentFunctions.attachJsonRpcInputTrigger({
        triggerSelector: '.user-filter[user-filter-id] .form-actions .reset',
        containerSelector: '.user-filter[user-filter-id]',
        method: 'user.filter.table.clear',
        endpoint: '/api/users',
        callbackOnData: (response) => {
            ComponentFunctions.replaceLeadTable(response, '[table-r-id]');
        },
    });

</script>
