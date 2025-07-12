<?php

$asides = $asides ?? [];

foreach ($asides as $aside) {
    $content = $aside['content'] ?? '';
    $classNameId = $aside['classNameId'] ?? '';

    echo <<<HTML
        <aside class = "right-sidebar {htmlspecialchars($classNameId)}" >
            {htmlspecialchars($content)}
        </aside>
    HTML;
}
