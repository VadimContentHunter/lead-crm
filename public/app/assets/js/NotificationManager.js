export class NotificationManager {
    constructor({ containerSelector, maxVisible = 3, timeout = 5000, timeOpacity = 500 } = {}) {
        this.container = document.querySelector(containerSelector);
        this.maxVisible = --maxVisible;
        this.timeout = timeout;
        this.timeOpacity = timeOpacity;
        this.queue = [];
        this.hasActiveDelete = false;
    }

    /**
     * Добавить уведомление в очередь
     */
    add(message, type = 'info') {
        const item = this._createNotification(message, type);
        this.queue.push(item);
        this._processQueue();
        this._showNotification();
    }

    _processQueue() {
        if (this.queue.length > 0 && this.hasActiveDelete === false) {
            const item = this.queue[0];

            this._showNotification();
            this._removeNotification(item);
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
        if (this.hasActiveDelete === false) {
            this.hasActiveDelete = true;
            // setTimeout(() => {
                await this._delay(this.timeout);
                notification.classList.add('hide');
                await this._delay(this.timeOpacity);

                this.container.removeChild(notification);
                this.queue.splice(0, 1);
                this.hasActiveDelete = false;

                this._processQueue();
            // }, this.timeout);
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
        return notify;
    }
}
