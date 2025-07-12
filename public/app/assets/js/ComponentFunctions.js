import { JsonRpcTransport } from './JsonRpcTransport.js';
import { NotificationManager } from './NotificationManager.js';

const ComponentFunctionsNotification = new NotificationManager({
    containerSelector: '.notification-container',
    maxVisible: 4,
    timeout: 5000,
    timeOpacity: 2000
});

const onErrorDefaultFunction = (error) => {
    let message;

    if (typeof error === 'string') {
        message = error;
    } else if (error instanceof Error) {
        message = error.message || 'Неизвестная ошибка';
    } else if (error && typeof error.message === 'string') {
        message = error.message;
    } else {
        message = 'Неизвестная ошибка';
    }

    ComponentFunctionsNotification.add(message, 'danger');
}

const onSuccessDefaultFunction = (message) => {
    ComponentFunctionsNotification.add(message ?? 'Успех!', 'success');
}


export function processingMessage(message, type = 'info') {
    if (type === 'error') {
        if (typeof onErrorDefaultFunction !== 'function') {
            console.log('[JsonRpc] Ошибки:', message);
            return;
        }
        onErrorDefaultFunction(message);
    } else if (type === 'success') {
        if (typeof onSuccessDefaultFunction !== 'function') {
            console.log('[JsonRpc] Успех:', message);
            return;
        }
        onSuccessDefaultFunction(message);
    }
}


/**
 * Функции, связанные с компонентами интерфейса.
 * Все объединены в единый объект.
 *
 * @example
 * import { ComponentFunctions } from './ComponentFunctions.js';
 * ComponentFunctions.focusInput({ selector: '#username' });
 * ComponentFunctions.attachJsonRpcFormTrigger({...});
 */
