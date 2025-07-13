<?php
$comments = isset($comments) && is_array($comments) ? $comments : [];
$leadId = isset($leadId) && is_numeric($leadId) ? (int)$leadId : 0;
?>
<section class="component-wrapper-line">

    <section class="component component--full" style="max-width: 1000px;">
        <h2>История / комментарии</h2>

        <!-- Список существующих комментариев -->
        <div class="comments-list">
            <?php if (empty($comments)) : ?>
                <p class="comment-placeholder">Комментариев пока нет.</p>
            <?php else : ?>
                <?php foreach ($comments as $comment) : ?>
                    <div class="comment-item">
                        <p class="comment-text">
                            <?= nl2br(htmlspecialchars($comment ?? '')); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Форма для добавления нового комментария -->
        <form class="form-stretch comment-form" comment-form-id>
            <input type="text" name="lead_id" value="<?= $leadId ?>" hidden>
            <div class="form-group">
                <label>Оставить комментарий</label>
                <textarea name="comment" rows="4" placeholder="Введите текст комментария..."></textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="form-button submit">Отправить</button>
                <button type="button" class="form-button update">Обновить</button>
            </div>
        </form>

    </section>

</section>
