<?php

$sourcesList = $sourcesTitle ?? [];
$statusesList = $statusesTitle ?? [];
$managersList = $managersLogin ?? [];
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

// Выбранные значения для select-ов
$selectedManagerId = $selectedData['managerId'] ?? '';
$selectedStatusId = $selectedData['statusId'] ?? '';
$selectedSourceId = $selectedData['sourceId'] ?? '';
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

<form class="base-form lead-filter" id="lead-filter-form">
    <div class="form-group">
        <label>Поиск</label>
        <input type="text" name="search" placeholder="Поиск по имени или контакту">
    </div>

    <div class="form-group">
        <label>Менеджер</label>
        <select name="managerId">
            <option value="">Все менеджеры</option>
            <option value="1">Иван</option>
            <option value="2">Анна</option>
            <!-- Добавь свои варианты -->
        </select>
    </div>

    <div class="form-group">
        <label>Статус</label>
        <select name="statusId">
            <option value="">Все статусы</option>
            <option value="1">Новый</option>
            <option value="2">В работе</option>
            <option value="3">Закрыт</option>
            <!-- Добавь свои статусы -->
        </select>
    </div>

    <div class="form-group">
        <label>Источник</label>
        <select name="sourceId">
            <option value="">Все источники</option>
            <option value="1">Реклама</option>
            <option value="2">Звонок</option>
            <!-- Добавь свои источники -->
        </select>
    </div>

    <div class="form-group">
        <label>Potential min</label>
        <input type="number" name="potentialMin" step="0.01" placeholder="Минимум">
    </div>

    <div class="form-group">
        <label>Balance min</label>
        <input type="number" name="balanceMin" step="0.01" placeholder="Минимум">
    </div>

    <div class="form-group">
        <label>Drain min</label>
        <input type="number" name="drainMin" step="0.01" placeholder="Минимум">
    </div>

    <div class="form-group">
        <label>Сортировка</label>
        <select name="sort">
            <option value="">По умолчанию</option>
            <option value="created_at">Дата создания</option>
            <option value="full_name">Имя</option>
            <option value="potential">Potential</option>
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
        <button type="submit" class="form-button submit">Применить</button>
        <button type="reset" class="form-button reset">Сбросить</button>
    </div>
</form>
