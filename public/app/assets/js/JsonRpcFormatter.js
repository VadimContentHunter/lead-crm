/**
 * JsonRpcFormatter
 *
 * Формирует JSON-RPC 2.0-запрос из различных источников данных.
 * Используется для стандартизации клиент-серверного взаимодействия.
 *
 * @example
 * import { JsonRpcFormatter } from './JsonRpcFormatter.js';
 *
 *const formatter = new JsonRpcFormatter('user.login');
 *
 * // 1. Из формы
 * const formEl = document.querySelector('#login-form');
 * const request1 = formatter.fromForm(formEl);
 *
 * // 2. Из массива
 * const request2 = formatter.fromArray(['admin', '1234']);
 *
 * // 3. Из объекта
 * const request3 = formatter.fromObject({ username: 'admin', password: '1234' });
 *
 * // 4. Из колбэка
 * const request4 = formatter.fromCallback(() => ({
 *      token: getSessionToken(),
 *      data: collectInputs()
 * }));
 *
 * // 5. По селектору
 * const request5 = formatter.fromSelectorInputs('.modal-auth');
 */
export class JsonRpcFormatter {
    /**
     * @param {string} method - имя вызываемого метода RPC
     */
    constructor(method) {
        this.method = method;
    }

    /**
     * Генерирует уникальный ID на основе даты и времени
     * @returns {string} строка в формате 20250701-163242-abcd
     */
    generateId() {
        const now = new Date();
        return (
            now.getFullYear().toString() +
            String(now.getMonth() + 1).padStart(2, '0') +
            String(now.getDate()).padStart(2, '0') + "-" +
            String(now.getHours()).padStart(2, '0') +
            String(now.getMinutes()).padStart(2, '0') +
            String(now.getSeconds()).padStart(2, '0') + "-" +
            Math.random().toString(36).substring(2, 6)
        );
    }

    /**
     * Формирует RPC-запрос из произвольного объекта
     * @param {object} obj
     * @returns {object}
     */
    fromObject(obj) {
        return this._build(obj);
    }

    /**
     * Формирует RPC-запрос из массива (будет преобразован в {0: ..., 1: ...})
     * @param {Array} arr
     * @returns {object}
     */
    fromArray(arr) {
        const indexed = Object.assign({}, arr);
        return this._build(indexed);
    }

    /**
     * Формирует RPC-запрос на основе callback-функции, которая возвращает объект
     * @param {function(): object} fn
     * @returns {object}
     */
    fromCallback(fn) {
        const data = typeof fn === 'function' ? fn() : {};
        return this._build(data);
    }

    /**
     * Формирует RPC-запрос из HTML формы (FormData)
     * @param {HTMLFormElement} form
     * @returns {object}
     */
    fromForm(form) {
        if (!(form instanceof HTMLFormElement)) {
            throw new Error('fromForm: ожидается элемент <form>');
        }
        const formData = new FormData(form);
        const params = Object.fromEntries(formData.entries());
        return this._build(params);
    }

    /**
     * Формирует RPC-запрос, собирая все <input>, <select>, <textarea> внутри контейнера/селектора
     * @param {string|HTMLElement} selectorOrElement
     * @returns {object}
     */
    fromSelectorInputs(selectorOrElement) {
        let root = selectorOrElement;
        if (typeof selectorOrElement === 'string') {
            root = document.querySelector(selectorOrElement);
        }
        if (!(root instanceof HTMLElement)) {
            throw new Error('fromSelectorInputs: передан некорректный селектор или элемент');
        }

        const fields = root.querySelectorAll('input[name], select[name], textarea[name]');
        const params = {};

        fields.forEach((el) => {
            if (el.type === 'checkbox') {
                params[el.name] = el.checked;
            } else if (el.type === 'radio') {
                if (el.checked) params[el.name] = el.value;
            } else {
                params[el.name] = el.value;
            }
        });

        return this._build(params);
    }

    /**
     * Внутренний метод: формирует JSON-RPC 2.0 объект
     * @param {object} params
     * @returns {object}
     * @private
     */
    _build(params) {
        return {
            jsonrpc: "2.0",
            method: this.method,
            params,
            id: this.generateId()
        };
    }
}
