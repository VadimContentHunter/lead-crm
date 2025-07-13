import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';

const endPoint = '/api/leads';


//
// === Добавление строки в таблицу по кнопке ===
//
ComponentFunctions.attachJsonRpcInputTrigger({
    triggerSelector: '#add-lead-form .form-actions .form-button.submit',
    containerSelector: '#add-lead-form',
    method: 'lead.add',
    endpoint: endPoint,
    callbackOnData: (payload) => {
        ComponentFunctions.replaceTable(payload, '[table-r-id="lead-table-1"]');
    }
});

//
// === Удаление строки из таблицы по кнопке ===
//
function attachDeleteTriggerLead() {
    ComponentFunctions.attachDeleteTrigger({
        triggerSelector: '[table-r-id="lead-table-1"] .btn-delete.btn-row-table',
        method: 'lead.delete',
        endpoint: endPoint,
        callbackOnData: (payload) => {
            ComponentFunctions.replaceTable(payload, '[table-r-id="lead-table-1"]');
            if (payload?.messages?.length > 0) {
                ComponentFunctions.processMessagesArray(payload.messages);
            }
        },
    });
}

//
// === Редактирование значения ячейки по кнопке внутри строки ===
//
function attachInputButtonTriggerLead() {
    ComponentFunctions.attachInputButtonTrigger({
        containerSelector: '[table-r-id="lead-table-1"]',
        buttonSelector: 'td .edit-row-button',
        inputSelector: 'input.edit-row-input',
        searchRootSelector: 'td',
        attributes: ['value', 'data-row-id'],
        method: 'lead.edit.cell',
        endpoint: endPoint,
    });
}

//
// === Отслеживание изменения значения в input'ах таблицы ===
//
function watchInputValueChangeLead() {
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
attachDeleteTriggerLead();
attachInputButtonTriggerLead();
watchInputValueChangeLead();

//
// === Наблюдение за появлением новых элементов в DOM и повторное навешивание обработчиков ===
//
const targetNode = document.querySelector('[table-r-id="lead-table-1"]');
if (!targetNode) {
    console.warn('Container [table-r-id="lead-table-1"] not found');
} else {
    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                attachDeleteTriggerLead();
                attachInputButtonTriggerLead();
                watchInputValueChangeLead();
                break; // один раз достаточно
            }
        }
    });

    observer.observe(targetNode, {
        childList: true,
        subtree: true
    });
}