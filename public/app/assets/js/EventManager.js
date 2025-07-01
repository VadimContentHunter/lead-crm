/**
 * EventManager
 *
 * Класс для централизованного управления обработчиками событий.
 * Позволяет отслеживать, снимать и полностью очищать слушателей у DOM-элементов.
 *
 * Применим для динамически создаваемых или удаляемых компонентов (например, после update_content).
 *
 * @example
 * import { EventManager } from './EventManager.js';
 *    const em = new EventManager();
 *    const btn = document.querySelector('#delete-user');
 *    const onClick = () => alert('Удалено!');
 *
 *    // Привязка
 *    em.on(btn, 'click', onClick);
 *
 *    // Позже можно снять конкретный обработчик
 *    em.off(btn, 'click', onClick);
 *
 *    // Или очистить все события с этого элемента
 *    em.clearAllFor(btn);
 */
export class EventManager {
    constructor() {
        /**
         * @type {Map<HTMLElement, Record<string, Set<Function>}
         */
        this.registry = new Map(); // элемент → { событие → Set<функций> }
    }

    /**
     * Назначает слушатель события и регистрирует его.
     *
     * @param {HTMLElement} element - DOM-элемент
     * @param {string} event - тип события (например, "click")
     * @param {Function} handler - функция-обработчик
     * @param {boolean|AddEventListenerOptions} [options] - опции прослушивания
     */
    on(element, event, handler, options) {
        element.addEventListener(event, handler, options);

        if (!this.registry.has(element)) this.registry.set(element, {});
        const events = this.registry.get(element);
        if (!events[event]) events[event] = new Set();
        events[event].add(handler);
    }

    /**
     * Удаляет указанный обработчик события и обновляет реестр.
     *
     * @param {HTMLElement} element - DOM-элемент
     * @param {string} event - тип события
     * @param {Function} handler - ранее зарегистрированный обработчик
     */
    off(element, event, handler) {
        element.removeEventListener(event, handler);
        const events = this.registry.get(element);
        if (events?.[event]) events[event].delete(handler);
    }

    /**
     * Полностью очищает все обработчики для заданного элемента.
     *
     * @param {HTMLElement} element
     */
    clearAllFor(element) {
        const events = this.registry.get(element);
        if (!events) return;

        for (const [event, handlers] of Object.entries(events)) {
            for (const handler of handlers) {
                element.removeEventListener(event, handler);
            }
        }
        this.registry.delete(element);
    }
}