export const ComponentFunctions = {

    processMessagesArray(messages = []) {
        if (!Array.isArray(messages)) {
            console.warn('[JsonRpc] Ожидался массив сообщений');
            return;
        }

        messages.forEach(({ message, type }) => {
            if (typeof message !== 'string' || typeof type !== 'string') {
                console.warn('[JsonRpc] Некорректный формат сообщения:', { message, type });
                return;
            }

            processingMessage(message, type);
        });
    },

    /**
     * Показывает сообщение в консоль (или alert)
     * @param {{ message: string, alert?: boolean }} payload
     */
    showMessage({ message, alert = false }) {
        if (alert) window.alert(message);
        else console.log('[ComponentMessage]', message);
    },

    /**
     * Назначает обработку формы через JSON-RPC по клику на кнопку
     *
     * @param {{
     *   triggerSelector: string,
     *   formSelector: string,  
     *   method: string,
     *   endpoint?: string
     * }} config
     */
    attachJsonRpcFormTrigger({ triggerSelector, formSelector, method, endpoint = '/api' }) {
        const trigger = document.querySelector(triggerSelector);
        const form = document.querySelector(formSelector);

        if (!trigger || !form) {
            console.warn('[ComponentFunctions] Кнопка или форма не найдена');
            return;
        }

        const transport = new JsonRpcTransport(method, {
            endpoint,
            onContentUpdate: () => { }, // заглушка
            onData: (payload) => {
                const messages = Array.isArray(payload) ? payload : [];
                for (const msg of messages) {
                    processingMessage(msg.message, msg.type);
                }
            },
            onError: onErrorDefaultFunction
        });

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            transport.sendFromForm?.(form) || transport.send(transport.formatter.fromForm(form).params);
        });
    },

    /**
     * Назначает обработку любого input-контейнера через JSON-RPC по клику на кнопку.
     *
     * После клика собирает данные из input, select и textarea внутри указанного контейнера
     * и отправляет их через JSON-RPC.
     *
     * @param {Object} config - Конфигурация вызова
     * @param {string} config.triggerSelector - CSS-селектор для кнопки-триггера (например, '.btn-submit')
     * @param {string} config.containerSelector - CSS-селектор контейнера, из которого будут собираться данные
     * @param {string} config.method - Название метода JSON-RPC
     * @param {string} [config.endpoint='/api'] - Адрес отправки запроса
     * @param {function(any):void} [config.callbackOnData=null] - Коллбек для обработки успешного ответа (если не передан, будут показаны сообщения в messageBox)
     * @param {function(Error):void} [config.callbackOnError=null] - Коллбек для обработки ошибок (если не передан, ошибка выводится в консоль)
     *
     * @example
     * ComponentFunctions.attachJsonRpcInputTrigger({
     *   triggerSelector: '.btn-submit',
     *   containerSelector: '#form-container',
     *   method: 'lead.filter',
     *   endpoint: '/api/leads',
     *   callbackOnData: (data) => console.log(data),
     * });
     */
    attachJsonRpcInputTrigger({
        triggerSelector,
        containerSelector,
        method,
        endpoint = '/api',
        callbackOnData = null,
        callbackOnError = onErrorDefaultFunction,
    }) {
        const trigger = document.querySelector(triggerSelector);
        const container = document.querySelector(containerSelector);

        if (!trigger || !container) {
            console.warn('[ComponentFunctions] Кнопка или контейнер не найден');
            return;
        }

        const transport = new JsonRpcTransport(method, {
            endpoint,
            onContentUpdate: () => { },
            onData: (payload) => {
                if (typeof callbackOnData === 'function') {
                    callbackOnData(payload);
                    return;
                }

                const messages = Array.isArray(payload) ? payload : [];
                for (const msg of messages) {
                    if (msg.type === 'redirect') {
                        continue;
                    }
                    processingMessage(msg.message, msg.type);
                }

                const redirect = messages.find((msg) => msg.type === 'redirect');
                if (redirect) {
                    setTimeout(() => { }, 1000);
                    window.location.href = redirect.url || '/';
                }
            },
            onError: (error) => {
                if (typeof callbackOnError === 'function') {
                    callbackOnError(error);
                } else {
                    console.error('[JsonRpcTransport] Ошибка:', error.message);
                }
            }
        });

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            transport.sendFromSelectorInputs(container);
        });
    },


    /**
      * Навешивает обработчик JSON-RPC по пользовательским атрибутам
      *
      * @param {{
      *   triggerSelector: string,
      *   endpoint?: string,
      *   methodAttr?: string,
      *   dataAttr?: string,
      *   endpointAttr?: string,
      *   onData?: Function,
      *   onError?: Function,
      *   onContentUpdate?: Function
      * }} options
      */
    attachJsonRpcTriggerFromAttributes({
        triggerSelector,
        endpoint = '/api',
        methodAttr = 'data-rpc-method',
        dataAttr = 'data-rpc-data',
        endpointAttr = 'data-rpc-endpoint',
        onData = (payload) => console.log('[JsonRpc] Ответ:', payload),
        onError = onErrorDefaultFunction,
        onContentUpdate = () => { }
    }) {
        const trigger = document.querySelector(triggerSelector);
        if (!trigger) {
            console.warn('[ComponentFunctions] Триггер не найден:', triggerSelector);
            return;
        }

        trigger.addEventListener('click', async (e) => {
            e.preventDefault();

            const method = trigger.getAttribute(methodAttr);
            if (!method) {
                console.warn(`[ComponentFunctions] Не найден атрибут ${methodAttr}`);
                return;
            }

            let data = {};
            const raw = trigger.getAttribute(dataAttr);
            if (raw) {
                try {
                    data = JSON.parse(raw);
                } catch {
                    if (window[raw]) {
                        data = window[raw];
                    } else {
                        console.warn(`[ComponentFunctions] Не удалось разобрать ${dataAttr}:`, raw);
                    }
                }
            }

            const endpointFromAttr = trigger.getAttribute(endpointAttr);
            const finalEndpoint = endpointFromAttr || endpoint;

            const transport = new JsonRpcTransport(method, {
                endpoint: finalEndpoint,
                onData,
                onError,
                onContentUpdate
            });

            transport.send(data);
        });
    },

    /**
     * Назначает обработку input-контейнеров через JSON-RPC для нескольких триггеров.
     *
     * @param {{
     *   triggerSelector: string,
     *   containerSelector: string,
     *   method: string,
     *   endpoint?: string,
     *   callbackOnData?: Function,
     *   callbackOnError?: Function
     * }} config
     */
    attachJsonRpcInputManyTriggers(config) {
        const {
            triggerSelector,
            containerSelector,
            method,
            endpoint = '/api',
            callbackOnData = null,
            callbackOnError = onErrorDefaultFunction,
        } = config;

        const triggers = document.querySelectorAll(triggerSelector);
        const container = document.querySelector(containerSelector);

        if (!triggers.length || !container) {
            console.warn('[ComponentFunctions] Кнопки или контейнер не найдены для:', triggerSelector);
            return;
        }

        const transport = new JsonRpcTransport(method, {
            endpoint,
            onContentUpdate: () => { },
            onData: (payload) => {
                if (typeof callbackOnData === 'function') {
                    callbackOnData(payload);
                    return;
                }

                for (const msg of messages) {
                    processingMessage(msg.message, msg.type);
                }
            },
            onError: (error) => {
                if (typeof callbackOnError === 'function') {
                    callbackOnError(error);
                } else {
                    console.error('[JsonRpcTransport] Ошибка:', error.message);
                }
            }
        });

        for (const trigger of triggers) {
            trigger.addEventListener('click', (e) => {
                e.preventDefault();
                transport.sendFromSelectorInputs(container);
            });
        }
    },


    /**
     * Заменяет таблицу из ответа или выводит сообщение об отсутствии данных.
     *
     * @param {any} response Ответ от сервера с полем table (строкой HTML)
     * @param {string} tableWrapperSelector CSS-селектор обёртки
     */
    replaceLeadTable(response, tableWrapperSelector = '.table-wrapper') {
        const wrapper = document.querySelector(tableWrapperSelector);
        if (!wrapper) {
            console.warn('[ComponentFunctions] Обёртка таблицы не найдена:', tableWrapperSelector);
            return;
        }

        if (response && typeof response.table === 'string') {
            wrapper.innerHTML = response.table;
        } else {
            wrapper.innerHTML = `<p class="no-data-message">Данные не найдены или произошла ошибка при загрузке.</p>`;
        }
    },

    attachDeleteTrigger({
        triggerSelector,
        method,
        endpoint = '/api/',
        onData = (payload) => console.log('[JsonRpc] Ответ:', payload),
    }) {
        const triggers = document.querySelectorAll(triggerSelector);

        for (const trigger of triggers) {
            if (trigger.dataset.bound) continue; // защита от повторного добавления
            trigger.dataset.bound = 'true';

            trigger.addEventListener('click', (event) => {
                event.preventDefault();

                // Ищем ближайший input[name="row_id"] среди родителей и соседей
                const rowIdInput = trigger.closest('tr')?.querySelector('input[name="row_id"]');
                const rowId = rowIdInput?.value ?? null;

                if (!rowId) {
                    console.warn('[Delete Trigger] Не найден row_id для удаления');
                    return;
                }

                const transport = new JsonRpcTransport(method, {
                    endpoint,
                    onContentUpdate: () => { },
                    onData: onData,
                    onError: (error) => {
                        onErrorDefaultFunction(error);
                        console.error('[JsonRpcTransport] Ошибка:', error.message);
                    }
                });

                transport.send({ rowId });
            });
        }
    },

    /**
 * Вешает обработчик на все кнопки и отправляет данные из связанного input, ища родителя по кастомному селектору.
 *
 * @param {Object} options
 * @param {string} options.containerSelector - Основной контейнер.
 * @param {string} options.buttonSelector - Селектор кнопок.
 * @param {string} options.inputSelector - Селектор для поиска input внутри родителя.
 * @param {string[]} [options.attributes=['value', 'data-row-id']] - Какие атрибуты брать из input.
 * @param {string} options.method - Метод JSON-RPC.
 * @param {string} [options.endpoint='/api/'] - Эндпоинт для отправки.
 * @param {string} [options.searchRootSelector='tr'] - До какого родителя искать (например, 'tr', 'td', 'div.row').
 */
    attachInputButtonTrigger({
        containerSelector,
        buttonSelector,
        inputSelector,
        attributes = ['value', 'data-row-id'],
        method,
        endpoint = '/api/',
        searchRootSelector = 'tr',
    }) {
        const container = document.querySelector(containerSelector);
        if (!container) {
            console.warn(`[attachInputButtonTrigger] Контейнер ${containerSelector} не найден`);
            return;
        }

        const buttons = container.querySelectorAll(buttonSelector);

        for (const button of buttons) {
            if (button.dataset.bound) continue;
            button.dataset.bound = 'true';

            button.addEventListener('click', (event) => {
                event.preventDefault();

                // Ищем родителя до заданного уровня
                const searchRoot = button.closest(searchRootSelector);
                const input = searchRoot?.querySelector(inputSelector);

                if (!input) {
                    console.warn('[attachInputButtonTrigger] Не найден связанный input');
                    return;
                }

                const data = {};
                for (const attr of attributes) {
                    if (attr === 'value') {
                        data[attr] = input.value ?? null;
                    } else if (attr.startsWith('data-')) {
                        const datasetKey = attr.slice(5).replace(/-([a-z])/g, (_, c) => c.toUpperCase());
                        data[attr] = input.dataset[datasetKey] ?? null;
                    } else {
                        data[attr] = input.getAttribute(attr);
                    }
                }

                const transport = new JsonRpcTransport(method, {
                    endpoint,
                    onContentUpdate: () => { },
                    onData: (payload) => {
                        ComponentFunctions.replaceLeadTable(payload, '[table-r-id]');
                    },
                    onError: (error) => {
                        onErrorDefaultFunction(error);
                        console.error('[JsonRpcTransport] Ошибка:', error.message);
                    },
                });

                transport.send(data);
            });
        }
    },


    /**
     * Следит за изменениями input'ов и вызывает callback при изменении значения и потере фокуса.
     *
     * @param {Object} options
     * @param {string} options.inputSelector - Селектор для поиска input элементов.
     * @param {function} options.onChange - Коллбэк при изменении значения: (oldValue, newValue, inputElement).
     * @param {function} [options.onBlur] - Коллбэк при потере фокуса: (inputElement, previousValue).
     */
    watchInputValueChange({ inputSelector, onChange, onBlur }) {
        const inputs = document.querySelectorAll(inputSelector);

        for (const input of inputs) {
            const previous = { value: input.value }; // Объект для ссылки на значение

            const checkChange = () => {
                if (input.value !== previous.value) {
                    if (typeof onChange === 'function') {
                        onChange(previous.value, input.value, input);
                    }
                    previous.value = input.value;
                }
            };

            input.addEventListener('input', checkChange);
            input.addEventListener('change', checkChange);

            if (typeof onBlur === 'function') {
                input.addEventListener('blur', () => {
                    setTimeout(() => {
                        input.value = previous.value;
                        onBlur?.(input, previous);
                    }, 200);
                });
            }
        }
    }





};
