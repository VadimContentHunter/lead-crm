import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
import { ConfirmDialog } from '/assets/js/ConfirmDialog.js';

const endPoint = '/api/users';


//
// === Добавление строки в таблицу по кнопке ===
//
ComponentFunctions.attachJsonRpcInputTrigger({
    triggerSelector: '#add-user-form .form-actions .form-button.submit',
    containerSelector: '#add-user-form',
    method: 'user.add',
    endpoint: endPoint,
    callbackOnData: (payload) => {
        ComponentFunctions.replaceTable(payload, '[table-r-id="user-table-1"]');
    }
});


//
// === Удаление строки из таблицы по кнопке ===
//
function attachDeleteTriggerSource() {
    ComponentFunctions.attachDeleteTrigger({
        triggerSelector: '[table-r-id="user-table-1"] .btn-delete.btn-row-table',
        method: 'user.delete',
        endpoint: endPoint,
        callbackOnData: (payload) => {
            ComponentFunctions.replaceTable(payload, '[table-r-id="user-table-1"]');
        },
        beforeSendCallback: async (trigger, rowId) => {
            return await ConfirmDialog.show('Удаление', `Удалить элемент #${rowId}?`, '.overlay-main');
        },
    });
}

//
// === Редактирование значения ячейки по кнопке внутри строки ===
//
function attachInputButtonTriggerSource() {
    ComponentFunctions.attachInputButtonTrigger({
        containerSelector: '[table-r-id="user-table-1"]',
        buttonSelector: 'td .edit-row-button',
        inputSelector: 'input.edit-row-input',
        searchRootSelector: 'td',
        attributes: ['value', 'data-row-id'],
        method: 'user.edit.cell',
        endpoint: endPoint,
    });
}

//
// === Отслеживание изменения значения в input'ах таблицы ===
//
function watchInputValueChangeSource() {
    ComponentFunctions.watchInputValueChange({
        inputSelector: '[table-r-id="user-table-1"] input.edit-row-input',
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
attachDeleteTriggerSource();
attachInputButtonTriggerSource();
watchInputValueChangeSource();

//
// === Наблюдение за появлением новых элементов в DOM и повторное навешивание обработчиков ===
//
const targetNode = document.querySelector('[table-r-id="user-table-1"]');
if (!targetNode) {
    console.warn('Container [table-r-id="user-table-1"] not found');
} else {
    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                attachDeleteTriggerSource();
                attachInputButtonTriggerSource();
                watchInputValueChangeSource();
                break; // один раз достаточно
            }
        }
    });

    observer.observe(targetNode, {
        childList: true,
        subtree: true
    });
}