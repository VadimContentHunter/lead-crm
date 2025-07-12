import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';

const endPoint = '/api/leads';

function attachDeleteTrigger() {
    ComponentFunctions.attachDeleteTrigger({
        triggerSelector: '[table-r-id="lead-table-1"] .btn-delete.btn-row-table',
        method: 'lead.delete',
        endpoint: endPoint,
        onData: (payload) => {
            ComponentFunctions.replaceLeadTable(payload, '[table-r-id="lead-table-1"]');
        },
    });
}

function attachInputButtonTrigger() {
    ComponentFunctions.attachInputButtonTrigger({
        containerSelector: '[table-r-id="lead-table-1"]',
        buttonSelector: 'td .edit-row-button',
        inputSelector: 'input.edit-row-input',
        searchRootSelector: 'td', // можно заменить на 'td' или '.row-block'
        attributes: ['value', 'data-row-id'],
        method: 'lead.edit.cell',
        endpoint: endPoint,
    });
}

function watchInputValueChange() {
    ComponentFunctions.watchInputValueChange({
        inputSelector: '[table-r-id="lead-table-1"] input.edit-row-input',
        onChange: (oldValue, newValue, inputElement) => {
            const container = inputElement.closest('td');
            const wrapper = container?.querySelector('.cell-actions-wrapper');
            const inputOldValue = inputElement.getAttribute("old-value") ?? null;
            if (inputOldValue !== newValue && wrapper) {
                wrapper.style.display = 'flex';
            } else if (inputOldValue === newValue && wrapper) {
                wrapper.style.display = '';
            }
        },
        onBlur: (inputElement, previous) => {
            const container = inputElement.closest('td');
            const wrapper = container?.querySelector('.cell-actions-wrapper');
            // inputElement.classList.add('activated');
            const oldValue = inputElement.getAttribute("old-value") ?? null;
            if (oldValue !== null) {
                inputElement.value = oldValue;
                previous.value = oldValue;
            }
            if (wrapper) {
                wrapper.style.display = '';
            }
        }
    });


}

// Первый запуск для уже существующих элементов
attachDeleteTrigger();
attachInputButtonTrigger();
watchInputValueChange();

// Следим за изменениями в контейнере [table-r-id]
const targetNode = document.querySelector('[table-r-id="lead-table-1"]');
if (!targetNode) {
    console.warn('Container [table-r-id="lead-table-1"] not found');
} else {
    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                attachDeleteTrigger();
                attachInputButtonTrigger();
                watchInputValueChange();
                break; // один раз достаточно
            }
        }
    });

    observer.observe(targetNode, {
        childList: true,
        subtree: true
    });
}