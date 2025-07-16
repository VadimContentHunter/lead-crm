<?php

?>

<section class="component-wrapper-line">

    <section class="component-wrapper-side-bar">
        <form class="base-form component" id="add-source-form">
            <div class="form-messages-container">
                <div class="form-message">
                    <p>Введите название источника.</p>
                </div>
            </div>

            <div class="form-group">
                <label for="title">Название <i style="color:rgb(0, 100, 146)">источника</i></label>
                <input type="text" name="label" placeholder="Введите название">
            </div>

            <div class="form-group">
                <label for="title">Код <i style="color:rgb(0, 100, 146)">источника</i></label>
                <p>Например: <code>inv_1</code>, <code>bybit</code>, <code>binance</code> </p>
                <input type="text" name="code" placeholder="Введите название">
            </div>

            <div class="form-actions">
                <button type="button" class="form-button submit">Добавить</button>
                <button type="reset" class="form-button">Сбросить</button>
            </div>
        </form>
    </section>
</section>
