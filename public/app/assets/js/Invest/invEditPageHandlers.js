import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
import { ConfirmDialog } from '/assets/js/ConfirmDialog.js';

const endPointInvLeads = '/api/invest/leads';
const endPointInvActivities = '/api/invest/activities';
const endPointInvBalances = '/api/invest/balances';
const overlaySideBarLoader = document.querySelector('#overlay-loader');


const overlayMainLoaderOpen = () => {
    const overlayMain = document.querySelector('.overlay-main');
    const overlayLoader = document.querySelector('#overlay-loader-main');
    if (overlayLoader instanceof HTMLElement && overlayMain instanceof HTMLElement) {
        overlayMain.style.display = 'flex';
        overlayLoader.style.display = '';
    }
};

const overlayMainLoaderClose = () => {
    const overlayMain = document.querySelector('.overlay-main');
    const overlayLoader = document.querySelector('#overlay-loader-main');
    if (overlayLoader instanceof HTMLElement && overlayMain instanceof HTMLElement) {
        overlayMain.style.display = 'none';
        overlayLoader.style.display = 'none';
    }
};

//
// === Добавление Активности ===
//
ComponentFunctions.attachJsonRpcInputTrigger({
    triggerSelector: '#add-inv-activity-form .form-actions .form-button.submit',
    containerSelector: '#add-inv-activity-form',
    method: 'active.add',
    endpoint: endPointInvActivities,
    callbackBeforeSend: () => {
        if (overlaySideBarLoader instanceof HTMLElement) {
            overlaySideBarLoader.style.display = '';
        }
    },
    callbackOnData: (payload) => {
        // console.log(payload);
        ComponentFunctions.replaceTable(payload, '[table-r-id="inv-activity-table-1"]');
        if (overlaySideBarLoader instanceof HTMLElement) {
            overlaySideBarLoader.style.display = 'none';
        }
    }
});

//
// === Заполняет форму при загрузки страницы ===
//

const uid = window.location.pathname.split('/').pop();
ComponentFunctions.runJsonRpcLoadImmediately({
    method: 'invest.lead.get.form.create',
    endpoint: endPointInvLeads,
    jsonContent: { uid: uid },
    callbackBeforeSend: () => {
        overlayMainLoaderOpen();
    },
    callbackOnData: (payload) => {
        ComponentFunctions.fillFormFromData('#inv-lead-form-1 form', payload?.data ?? []);
        overlayMainLoaderClose();
    }
});

ComponentFunctions.runJsonRpcLoadImmediately({
    method: 'invest.balance.get',
    endpoint: endPointInvBalances,
    jsonContent: { uid: uid },
    callbackBeforeSend: () => {
        overlayMainLoaderOpen();
    },
    callbackOnData: (payload) => {
        ComponentFunctions.fillFormFromData('#inv-balance-form-1 form', payload?.data ?? []);
        overlayMainLoaderClose();
    }
});

//
// === Заполнение формы при открытии модального окна ===
//
ComponentFunctions.attachJsonRpcLoadTrigger({
    triggerSelector: '#add-inv-activity-btn',
    method: 'active.get.form',
    endpoint: endPointInvActivities,
    jsonContent: { uid: uid },
    callbackBeforeSend: () => {
        if (overlaySideBarLoader instanceof HTMLElement) {
            overlaySideBarLoader.style.display = '';
        }
    },
    callbackOnData: (payload) => {
        ComponentFunctions.fillFormFromData('#add-inv-activity-form form', payload?.data ?? []);
        if (overlaySideBarLoader instanceof HTMLElement) {
            overlaySideBarLoader.style.display = 'none';
        }
    }
});

//
// === Обновление основной информации для лида (инвестиции) ===
//

ComponentFunctions.attachJsonRpcInputTrigger({
    triggerSelector: '#inv-lead-form-1 .form-actions .submit',
    containerSelector: '#inv-lead-form-1',
    method: 'invest.lead.update',
    endpoint: endPointInvLeads,
    callbackBeforeSend: () => {
        overlayMainLoaderOpen();
    },
    callbackOnData: (payload) => {
        overlayMainLoaderClose();
    }
});

