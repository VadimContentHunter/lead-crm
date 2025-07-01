<?php

?>

<section class="component component--medium">
    <form class="base-form">
        <div class="form-group">
            <label for="text-input">Текст</label>
            <input type="text" id="text-input" name="text">
        </div>

        <div class="form-group">
            <label for="number-input">Число</label>
            <input type="number" id="number-input" name="number">
        </div>

        <div class="form-group">
            <label for="password-input">Пароль</label>
            <input type="password" id="password-input" name="password">
        </div>

        <div class="form-group">
            <label for="select-input">Выбор</label>
            <select id="select-input" name="select">
                <option value="">Выберите...</option>
                <option value="1">Вариант 1</option>
                <option value="2">Вариант 2</option>
            </select>
        </div>

        <div class="form-actions">
            <button class="form-button submit">Сохранить</button>
            <button type="reset" class="form-button">Сбросить</button>
        </div>
    </form>
</section>
