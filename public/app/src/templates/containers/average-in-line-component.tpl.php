<?php

    $component = $component ?? '';
    $filterPanel = $filterPanel ?? '';
    $methodSend = $methodSend ?? '';
    $endpointSend = $endpointSend ?? '';
?>

<section class="component-wrapper-line">
    <section class="component-wrapper-table component">
        <?= $filterPanel ?>
        <?= $component ?>
    </section>
</section>

<script type="module">
import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';

function attachDeleteTrigger() {
    ComponentFunctions.attachDeleteTrigger({
        triggerSelector: '[table-r-id] .btn-delete.row-action',
        containerSelectorAttribute: 'table-r-id',
        method: '<?= $methodSend ?>',
        endpoint: '<?= $endpointSend ?>',
    });
}

function attachInputButtonTrigger() {
    ComponentFunctions.attachInputButtonTrigger({
        containerSelector: '[table-r-id]',
        buttonSelector: 'td .edit-row-button',
        inputSelector: 'input.edit-row-input',
        searchRootSelector: 'td', // можно заменить на 'td' или '.row-block'
        attributes: ['value', 'data-row-id'],
        method: 'lead.edit.cell',
        endpoint: '/api/leads',
    });
}

function watchInputValueChange() {
    ComponentFunctions.watchInputValueChange({
        inputSelector: '[table-r-id] input.edit-row-input',
        onChange: (oldValue, newValue, inputElement) => {
            const container = inputElement.closest('td');
            const wrapper = container?.querySelector('.cell-actions-wrapper');
            const inputOldValue = inputElement.getAttribute("old-value") ?? null;
            if (inputOldValue !== newValue && wrapper) {
                wrapper.style.display = 'flex';
            }else if (inputOldValue === newValue && wrapper) {
                wrapper.style.display = '';
            }
        },
        onBlur: (inputElement, previous) => {
            const container = inputElement.closest('td');
            const wrapper = container?.querySelector('.cell-actions-wrapper');
            // inputElement.classList.add('activated');
            const oldValue = inputElement.getAttribute("old-value") ?? null;
            if (oldValue !== null ) {
                inputElement.value = oldValue;
                previous.value = oldValue;
            }
            if (wrapper) {
                wrapper.style.display = '';
            }
        }
    });


}

// Первый запуск для уже существующих элементов
attachDeleteTrigger();
attachInputButtonTrigger();
watchInputValueChange();

// Следим за изменениями в контейнере [table-r-id]
const targetNode = document.querySelector('[table-r-id]');
if (!targetNode) {
    console.warn('Container [table-r-id] not found');
} else {
    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                attachDeleteTrigger();
                attachInputButtonTrigger();
                watchInputValueChange();
                break; // один раз достаточно
            }
        }
    });

    observer.observe(targetNode, {
        childList: true,
        subtree: true
    });
}
</script>
