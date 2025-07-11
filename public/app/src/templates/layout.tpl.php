<?php

$head = $head ?? '';
$main_container = $main_container ?? '';

// Массив обычных скриптов
$scripts = $scripts ?? [];

// Массив модульных скриптов
$module_scripts = $module_scripts ?? [];

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <?= $head ?>
</head>

<body>
    <?= $main_container ?>

    <!-- Обычные скрипты -->
    <?php foreach ($scripts as $script) : ?>
        <script src="<?= htmlspecialchars($script) ?>"></script>
    <?php endforeach; ?>

    <!-- Модульные скрипты -->
    <?php foreach ($module_scripts as $moduleScript) : ?>
        <script type="module" src="<?= htmlspecialchars($moduleScript) ?>"></script>
    <?php endforeach; ?>
</body>

</html>
