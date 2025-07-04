<?php
$comments = is_array($comments ?? []) ? $comments : [];
$leadId = is_numeric($leadId ?? 0) ? (int)$leadId : 0;
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
            <input type="text" name="lead_id" value="<?= $leadId ?? '' ?>" hidden>
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
        method: 'comment.add',
        endpoint: '/api/comments'
    });

    ComponentFunctions.attachJsonRpcInputTrigger({
        triggerSelector: '.comment-form[comment-form-id] .form-actions .update',
        containerSelector: '.comment-form[comment-form-id]',
        method: 'comment.get.all',
        endpoint: '/api/comments',
        callbackOnData: (response) => {
            const commentsBlock = document.querySelector('.comments-list');

            if (!commentsBlock) {
                console.warn('[Comment] Блок .comments-list не найден');
                return;
            }

            // Очищаем текущее содержимое
            commentsBlock.innerHTML = '';

            // Ищем первый элемент с type: success и comments: [...]
            const successBlock = Array.isArray(response)
                ? response.find(item => item.type === 'success' && Array.isArray(item.comments))
                : null;

            const comments = successBlock ? successBlock.comments : [];

            if (comments.length === 0) {
                commentsBlock.innerHTML = '<p class="comment-placeholder">Комментариев пока нет.</p>';
                return;
            }

            for (const commentText of comments) {
                const item = document.createElement('div');
                item.className = 'comment-item';
                item.innerHTML = `<p class="comment-text">${commentText}</p>`;
                commentsBlock.appendChild(item);
            }
        }
    });

</script>
