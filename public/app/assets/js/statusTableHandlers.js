import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';

const endPoint = '/api/statuses';

//
// === Удаление строки из таблицы по кнопке ===
//
function attachDeleteTrigger() {
    ComponentFunctions.attachDeleteTrigger({
        triggerSelector: '[table-r-id="status-table-1"] .btn-delete.btn-row-table',
        method: 'status.delete',
        endpoint: endPoint,
        onData: (payload) => {
            ComponentFunctions.replaceTable(payload, '[table-r-id="status-table-1"]');
            if (payload?.messages?.length > 0) {
                ComponentFunctions.processMessagesArray(payload.messages);
            } else if (Array.isArray(payload)) {
                const message = { message: payload[0].message, type: payload[0].type }
                ComponentFunctions.processMessagesArray([message]);
            }
        },
    });
}

//
// === Редактирование значения ячейки по кнопке внутри строки ===
//
function attachInputButtonTrigger() {
    ComponentFunctions.attachInputButtonTrigger({
        containerSelector: '[table-r-id="status-table-1"]',
        buttonSelector: 'td .edit-row-button',
        inputSelector: 'input.edit-row-input',
        searchRootSelector: 'td',
        attributes: ['value', 'data-row-id'],
        method: 'status.edit.cell',
        endpoint: endPoint,
    });
}

//
// === Отслеживание изменения значения в input'ах таблицы ===
//
function watchInputValueChange() {
    ComponentFunctions.watchInputValueChange({
        inputSelector: '[table-r-id="status-table-1"] input.edit-row-input',
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

//
// === Инициализация обработчиков для уже существующих элементов таблицы ===
//
attachDeleteTrigger();
attachInputButtonTrigger();
watchInputValueChange();

//
// === Наблюдение за появлением новых элементов в DOM и повторное навешивание обработчиков ===
//
const targetNode = document.querySelector('[table-r-id="status-table-1"]');
if (!targetNode) {
    console.warn('Container [table-r-id="status-table-1"] not found');
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