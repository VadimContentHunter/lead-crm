/**
 * Функция связывает кнопку и контейнер по селекторам.
 * @param {string|HTMLElement} buttonSelector - CSS-селектор кнопки.
 * @param {string} containerSelector - CSS-селектор контейнера.
 * @param {function} callback - Функция, которая вызывается при клике на кнопку.
 */
function bindButtonToContainer(buttonSelector, containerSelector, callback) {
    const button = buttonSelector instanceof HTMLElement
        ? buttonSelector
        : document.querySelector(buttonSelector);
    const container = document.querySelector(containerSelector);

    if (!button || !container) {
        console.warn('Кнопка или контейнер не найдены');
        return;
    }

    button.addEventListener('click', function () {
        callback(container);
    });
}

function onClickOutside(element, callback, excludeElements = []) {
    function handleClick(event) {
        const clickedInsideElement = element.contains(event.target);
        const clickedOnExcluded = excludeElements.some(exEl => exEl.contains(event.target));

        if (!clickedInsideElement && !clickedOnExcluded) {
            callback(event);
        }
    }

    document.addEventListener('click', handleClick);

    return function removeListener() {
        document.removeEventListener('click', handleClick);
    };
}


document.addEventListener('DOMContentLoaded', function () {
    const notificationContainer = document.querySelector('.notification-container');
    const overlayMain = document.querySelector('.overlay-main');
    const overlayLoader = document.querySelector('#overlay-loader');
    
    const toggleScroll = (shouldHide) => {
        const contentContainerElement = document.querySelector('.content-container');
        if (contentContainerElement instanceof HTMLElement) {
            contentContainerElement.style.overflow = shouldHide ? 'hidden' : '';
        }
    };

    const buttonSource = document.querySelector('#inv-sources-btn');
    bindButtonToContainer(buttonSource, '.overlay-content', function (container) {
        if (container instanceof HTMLElement) {
            container.style = 'display: flex;';
            toggleScroll(true);

            const rightSidebar = container.querySelector('.right-sidebar.inv-source-menu-id');
            if (rightSidebar instanceof HTMLElement) {
                rightSidebar.style = 'display: flex;';

                // Добавляем обработчик с задержкой, чтобы клик по кнопке не сработал
                const removeListener = onClickOutside(rightSidebar, () => {
                    rightSidebar.style.display = 'none';
                    container.style.display = 'none';
                    toggleScroll(false);

                    removeListener();
                }, [buttonSource, notificationContainer, overlayMain, overlayLoader]);

            }
        }
    });


    const buttonStatus = document.querySelector('#inv-statuses-btn');
    bindButtonToContainer(buttonStatus, '.overlay-content', function (container) {
        if (container instanceof HTMLElement) {
            container.style = 'display: flex;';
            toggleScroll(true);

            const rightSidebar = container.querySelector('.right-sidebar.inv-status-menu-id');
            if (rightSidebar instanceof HTMLElement) {
                rightSidebar.style = 'display: flex;';

                const removeListener = onClickOutside(rightSidebar, () => {
                    rightSidebar.style.display = 'none';
                    container.style.display = 'none';
                    toggleScroll(false);

                    removeListener();
                }, [buttonStatus, notificationContainer, overlayMain, overlayLoader]);
            }
        }
    });

    const buttonInvLead = document.querySelector('#add-inv-lead-btn');
    bindButtonToContainer(buttonInvLead, '.overlay-content', function (container) {
        if (container instanceof HTMLElement) {
            container.style = 'display: flex;';
            toggleScroll(true);

            const rightSidebar = container.querySelector('.right-sidebar.inv-lead-menu-id');
            if (rightSidebar instanceof HTMLElement) {
                rightSidebar.style = 'display: flex;';

                const removeListener = onClickOutside(rightSidebar, () => {
                    rightSidebar.style.display = 'none';
                    container.style.display = 'none';
                    toggleScroll(false);

                    removeListener();
                }, [buttonInvLead, notificationContainer, overlayMain, overlayLoader]);
            }
        }
    });

});