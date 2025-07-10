<?php

    $message = $message ?? '';
?>

<section class="component-wrapper-line">
    <section class="component-wrapper">
            <div class="error-component">
                <div class="error-code">403</div>
                <div class="error-message">У вас нет доступа к этой странице.</div>
                <br>
                <hr>
                <div class="error-message error-description"><i>Описание: <?= $message ?></i></div>
            </div>
    </section>
</section>
