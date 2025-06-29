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

        // ⚠️ Вызов warning — обращение к несуществующему ключу массива
        // $data = ['name' => 'Alice'];
        // $undefined = $data['age']; // Этого ключа нет — будет E_WARNING

        // ⚠️ Вызов warning — обращение к несуществующему ключу массива
        // $data = ['name' => 'Alice'];
        // $undefined = $data['age']; // Этого ключа нет — будет E_WARNING

        // ⚠️ Вызов ошибки — вызов метода у null
        // $obj = null;
        // $obj->someMethod();  // Fatal error: Call to a member function on null

        // $obj2 = null;
        // $obj2->someMethod();  // Fatal error: Call to a member function on null

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