ComponentFunctions.attachJsonRpcInputTrigger({
    triggerSelector: '#inv-balance-form-1 .form-actions .submit',
    containerSelector: '#inv-balance-form-1',
    method: 'invest.balance.update',
    endpoint: endPointInvBalances,
    callbackBeforeSend: () => {
        overlayMainLoaderOpen();
    },
    callbackOnData: (payload) => {
        ComponentFunctions.fillFormFromData('#inv-balance-form-1 form', payload?.data ?? []);
        overlayMainLoaderClose();
    }
});

//
// === Удаление строки из таблицы по кнопке ===
//
function attachDeleteTriggerActivity() {
    ComponentFunctions.attachDeleteTrigger({
        triggerSelector: '[table-r-id="inv-activity-table-1"] .btn-delete.btn-row-table',
        method: 'active.delete',
        endpoint: endPointInvActivities,
        callbackOnData: (payload) => {
            ComponentFunctions.replaceTable(payload, '[table-r-id="inv-activity-table-1"]');
            overlayMainLoaderClose();
        },
        callbackOnError: (error) => {
            overlayMainLoaderClose();
        },
        beforeValidateCallback: async (trigger, rowId) => {
            return await ConfirmDialog.show('Удаление', `Удалить элемент #${rowId}?`, '.overlay-main');
        },
        beforeSendCallback: (trigger, rowId) => {
            overlayMainLoaderOpen();
        },
    });
}


//
// === Редактирование значения ячейки по кнопке внутри строки ===
//
// function attachInputButtonTriggerLead() {
//     ComponentFunctions.attachInputButtonTrigger({
//         containerSelector: '[table-r-id="inv-lead-table-1"]',
//         buttonSelector: 'td .edit-row-button',
//         inputSelector: 'input.edit-row-input',
//         searchRootSelector: 'td',
//         attributes: ['value', 'data-row-id'],
//         method: 'lead.edit.cell',
//         endPointInvLeads: endPointInvLeads,
//     });
// }

//
// === Отслеживание изменения значения в input'ах таблицы ===
//
// function watchInputValueChangeLead() {
//     ComponentFunctions.watchInputValueChange({
//         inputSelector: '[table-r-id="inv-lead-table-1"] input.edit-row-input',
//         onChange: (oldValue, newValue, inputElement) => {
//             const container = inputElement.closest('td');
//             const wrapper = container?.querySelector('.cell-actions-wrapper');
//             const inputOldValue = inputElement.getAttribute("old-value") ?? null;
//             if (inputOldValue !== newValue && wrapper) {
//                 wrapper.style.display = 'flex';
//             } else if (inputOldValue === newValue && wrapper) {
//                 wrapper.style.display = '';
//             }
//         },
//         onBlur: (inputElement, previous) => {
//             const container = inputElement.closest('td');
//             const wrapper = container?.querySelector('.cell-actions-wrapper');
//             const oldValue = inputElement.getAttribute("old-value") ?? null;
//             if (oldValue !== null) {
//                 inputElement.value = oldValue;
//                 previous.value = oldValue;
//             }
//             if (wrapper) {
//                 wrapper.style.display = '';
//             }
//         }
//     });
// }

//
// === Инициализация обработчиков для уже существующих элементов таблицы ===
//
attachDeleteTriggerActivity();
// attachInputButtonTriggerLead();
// watchInputValueChangeLead();

//
// === Наблюдение за появлением новых элементов в DOM и повторное навешивание обработчиков ===
//
const targetNode = document.querySelector('[table-r-id="inv-activity-table-1"]');
if (!targetNode) {
    console.warn('Container [table-r-id="inv-activity-table-1"] not found');
} else {
    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                attachDeleteTriggerActivity();
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