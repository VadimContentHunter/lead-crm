function updateLeadTable(response, tableSelector = '#lead-table-1') {
    const table = document.querySelector(tableSelector);

    if (!table) {
        console.warn('[LeadTable] Таблица не найдена:', tableSelector);
        return;
    }

    const tbody = table.querySelector('tbody');
    if (!tbody) {
        console.warn('[LeadTable] tbody не найден в таблице');
        return;
    }

    // Очищаем старые строки
    tbody.innerHTML = '';

    // Достаём leads из первого success-блока
    const leadsBlock = Array.isArray(response?.payload)
        ? response.payload.find(item => item.type === 'success' && Array.isArray(item.leads))
        : null;

    const leads = leadsBlock ? leadsBlock.leads : [];

    if (leads.length === 0) {
        tbody.innerHTML = `<tr><td colspan="12" style="text-align: center;">Нет данных</td></tr>`;
        return;
    }

    for (const lead of leads) {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${lead.id ?? '-'}</td>
            <td>${lead.contact ?? '-'}</td>
            <td>${lead.full_name ?? '-'}</td>
            <td>${lead.account_manager_id ?? '-'}</td>
            <td>${lead.status_id ?? '-'}</td>
            <td>${lead.source_id ?? '-'}</td>
            <td>${lead.potential ?? '-'}</td>
            <td>${lead.current ?? '-'}</td>
            <td>${lead.drain ?? '-'}</td>
            <td>${lead.address ?? '-'}</td>
            <td>
                <a href="/page/lead-edit/${lead.id}" class="btn-table-action btn-edit" data-id="${lead.id}">✏️</a>
            </td>
            <td>
                <button type="button" class="btn-drain" data-id="${lead.id}">🗑️</button>
            </td>
        `;
        tbody.appendChild(row);
    }
}
