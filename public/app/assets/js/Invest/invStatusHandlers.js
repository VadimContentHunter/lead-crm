import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
import { ConfirmDialog } from '/assets/js/ConfirmDialog.js';

const endPoint = '/api/invest/statuses';
const overlayLoader = document.querySelector('#overlay-loader');

//
// === Добавление строки в таблицу по кнопке ===
//
ComponentFunctions.attachJsonRpcInputTrigger({
    triggerSelector: '#add-inv-status-form .form-actions .form-button.submit',
    containerSelector: '#add-inv-status-form',
    method: 'invest.status.add',
    endpoint: endPoint,
    callbackBeforeSend: () => {
        if (overlayLoader instanceof HTMLElement) {
            overlayLoader.style.display = '';
        }
    },
    callbackOnData: (payload) => {
        ComponentFunctions.replaceTable(payload, '[table-r-id="inv-status-table-1"]');
        if (overlayLoader instanceof HTMLElement) {
            overlayLoader.style.display = 'none';
        }
    },
});


//
// === Удаление строки из таблицы по кнопке ===
//
function attachDeleteTriggerStatus() {
    ComponentFunctions.attachDeleteTrigger({
        triggerSelector: '[table-r-id="inv-status-table-1"] .btn-delete.btn-row-table',
        method: 'invest.status.delete',
        endpoint: endPoint,
        callbackOnData: (payload) => {
            ComponentFunctions.replaceTable(payload, '[table-r-id="inv-status-table-1"]');
            if (overlayLoader instanceof HTMLElement) {
                overlayLoader.style.display = 'none';
            }
        },
        beforeValidateCallback: async (trigger, rowId) => {
            return await ConfirmDialog.show('Удаление', `Удалить элемент #${rowId}?`, '.overlay-main');
        },
        beforeSendCallback: () => {
            if (overlayLoader instanceof HTMLElement) {
                overlayLoader.style.display = '';
            }
        },
        callbackOnError: (error) => {
            if (overlayLoader instanceof HTMLElement) {
                overlayLoader.style.display = 'none';
            }
        }
    });
}

//
// === Редактирование значения ячейки по кнопке внутри строки ===
//
function attachInputButtonTriggerStatus() {
    ComponentFunctions.attachInputButtonTrigger({
        containerSelector: '[table-r-id="inv-status-table-1"]',
        buttonSelector: 'td .edit-row-button',
        inputSelector: 'input.edit-row-input',
        searchRootSelector: 'td',
        attributes: ['value', 'data-row-id', 'old-value', 'name'],
        method: 'invest.status.edit.cell',
        endpoint: endPoint,
        callbackBeforeSend: () => {
            if (overlayLoader instanceof HTMLElement) {
                overlayLoader.style.display = '';
            }
        },
        callbackOnData: (payload) => {
            ComponentFunctions.replaceTable(payload, '[table-r-id="inv-status-table-1"]');
            if (overlayLoader instanceof HTMLElement) {
                overlayLoader.style.display = 'none';
            }
        },
        callbackOnError: (error) => {
            if (overlayLoader instanceof HTMLElement) {
                overlayLoader.style.display = 'none';
            }
        }
    });
}

//
// === Отслеживание изменения значения в input'ах таблицы ===
//
function watchInputValueChangeStatus() {
    ComponentFunctions.watchInputValueChange({
        inputSelector: '[table-r-id="inv-status-table-1"] input.edit-row-input',
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
attachDeleteTriggerStatus();
attachInputButtonTriggerStatus();
watchInputValueChangeStatus();

//
// === Наблюдение за появлением новых элементов в DOM и повторное навешивание обработчиков ===
//
const targetNode = document.querySelector('[table-r-id="inv-status-table-1"]');
if (!targetNode) {
    console.warn('Container [table-r-id="inv-status-table-1"] not found');
} else {
    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                attachDeleteTriggerStatus();
                attachInputButtonTriggerStatus();
                watchInputValueChangeStatus();
                break; // один раз достаточно
            }
        }
    });

    observer.observe(targetNode, {
        childList: true,
        subtree: true
    });
}