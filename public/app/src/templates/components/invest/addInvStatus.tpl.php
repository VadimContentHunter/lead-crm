<?php

?>

<section class="component-wrapper-line">

    <section class="component-wrapper-side-bar">
        <form class="base-form component" id="add-inv-status-form">
            <div class="form-messages-container">
                <div class="form-message">
                    <p>Введите название статуса.</p>
                </div>
            </div>

            <div class="form-group">
                <label for="title">Название статуса</label>
                <input type="text" name="label" placeholder="Введите название">
            </div>

            <div class="form-group">
                <label for="code">Код статуса</label>
                <p>Например: <code>work</code>, <code>done</code>, <code>fail_1</code> </p>
                <input type="text" name="code" placeholder="Введите код">
            </div>

            <div class="form-actions">
                <button type="button" class="form-button submit">Добавить</button>
                <button type="reset" class="form-button">Сбросить</button>
            </div>
        </form>
    </section>
</section>
