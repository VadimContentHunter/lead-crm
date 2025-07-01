/**
 * JsonRpcResponseParser
 *
 * Валидирует и извлекает информацию из ответа JSON-RPC 2.0.
 * Используется на клиенте после получения ответа от сервера.
 *
 * @example
 * const parser = new JsonRpcResponseParser(response);
 * if (parser.isValid()) {
 *   if (parser.isSuccess()) console.log(parser.getResult());
 *   else console.warn(parser.getError());
 * }
 */
export class JsonRpcResponseParser {
  /**
   * @param {object|string} response - ответ сервера (объект или JSON-строка)
   */
  constructor(response) {
    try {
      this.raw = typeof response === 'string'
        ? JSON.parse(response)
        : response;

      this.valid = this._validate(this.raw);
    } catch (e) {
      this.valid = false;
      this.error = { code: -32700, message: 'Parse error', details: e.message };
    }
  }

  /**
   * Проверка структуры JSON-RPC 2.0
   * @param {object} res
   * @returns {boolean}
   * @private
   */
  _validate(res) {
    if (!res || typeof res !== 'object') return false;
    if (res.jsonrpc !== '2.0') return false;
    if (!('id' in res)) return false;
    if (!('result' in res || 'error' in res)) return false;

    if ('error' in res) {
      const err = res.error;
      return typeof err.code === 'number' && typeof err.message === 'string';
    }

    return true;
  }

  /**
   * Является ли ответ корректным
   * @returns {boolean}
   */
  isValid() {
    return this.valid;
  }

  /**
   * Успешен ли ответ (есть result, нет error)
   * @returns {boolean}
   */
  isSuccess() {
    return this.valid && 'result' in this.raw;
  }

  /**
   * Есть ли ошибка
   * @returns {boolean}
   */
  isError() {
    return this.valid && 'error' in this.raw;
  }

  /**
   * Возвращает result (если есть)
   * @returns {any}
   */
  getResult() {
    return this.isSuccess() ? this.raw.result : null;
  }

  /**
   * Возвращает error (если есть)
   * @returns {{code: number, message: string, data?: any}|null}
   */
  getError() {
    return this.isError() ? this.raw.error : null;
  }

  /**
   * Возвращает id запроса
   * @returns {string|number|null}
   */
  getId() {
    return this.raw?.id ?? null;
  }

  /**
   * Возвращает полный исходный объект
   * @returns {object|null}
   */
  toObject() {
    return this.raw ?? null;
  }
}
