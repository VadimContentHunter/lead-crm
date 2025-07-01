import { ScriptExecutor } from './ScriptExecutor.js';

/**
 * Создаёт обработчик для JsonRpcTransport → onContentUpdate,
 * который обновляет DOM и выполняет скрипты.
 *
 * @returns {(result: { selector: string, content: string }) => void}
 *
 * @example
 * const rpc = new JsonRpcTransport('auth.login', {
 *   onContentUpdate: createContentUpdateHandler()
 * });
 */
export function createContentUpdateHandler() {
    const executor = new ScriptExecutor();

    return function handleContentUpdate(result) {
        const { selector, content } = result;

        if (!selector || typeof content !== 'string') {
            console.warn('[onContentUpdate] Некорректный результат:', result);
            return;
        }

        const target = document.querySelector(selector);
        if (!target) {
            console.warn(`[onContentUpdate] Элемент не найден: ${selector}`);
            return;
        }

        // Заменить контент (без скриптов)
        const fragment = executor.extractContentWithoutScripts(content);
        target.innerHTML = ''; // очистка
        target.appendChild(fragment);

        // Выполнить скрипты отдельно
        executor.runFromHtml(content);
    };
}
