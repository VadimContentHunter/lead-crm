/**
 * ScriptExecutor
 *
 * Класс для безопасного выполнения <script> из HTML-фрагментов.
 * Позволяет:
 * - извлечь DOM-контент без <script>;
 * - выполнить inline <script> один раз;
 * - отслеживать исполненные скрипты по хешу.
 *
 * Применяется при динамической вставке HTML: через innerHTML, AJAX, update_content.
 * 
 * @example
 * import { ScriptExecutor } from './ScriptExecutor.js';
 *
 *    const executor = new ScriptExecutor();
 *
 *    const html = `
 *      <div class="user-panel">Контент</div>
 *      <script>console.log("init")</script>
 *    `;
 *
 *    // получить контент без скриптов
 *    const content = executor.extractContentWithoutScripts(html);
 *
 *    // вставить в DOM
 *    document.querySelector('.target').appendChild(content);
 *
 *    // отдельно выполнить скрипты
 *    executor.runFromHtml(html); // или executor.runFromHtml(content)
 */
export class ScriptExecutor {
    constructor() {
        /**
         * @type {Set<string>} — Хеши уже исполненных скриптов
         */
        this.executedScripts = new Set();
    }

    /**
     * Выполняет все inline <script> в переданном HTML или DOM-фрагменте.
     * Срабатывает один раз для каждого скрипта.
     *
     * @param {string | HTMLElement | DocumentFragment} htmlOrElement
     */
    runFromHtml(htmlOrElement) {
        const wrapper = this._toFragment(htmlOrElement);
        const scripts = wrapper.querySelectorAll('script');

        scripts.forEach(script => {
            if (script.type && script.type !== 'text/javascript' && script.type !== '') return;

            const code = script.textContent.trim();
            const hash = this.hashScript(code);
            if (this.executedScripts.has(hash)) return;

            this.executedScripts.add(hash);
            this.injectScript(code);
        });
    }

    /**
     * Извлекает только содержимое без <script>-тегов
     *
     * @param {string} html
     * @returns {DocumentFragment}
     */
    extractContentWithoutScripts(html) {
        const template = document.createElement('template');
        template.innerHTML = html.trim();

        const scripts = template.content.querySelectorAll('script');
        scripts.forEach(script => script.remove());

        return template.content;
    }

    /**
     * Преобразует строку или DOM в DocumentFragment
     * @private
     * @param {string|HTMLElement|DocumentFragment} input
     * @returns {DocumentFragment}
     */
    _toFragment(input) {
        if (typeof input === 'string') {
            const template = document.createElement('template');
            template.innerHTML = input.trim();
            return template.content;
        } else if (input instanceof HTMLElement) {
            const frag = document.createDocumentFragment();
            frag.appendChild(input.cloneNode(true));
            return frag;
        } else if (input instanceof DocumentFragment) {
            return input;
        } else {
            throw new Error('ScriptExecutor: неподдерживаемый тип входа');
        }
    }

    /**
     * Вставляет и выполняет код через <script>
     * @param {string} code
     */
    injectScript(code) {
        const script = document.createElement('script');
        script.textContent = code;
        document.body.appendChild(script);
        script.remove();
    }

    /**
     * Создаёт простой числовой хеш от строки
     * @param {string} code
     * @returns {string}
     */
    hashScript(code) {
        let hash = 0;
        for (let i = 0; i < code.length; i++) {
            hash = ((hash << 5) - hash) + code.charCodeAt(i);
            hash |= 0;
        }
        return hash.toString();
    }
}
