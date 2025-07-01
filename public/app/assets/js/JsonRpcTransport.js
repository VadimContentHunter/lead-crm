import { JsonRpcFormatter } from './JsonRpcFormatter.js';
import { JsonRpcResponseParser } from './JsonRpcResponseParser.js';

/**
 * JsonRpcTransport
 *
 * Универсальный клиент для отправки JSON-RPC 2.0-запросов и обработки ответов.
 *
 * Использует:
 * - JsonRpcFormatter — для формирования запроса;
 * - fetch — для отправки;
 * - JsonRpcResponseParser — для разбора ответа;
 * - Коллбеки onContentUpdate / onData / onError — для реакции на результат.
 *
 * @example
 * import { JsonRpcTransport } from './JsonRpcTransport.js';
 *
 * const transport = new JsonRpcTransport('user.add', {
 *   endpoint: '/api',
 *   onContentUpdate: (res) => {
 *     const el = document.querySelector(res.selector);
 *     if (el) el.innerHTML = res.content;
 *   },
 *   onData: (payload) => {
 *     console.log('Получены данные:', payload);
 *   },
 *   onError: (error) => {
 *     alert(`Ошибка: ${error.message}`);
 *   }
 * });
 *
 * // Отправка данных
 * transport.send({
 *   username: 'admin',
 *   password: '1234'
 * });
 */
export class JsonRpcTransport {
    /**
     * @param {string} method - имя JSON-RPC метода
     * @param {object} options
     * @param {string} [options.endpoint="/api"] - путь до API
     * @param {Function} [options.onContentUpdate] - обработка { selector, content }
     * @param {Function} [options.onData] - обработка data.payload
     * @param {Function} [options.onError] - обработка ошибок
     */
    constructor(method, {
        endpoint = "/api",
        onContentUpdate,
        onData,
        onError
    } = {}) {
        this.formatter = new JsonRpcFormatter(method);
        this.endpoint = endpoint;
        this.onContentUpdate = onContentUpdate;
        this.onData = onData;
        this.onError = onError;
    }

    /**
     * Отправляет запрос, принимает ответ, вызывает нужный обработчик
     * @param {object} jsonContent - любые данные для передачи
     */
    async send(jsonContent) {
        const request = this.formatter.fromObject(jsonContent);

        try {
            const res = await fetch(this.endpoint, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                credentials: "same-origin",
                body: JSON.stringify(request)
            });

            const json = await res.json();
            const parser = new JsonRpcResponseParser(json);

            if (!parser.isValid()) {
                throw new Error("Некорректный JSON-RPC ответ");
            }

            if (parser.isError()) {
                if (typeof this.onError === 'function') {
                    this.onError(parser.getError(), parser);
                } else {
                    console.error("[RPC Error]", parser.getError());
                }
                return;
            }

            const result = parser.getResult();

            if (result?.type === 'content_update' && typeof this.onContentUpdate === 'function') {
                this.onContentUpdate(result, parser);
            } else if (result?.type === 'data' && typeof this.onData === 'function') {
                this.onData(result.payload, parser);
            } else {
                console.log('[RPC Response]', result);
            }

        } catch (e) {
            if (typeof this.onError === 'function') {
                this.onError({ code: -1, message: e.message }, null);
            } else {
                console.error('[Transport Error]', e);
            }
        }
    }
}
