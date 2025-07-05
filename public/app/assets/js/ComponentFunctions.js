import { JsonRpcTransport } from './JsonRpcTransport.js';

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

                let container =
                    form.querySelector('.form-messages-container') ||
                    document.getElementById('global-messages-container');

                if (!container) {
                    console.log('[JsonRpc] Сообщения:', messages);
                    return;
                }

                container.innerHTML = '';
                for (const msg of messages) {
                    const div = document.createElement('div');
                    div.className = 'form-message' + (msg.type && msg.type !== 'info' ? ` ${msg.type}` : '');
                    div.innerHTML = `<p>${msg.message}</p>`;
                    container.appendChild(div);
                }
            },
            onError: (error) => {
                console.error('[JsonRpcTransport] Ошибка:', error.message);
            }
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
        callbackOnError = null,
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
                let messageBox =
                    container.querySelector('.form-messages-container') ||
                    document.getElementById('global-messages-container');

                if (!messageBox) {
                    console.log('[JsonRpc] Сообщения:', messages);
                    return;
                }

                messageBox.innerHTML = '';
                for (const msg of messages) {
                    const div = document.createElement('div');
                    div.className = 'form-message' + (msg.type && msg.type !== 'info' ? ` ${msg.type}` : '');
                    div.innerHTML = `<p>${msg.message}</p>`;
                    messageBox.appendChild(div);
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
        onError = (err) => console.error('[JsonRpc] Ошибка:', err),
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
            callbackOnError = null,
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

                const messages = Array.isArray(payload) ? payload : [];
                let messageBox =
                    container.querySelector('.form-messages-container') ||
                    document.getElementById('global-messages-container');

                if (!messageBox) {
                    console.log('[JsonRpc] Сообщения:', messages);
                    return;
                }

                messageBox.innerHTML = '';
                for (const msg of messages) {
                    const div = document.createElement('div');
                    div.className = 'form-message' + (msg.type && msg.type !== 'info' ? ` ${msg.type}` : '');
                    div.innerHTML = `<p>${msg.message}</p>`;
                    messageBox.appendChild(div);
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
    }




};
