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


export function processingMessage(message, type = 'info') {
    if (type === 'error') {
        if (typeof onErrorDefaultFunction !== 'function') {
            console.log('[JsonRpc] Ошибки:', message);
            return;
        }
        onErrorDefaultFunction(message);
    } else if (type === 'success') {
        if (typeof ComponentFunctionsNotification.add !== 'function') {
            console.log('[JsonRpc] Успех:', message);
            return;
        }
        ComponentFunctionsNotification.add(message ?? 'Успех!', 'success');
    } else if (type === 'info') {
        if (typeof ComponentFunctionsNotification.add !== 'function') {
            console.log('[JsonRpc] Информация:', message);
            return;
        }
        ComponentFunctionsNotification.add(message ?? 'Информация', 'info');
    }
}


export function processingPayload(payload) {
    if (typeof payload?.message === 'string') {
        const type = payload.type ?? 'info';
        processingMessage(payload.message, type);
        return;
    }

    if (typeof payload?.type === 'redirect') {
        setTimeout(() => { }, 1000);
        window.location.href = redirect.url || '/';
        return;
    }

    if (payload?.messages?.length > 0) {
        ComponentFunctions.processMessagesArray(payload.messages);
        return;
    }

    if (Array.isArray(payload)) {
        for (const msg of payload) {
            if (msg.type === 'redirect') {
                continue;
            }
            if (typeof msg?.message === 'string') {
                const type = msg.type ?? 'info';
                processingMessage(msg.message, type);
            }
        }

        const redirect = payload.find((msg) => msg.type === 'redirect');
        if (redirect) {
            setTimeout(() => { }, 1000);
            window.location.href = redirect.url || '/';
        }
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
 * @param {function():void} [config.callbackBeforeSend=null] - Коллбек, вызываемый перед отправкой запроса
 *
 * @example
 * ComponentFunctions.attachJsonRpcInputTrigger({
 *   triggerSelector: '.btn-submit',
 *   containerSelector: '#form-container',
 *   method: 'lead.filter',
 *   endpoint: '/api/leads',
 *   callbackBeforeSend: () => showLoader(),
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
        callbackBeforeSend = null,
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
                }
                processingPayload(payload);
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
            if (typeof callbackBeforeSend === 'function') {
                callbackBeforeSend();
            }
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
                    // return;
                }

                processingPayload(payload);
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
     * @param {boolean} outputError Выводить сообщение об отсутствии данных или ошибке
     */
    replaceTable(response, tableWrapperSelector = '', outputError = false) {
        const wrapper = document.querySelector(tableWrapperSelector);
        if (!wrapper) {
            console.warn('[ComponentFunctions] Обёртка таблицы не найдена:', tableWrapperSelector);
            return;
        }

        if (response && typeof response.table === 'string') {
            wrapper.innerHTML = response.table;
        } else if (outputError) {
            wrapper.innerHTML = `<p class="no-data-message">Данные не найдены или произошла ошибка при загрузке.</p>`;
        }
    },

    /**
     * Назначает обработчик на кнопки удаления, с поддержкой валидации, действий перед отправкой и коллбеков.
     *
     * @param {Object} config
     * @param {string} config.triggerSelector - Селектор кнопок удаления
     * @param {string} config.method - JSON-RPC метод
     * @param {string} [config.endpoint='/api/'] - Эндпоинт JSON-RPC
     * @param {function(HTMLElement, string): Promise<boolean|void>} [config.beforeValidateCallback] - Вызывается до отправки, может отменить процесс (вернув false)
     * @param {function(HTMLElement, string): void} [config.beforeSendCallback] - Вызывается сразу перед отправкой запроса
     * @param {function(any): void} [config.callbackOnData] - Вызывается при успешном ответе
     * @param {function(Error): void} [config.callbackOnError] - Вызывается при ошибке запроса
     */
    attachDeleteTrigger({
        triggerSelector,
        method,
        endpoint = '/api/',
        beforeValidateCallback = async () => { },
        beforeSendCallback = () => { },
        callbackOnData = (payload) => console.log('[JsonRpc] Ответ:', payload),
        callbackOnError = null,
    }) {
        const triggers = document.querySelectorAll(triggerSelector);

        for (const trigger of triggers) {
            if (trigger.dataset.bound) continue;
            trigger.dataset.bound = 'true';

            trigger.addEventListener('click', async (event) => {
                event.preventDefault();

                const rowIdInput = trigger.closest('tr')?.querySelector('input[name="row_id"]');
                const rowId = rowIdInput?.value ?? null;

                if (!rowId) {
                    console.warn('[Delete Trigger] Не найден row_id для удаления');
                    return;
                }

                try {
                    const valid = await beforeValidateCallback(trigger, rowId);
                    if (valid === false) {
                        console.log('[Delete Trigger] beforeValidateCallback отменил удаление');
                        return;
                    }
                } catch (e) {
                    console.warn('[Delete Trigger] beforeValidateCallback выбросил ошибку:', e);
                    return;
                }

                if (typeof beforeSendCallback === 'function') {
                    beforeSendCallback(trigger, rowId);
                }

                const transport = new JsonRpcTransport(method, {
                    endpoint,
                    onContentUpdate: () => { },
                    onData: (payload) => {
                        if (typeof callbackOnData === 'function') {
                            callbackOnData(payload);
                        }
                        processingPayload(payload);
                    },
                    onError: (error) => {
                        onErrorDefaultFunction(error);
                        console.error('[JsonRpcTransport] Ошибка:', error.message);

                        if (typeof callbackOnError === 'function') {
                            callbackOnError(error);
                        }
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
     * @param {function(Object):void} [options.callbackBeforeSend] - Вызывается перед отправкой запроса, получает data.
     * @param {function(any):void} [options.callbackOnData] - Вызывается при получении ответа, до processingPayload.
     * @param {function(any):void} [options.callbackOnError] - 
     */
    attachInputButtonTrigger({
        containerSelector,
        buttonSelector,
        inputSelector,
        attributes = ['value', 'data-row-id'],
        method,
        endpoint = '/api/',
        searchRootSelector = 'tr',
        callbackBeforeSend = null,
        callbackOnData = (payload) => ComponentFunctions.replaceTable(payload, '[table-r-id]'),
        callbackOnError = null,
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

                if (typeof callbackBeforeSend === 'function') {
                    callbackBeforeSend(data);
                }

                const transport = new JsonRpcTransport(method, {
                    endpoint,
                    onContentUpdate: () => { },
                    onData: (payload) => {
                        if (typeof callbackOnData === 'function') {
                            callbackOnData(payload);
                        }

                        processingPayload(payload);
                    },
                    onError: (error) => {
                        onErrorDefaultFunction(error);
                        console.error('[JsonRpcTransport] Ошибка:', error.message);

                        if (typeof callbackOnError === 'function') {
                            callbackOnError(error);
                        }
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
    },

    /**
     * Назначает обработчик, который выполняет JSON-RPC запрос при клике на кнопку,
     * вызывает переданный коллбек с данными и обрабатывает их через processingPayload.
     *
     * @param {Object} config - Конфигурация запроса
     * @param {string} config.triggerSelector - CSS-селектор кнопки
     * @param {string} config.method - Название метода JSON-RPC
     * @param {string} [config.endpoint='/api'] - Адрес JSON-RPC сервера
     * @param {Object} [config.jsonContent={}] - Данные, отправляемые в теле запроса
     * @param {function(any):void} [config.callbackOnData] - Коллбек, вызываемый при получении данных (всегда вызывается перед обработкой)
     * @param {function(Error):void} [config.callbackOnError] - Коллбек, вызываемый при ошибке (по умолчанию onErrorDefaultFunction)
     * @param {function():void} [config.callbackBeforeSend] - Коллбек, вызываемый сразу после клика, до отправки запроса
     */
    attachJsonRpcLoadTrigger({
        triggerSelector,
        method,
        endpoint = '/api',
        jsonContent = {},
        callbackOnData = null,
        callbackOnError = onErrorDefaultFunction,
        callbackBeforeSend = null,
    }) {
        const trigger = document.querySelector(triggerSelector);
        if (!trigger) {
            console.warn('[ComponentFunctions] Триггер не найден:', triggerSelector);
            return;
        }

        const transport = new JsonRpcTransport(method, {
            endpoint,
            onContentUpdate: () => { },
            onData: (payload) => {
                if (typeof callbackOnData === 'function') {
                    callbackOnData(payload);
                }
                processingPayload(payload);
            },
            onError: (error) => {
                if (typeof callbackOnError === 'function') {
                    callbackOnError(error);
                } else {
                    onErrorDefaultFunction(error);
                }
            }
        });

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            if (typeof callbackBeforeSend === 'function') {
                callbackBeforeSend();
            }
            transport.send(jsonContent);
        });
    },

    /**
     * Немедленно выполняет JSON-RPC запрос, аналогично attachJsonRpcLoadTrigger,
     * но без триггера и клика — всё запускается сразу.
     *
     * @param {Object} config - Конфигурация запроса
     * @param {string} config.method - Название метода JSON-RPC
     * @param {string} [config.endpoint='/api'] - Адрес JSON-RPC сервера
     * @param {Object} [config.jsonContent={}] - Данные, отправляемые в теле запроса
     * @param {function(any):void} [config.callbackOnData] - Коллбек, вызываемый при получении данных
     * @param {function(Error):void} [config.callbackOnError] - Коллбек, вызываемый при ошибке
     * @param {function():void} [config.callbackBeforeSend] - Коллбек, вызываемый до отправки запроса
     */
    runJsonRpcLoadImmediately({
        method,
        endpoint = '/api',
        jsonContent = {},
        callbackOnData = null,
        callbackOnError = onErrorDefaultFunction,
        callbackBeforeSend = null,
    }) {
        const transport = new JsonRpcTransport(method, {
            endpoint,
            onContentUpdate: () => { },
            onData: (payload) => {
                if (typeof callbackOnData === 'function') {
                    callbackOnData(payload);
                }
                processingPayload(payload);
            },
            onError: (error) => {
                if (typeof callbackOnError === 'function') {
                    callbackOnError(error);
                } else {
                    onErrorDefaultFunction(error);
                }
            }
        });

        if (typeof callbackBeforeSend === 'function') {
            callbackBeforeSend();
        }

        transport.send(jsonContent);
    },



    /**
     * Заполняет поля формы значениями из объекта.
     * Для <select> всегда ожидается массив опций с возможностью выбора.
     *
     * @param {string|HTMLFormElement} form - CSS-селектор формы или сам элемент формы
     * @param {Object<string, any>} data - Объект, где ключ — имя поля, значение:
     *   - string/boolean/number — для обычных input'ов
     *   - Array<{ value: string, text?: string, selected?: boolean }> — для <select>
     */
    fillFormFromData(form, data) {
        if (typeof form === 'string') {
            form = document.querySelector(form);
        }

        if (!(form instanceof HTMLFormElement)) {
            console.warn('[fillFormFromData] form не является HTMLFormElement или не найден по селектору');
            return;
        }

        if (typeof data !== 'object' || data === null) {
            console.warn('[fillFormFromData] data должен быть объектом');
            return;
        }

        for (const [key, raw] of Object.entries(data)) {
            const field = form.querySelector(`[name="${key}"]`);
            if (!field) continue;

            if (field.tagName === 'SELECT') {
                if (!Array.isArray(raw)) {
                    console.warn(`[fillFormFromData] Ожидался массив для select [name="${key}"]`);
                    continue;
                }

                field.innerHTML = '';
                let hasSelected = false;

                for (const opt of raw) {
                    if (typeof opt !== 'object' || opt === null || !('value' in opt)) continue;

                    const option = document.createElement('option');
                    option.value = opt.value;
                    option.textContent = opt.text ?? opt.value;
                    if (opt.selected) {
                        option.selected = true;
                        hasSelected = true;
                    }

                    field.appendChild(option);
                }

                if (!hasSelected && field.options.length > 0) {
                    field.options[0].selected = true;
                }

            } else if (field.type === 'checkbox' || field.type === 'radio') {
                field.checked = Boolean(raw);
            } else {
                field.value = raw;
            }
        }
    }





};
