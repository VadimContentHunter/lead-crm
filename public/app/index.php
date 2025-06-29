<?php
// Получаем название хоста
$host = $_SERVER['HTTP_HOST'] ?? 'неизвестно';

// Получаем путь (URI без параметров)
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

echo "<h3>Информация о запросе:</h3>";
echo "<p><strong>Хост:</strong> " . htmlspecialchars($host) . "</p>";
echo "<p><strong>Путь:</strong> " . htmlspecialchars($uri) . "</p>";

if (!empty($_GET)) {
    echo "<h3>GET параметры:</h3><ul>";
    foreach ($_GET as $key => $value) {
        echo "<li><strong>" . htmlspecialchars($key) . "</strong>: " . htmlspecialchars($value) . "</li>";
    }
    echo "</ul>";
} else {
    echo "<p>GET параметры отсутствуют.</p>";
}

// phpinfo();
