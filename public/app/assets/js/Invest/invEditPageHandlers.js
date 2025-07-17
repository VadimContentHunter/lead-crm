import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
import { ConfirmDialog } from '/assets/js/ConfirmDialog.js';

const endPoint = '/api/invest/leads';


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
// === Добавление строки в таблицу по кнопке в форме ===
//
// ComponentFunctions.attachJsonRpcInputTrigger({
//     triggerSelector: '#add-inv-lead-form .form-actions .form-button.submit',
//     containerSelector: '#add-inv-lead-form',
//     method: 'invest.lead.add',
//     endpoint: endPoint,
//     callbackBeforeSend: () => {
//         if (overlayLoader instanceof HTMLElement) {
//             overlayLoader.style.display = '';
//         }
//     },
//     callbackOnData: (payload) => {
//         console.log(payload);
//         ComponentFunctions.replaceTable(payload, '[table-r-id="inv-lead-table-1"]');
//         if (overlayLoader instanceof HTMLElement) {
//             overlayLoader.style.display = 'none';
//         }
//     }
// });

//
// === Заполняет форму при загрузки страницы ===
//

const uid = window.location.pathname.split('/').pop();
ComponentFunctions.runJsonRpcLoadImmediately({
    method: 'invest.lead.get.form.create',
    endpoint: endPoint,
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
    method: 'invest.lead.get.balance',
    endpoint: endPoint,
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
// === Обновление основной информации для лида (инвестиции) ===
//

ComponentFunctions.attachJsonRpcInputTrigger({
    triggerSelector: '#inv-lead-form-1 .form-actions .submit',
    containerSelector: '#inv-lead-form-1',
    method: 'invest.lead.update',
    endpoint: endPoint,
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
    method: 'invest.lead.update.balance',
    endpoint: endPoint,
    callbackBeforeSend: () => {
        overlayMainLoaderOpen();
    },
    callbackOnData: (payload) => {
        overlayMainLoaderClose();
    }
});

//
// === Удаление строки из таблицы по кнопке ===
//
// function attachDeleteTriggerLead() {
//     ComponentFunctions.attachDeleteTrigger({
//         triggerSelector: '[table-r-id="inv-lead-table-1"] .btn-delete.btn-row-table',
//         method: 'lead.delete',
//         endpoint: endPoint,
//         callbackOnData: (payload) => {
//             ComponentFunctions.replaceTable(payload, '[table-r-id="inv-lead-table-1"]');
//         },
//         beforeSendCallback: async (trigger, rowId) => {
//             return await ConfirmDialog.show('Удаление', `Удалить элемент #${rowId}?`, '.overlay-main');
//         },
//     });
// }


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
//         endpoint: endPoint,
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
// attachDeleteTriggerLead();
// attachInputButtonTriggerLead();
// watchInputValueChangeLead();

//
// === Наблюдение за появлением новых элементов в DOM и повторное навешивание обработчиков ===
//
// const targetNode = document.querySelector('[table-r-id="inv-lead-table-1"]');
// if (!targetNode) {
//     console.warn('Container [table-r-id="inv-lead-table-1"] not found');
// } else {
//     const observer = new MutationObserver((mutationsList) => {
//         for (const mutation of mutationsList) {
//             if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
//                 attachDeleteTriggerLead();
//                 attachInputButtonTriggerLead();
//                 watchInputValueChangeLead();
//                 break; // один раз достаточно
//             }
//         }
//     });

//     observer.observe(targetNode, {
//         childList: true,
//         subtree: true
//     });
// }