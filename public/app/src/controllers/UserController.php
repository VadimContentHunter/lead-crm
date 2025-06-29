<?php
namespace crm\src\controllers;

class UserController
{
    public function view(int $id, string $role)
    {
        // Выводим основные параметры, пришедшие из маршрутизации
        echo "<h1>UserController::view вызван</h1>";
        echo "<p><strong>id:</strong> " . htmlspecialchars((string)$id) . "</p>";
        echo "<p><strong>role:</strong> " . htmlspecialchars($role) . "</p>";

        // Выводим GET-параметры после ?
        if (!empty($_GET)) {
            echo "<h2>GET-параметры:</h2>";
            echo "<ul>";
            foreach ($_GET as $key => $value) {
                echo "<li><strong>" . htmlspecialchars($key) . ":</strong> " . htmlspecialchars((string)$value) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>GET-параметры отсутствуют.</p>";
        }
    }
}
