export class ConfirmDialog {
    /**
     * Показывает модальное окно подтверждения
     * @param {string} title - Заголовок окна
     * @param {string} message - Сообщение
     * @param {string|null} [withOverlay = null] - селектор для оверлея
     * @returns {Promise<boolean>}
     */
    static show(title = 'Подтверждение', message = 'Вы уверены?', withOverlay = null) {
        return new Promise((resolve) => {
            if (typeof withOverlay === "string") ConfirmDialog.#showOverlay(withOverlay);

            const dialog = document.createElement('div');
            dialog.className = 'component confirm-dialog';
            dialog.innerHTML = `
                <div class="confirm-dialog__title"><h2>${title}</h2></div>
                <div class="confirm-dialog__message"><p>${message}</p></div>
                <div class="confirm-dialog__buttons">
                    <button class="btn-default button-danger">Да</button>
                    <button class="btn-default button-secondary">Нет</button>
                </div>
            `;
            // document.body.appendChild(dialog);
            ConfirmDialog.#addInOverlay(dialog);

            const yes = dialog.querySelector('.button-danger');
            const no = dialog.querySelector('.button-secondary');

            const cleanup = () => {
                dialog.remove();
                if (typeof withOverlay === "string") ConfirmDialog.#hideOverlay(withOverlay);
            };

            yes.addEventListener('click', () => {
                setTimeout(() => {
                    cleanup();
                    resolve(true);
                }, 1);
            });

            no.addEventListener('click', () => {
                setTimeout(() => {
                    cleanup();
                    resolve(false);
                }, 1);
            });
        });
    }

    // 🔒 Приватный метод для показа overlay
    static #showOverlay(selector = '.overlay-main') {
        const overlay = document.querySelector(selector);
        if (overlay instanceof HTMLElement) {
            overlay.style.display = 'flex';
        }
    }

    // 🔒 Приватный метод для скрытия overlay
    static #hideOverlay(selector = '.overlay-main') {
        const overlay = document.querySelector(selector);
        if (overlay instanceof HTMLElement) {
            overlay.style.display = '';
        }
    }

    // 🔒 Приватный метод для добавления контента в overlay
    static #addInOverlay(content = '', selector = '.overlay-main') {
        const overlay = document.querySelector(selector);
        if (overlay instanceof HTMLElement) {
            overlay.appendChild(content);
        }
    }
}
