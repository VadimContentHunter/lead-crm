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
     * Назначает обработку любого input-контейнера через JSON-RPC по клику на кнопку
     *
     * @param {{
     *   triggerSelector: string,
     *   containerSelector: string,
     *   method: string,
     *   endpoint?: string
     * }} config
     */
    attachJsonRpcInputTrigger({ triggerSelector, containerSelector, method, endpoint = '/api' }) {
        const trigger = document.querySelector(triggerSelector);
        const container = document.querySelector(containerSelector);

        if (!trigger || !container) {
            console.warn('[ComponentFunctions] Кнопка или контейнер не найден');
            return;
        }

        const transport = new JsonRpcTransport(method, {
            endpoint,
            onContentUpdate: () => { }, // заглушка
            onData: (payload) => {
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
                console.error('[JsonRpcTransport] Ошибка:', error.message);
            }
        });

        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            transport.sendFromSelectorInputs(container);
        });
    }
};
