<?php

$asides = [
    ['content' => 'Контент первого блока', 'classNameId' => 'first-class'],
    ['content' => 'Контент второго блока', 'classNameId' => 'second-class'],
    ['content' => 'Контент третьего блока', 'classNameId' => 'third-class'],
    // Добавляйте сюда другие элементы массива по необходимости
];

foreach ($asides as $aside) {
    $content = $aside['content'] ?? '';
    $classNameId = $aside['classNameId'] ?? '';

    echo <<<HTML
        <aside class = "right-sidebar {htmlspecialchars($classNameId)}" >
            {htmlspecialchars($content)}
        </aside>
    HTML;
}
