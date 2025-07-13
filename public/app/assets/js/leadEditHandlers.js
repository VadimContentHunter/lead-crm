import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
import { JsonRpcTransport } from '/assets/js/JsonRpcTransport.js';


ComponentFunctions.attachJsonRpcInputTrigger({
    triggerSelector: '.edit-balance-form[balance-form-id] .form-actions .submit',
    containerSelector: '.edit-balance-form[balance-form-id]',
    method: 'balance.create.edit',
    endpoint: '/api/balances'
});

ComponentFunctions.attachJsonRpcInputTrigger({
    triggerSelector: '.edit-deposit-form[deposit-form-id] .form-actions .submit',
    containerSelector: '.edit-deposit-form[deposit-form-id]',
    method: 'deposit.create.edit',
    endpoint: '/api/deposits'
});

ComponentFunctions.attachJsonRpcInputTrigger({
    triggerSelector: '.edit-lead-form[lead-form-id] .form-actions .submit',
    containerSelector: '.edit-lead-form[lead-form-id]',
    method: 'lead.edit',
    endpoint: '/api/leads'
});

//
// === Комментарии ===
//

function updateComments(response) {
    const commentsBlock = document.querySelector('.comments-list');

    if (!commentsBlock) {
        console.warn('[Comment] Блок .comments-list не найден');
        return;
    }

    // Очищаем текущее содержимое
    commentsBlock.innerHTML = '';

    // Ищем первый элемент с type: success и comments: [...]
    const successBlock = Array.isArray(response) ?
        response.find(item => item.type === 'success' && Array.isArray(item.comments)) :
        null;

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

ComponentFunctions.attachJsonRpcInputTrigger({
    triggerSelector: '.comment-form[comment-form-id] .form-actions .update',
    containerSelector: '.comment-form[comment-form-id]',
    method: 'comment.get.all',
    endpoint: '/api/comments',
    callbackOnData: updateComments
});

ComponentFunctions.attachJsonRpcInputTrigger({
    triggerSelector: '.comment-form[comment-form-id] .form-actions .submit',
    containerSelector: '.comment-form[comment-form-id]',
    method: 'comment.add',
    endpoint: '/api/comments',
    callbackOnData: (response) => {
        const transport = new JsonRpcTransport('comment.get.all', {
            endpoint: '/api/comments',
            onContentUpdate: () => { },
            onData: (payload) => {
                updateComments(payload);
            },
            onError: (error) => {
                if (typeof callbackOnError === 'function') {
                    callbackOnError(error);
                } else {
                    console.error('[JsonRpcTransport] Ошибка:', error.message);
                }
            }
        });

        transport.sendFromSelectorInputs('.comment-form[comment-form-id]');
    }
});