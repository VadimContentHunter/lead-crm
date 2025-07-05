<?php

    $component = $component ?? '';
    $filterPanel = $filterPanel ?? '';
    $methodSend = $methodSend ?? '';
    $endpointSend = $endpointSend ?? '';
?>

<section class="component-wrapper-line">
    <section class="component component-table">
        <?= $filterPanel ?>
        <?= $component ?>
    </section>
</section>

<script type="module">
    import { ComponentFunctions } from '/assets/js/ComponentFunctions.js';
    function attachDeleteTrigger() {
        ComponentFunctions.attachJsonRpcInputManyTriggers({
            triggerSelector: '[table-r-id] .row-action.btn-delete',
            containerSelector: '[table-r-id]',
            method: '<?= $methodSend ?>',
            endpoint: '<?= $endpointSend ?>',
            callbackOnData: (response) => {
                ComponentFunctions.replaceLeadTable(response, '[table-r-id]');
            }
        });
    }

    // Первый запуск для уже существующих элементов
    attachDeleteTrigger();

    // Следим за изменениями в контейнере [table-r-id]
    const targetNode = document.querySelector('[table-r-id]');
    if (!targetNode) {
        console.warn('Container [table-r-id] not found');
    } else {
        const observer = new MutationObserver((mutationsList) => {
            for (const mutation of mutationsList) {
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    attachDeleteTrigger();
                    break; // один раз достаточно
                }
            }
        });

        observer.observe(targetNode, { childList: true, subtree: true });
    }
</script>
