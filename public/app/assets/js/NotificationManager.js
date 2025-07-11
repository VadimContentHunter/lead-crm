export class NotificationManager {
    constructor({ containerSelector, maxVisible = 3, timeout = 5000, timeOpacity = 500 } = {}) {
        this.container = document.querySelector(containerSelector);
        this.maxVisible = --maxVisible;
        this.timeout = timeout;
        this.timeOpacity = timeOpacity;
        this.queue = [];
        this.objHasActiveDelete = null;
        this.timers = new Map();
    }

    /**
     * Добавить уведомление в очередь
     */
    add(message, type = 'info') {
        const item = this._createNotification(message, type);
        item.id = Date.now() + Math.random();
        item.addEventListener('mouseenter', () => {
            item._isHovered = true;

            if (item._isHiding) {
                item.classList.remove('hide');
                item._isHiding = false;
            }

            this.stopTimer(item);
        });
        item.addEventListener('mouseleave', () => {
            item._isHovered = false;
            this.startTimer(item);
        });
        this.queue.push(item);
        this._processQueue();
        this._showNotification();
    }

    _processQueue() {
        if (this.queue.length > 0 && this.objHasActiveDelete === null) {
            this._showNotification();
            const item = this.queue[0];
            this.objHasActiveDelete = item;
            this.startTimer(item);


            // this._removeNotification(item);
        }
    }

    _showNotification() {
        for (let i = 0; i < this.queue.length; i++) {
            const element = this.queue[i];
            this.container.appendChild(element);
            if (i === this.maxVisible) {
                return;
            }
        }
    }

    async _removeNotification(notification) {
        this.stopTimer(notification);

        notification.classList.add('hide');
        notification._isHiding = true;
        await this._delay(this.timeOpacity);

        // Если пользователь навёл курсор, удаление отменяется
        if (notification._isHovered) {
            notification.classList.remove('hide');
            notification._isHiding = false;
            this.startTimer(notification); // Запускаем таймер заново
            return;
        }

        this.container.removeChild(notification);
        this.queue = this.queue.filter(el => el !== notification);
        this.objHasActiveDelete = null;

        this._processQueue();
        // }, this.timeout);
        // }
    }

    // Запуск таймера для конкретного элемента
    startTimer(el) {
        if (Number(el.id) <= 0 || this.objHasActiveDelete?.id !== el.id)
            return;

        if (el._isHovered) return;

        this.stopTimer(el);
        const timerId = setTimeout(async () => {
            // notification.classList.add('hide');
            // await this._delay(this.timeOpacity);

            this._removeNotification(el)
        }, this.timeout); // 2 секунды
        this.timers.set(el, timerId);
    }

    // Остановка таймера
    stopTimer(el) {
        if (this.timers.has(el)) {
            clearTimeout(this.timers.get(el));
            this.timers.delete(el);
            // this.objHasActiveDelete = null;
        }
    }


    /**
     * Вспомогательная задержка
     */
    _delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    /**
     * Генерация DOM элемента
     */
    _createNotification(message, type) {
        const notify = document.createElement('div');
        notify.className = `notify notify-${type}`;
        notify.innerHTML = `
            <span class="text">${message}</span>
            <button class="notify-close" type="button">
                <i class="fa-solid fa-xmark"></i>
            </button>
        `;

        // Находим кнопку
        const closeButton = notify.querySelector('.notify-close');

        // Вешаем обработчик удаления
        closeButton.addEventListener('click', () => {
            this.forceRemoveNotification(notify);
        });

        return notify;
    }

    forceRemoveNotification(notification) {
        this.stopTimer(notification); // Останавливаем таймер, если есть

        // Удаляем из DOM, если он там есть
        if (this.container.contains(notification)) {
            this.container.removeChild(notification);
        }

        // Убираем из очереди
        this.queue = this.queue.filter(el => el !== notification);

        // Если удаляемый был активным, сбрасываем активное удаление
        if (this.objHasActiveDelete === notification) {
            this.objHasActiveDelete = null;
            this._processQueue(); // Запускаем обработку очереди
        } else {
            this._showNotification();
        }
    }


}
