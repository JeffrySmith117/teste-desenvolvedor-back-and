/**
 * Banco de Vagas - comportamentos da interface.
 * JavaScript puro, sem dependências.
 */
document.addEventListener('DOMContentLoaded', () => {
    setupConfirmations();
    setupBulkSelection();
    setupAutoSubmit();
    setupDismissibleAlerts();
});

/**
 * Formulários com data-confirm pedem confirmação antes de enviar.
 */
function setupConfirmations() {
    document.querySelectorAll('form[data-confirm]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (!window.confirm(form.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });
}

/**
 * Seleção de itens para exclusão em massa.
 * - data-check-all: checkbox "selecionar todos"
 * - data-check-item: checkbox de cada linha
 * - data-bulk-submit: botão habilitado só com itens selecionados
 * - data-bulk-count: contador de selecionados
 */
function setupBulkSelection() {
    const checkAll = document.querySelector('[data-check-all]');
    const items = Array.from(document.querySelectorAll('[data-check-item]'));
    const submit = document.querySelector('[data-bulk-submit]');
    const counter = document.querySelector('[data-bulk-count]');

    if (!items.length) {
        if (checkAll) checkAll.disabled = true;
        return;
    }

    const update = () => {
        const selected = items.filter((item) => item.checked).length;

        if (counter) counter.textContent = selected;
        if (submit) submit.disabled = selected === 0;

        if (checkAll) {
            checkAll.checked = selected === items.length;
            checkAll.indeterminate = selected > 0 && selected < items.length;
        }
    };

    checkAll?.addEventListener('change', () => {
        items.forEach((item) => {
            item.checked = checkAll.checked;
        });
        update();
    });

    items.forEach((item) => item.addEventListener('change', update));

    update();
}

/**
 * Formulários com data-auto-submit são enviados ao mudar um <select>
 * (usado no seletor de itens por página).
 */
function setupAutoSubmit() {
    document.querySelectorAll('form[data-auto-submit]').forEach((form) => {
        form.querySelectorAll('select').forEach((select) => {
            select.addEventListener('change', () => form.submit());
        });
    });
}

/**
 * Alertas com data-dismissible somem ao clicar ou após 6 segundos.
 */
function setupDismissibleAlerts() {
    document.querySelectorAll('[data-dismissible]').forEach((alert) => {
        const remove = () => alert.remove();

        alert.addEventListener('click', remove);
        setTimeout(remove, 6000);
    });
}