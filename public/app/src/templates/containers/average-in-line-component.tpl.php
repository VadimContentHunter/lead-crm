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

    function attachEditInputTrigger() {
       ComponentFunctions.attachInputButtonTrigger({
            containerSelector: '[table-r-id]',
            buttonSelector: 'td .edit-row-button',
            inputSelector: 'input.edit-row-input',
            attributes: ['value', 'data-row-id', 'new-value'],
            method: 'lead.edit.cell',
            endpoint: '/api/leads',
        }); 
    }

    // Первый запуск для уже существующих элементов
    attachDeleteTrigger();

    // Первый запуск для уже существующих элементов
    attachEditInputTrigger();

    // Следим за изменениями в контейнере [table-r-id]
    const targetNode = document.querySelector('[table-r-id]');
    if (!targetNode) {
        console.warn('Container [table-r-id] not found');
    } else {
        const observer = new MutationObserver((mutationsList) => {
            for (const mutation of mutationsList) {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    attachDeleteTrigger();
                    attachEditInputTrigger();
                    break; // один раз достаточно
                }
            }
        });

        observer.observe(targetNode, { childList: true, subtree: true });
    }
</script>
