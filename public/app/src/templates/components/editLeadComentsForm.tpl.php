<?php
$comments = is_array($comments ?? []) ? $comments : [];
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
        <form class="base-form comment-form" comment-form-id>
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

<script type="module">
    import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
                        
    ComponentFunctions.attachJsonRpcInputTrigger({
        triggerSelector: '.comment-form[comment-form-id] .form-actions .submit',
        containerSelector: '.comment-form[comment-form-id]',
        method: 'comment.edit',
        endpoint: '/api/comments'
    });

    ComponentFunctions.attachJsonRpcInputTrigger({
        triggerSelector: '.comment-form[comment-form-id] .form-actions .update',
        containerSelector: '.comment-form[comment-form-id]',
        method: 'comment.update',
        endpoint: '/api/comments'
    });

</script>
