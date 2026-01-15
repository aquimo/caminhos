/**
 * Sistema de Gestão de Casas para Hospedagem
 * Ficheiro JavaScript Principal
 */

// Esperar que o DOM esteja carregado
document.addEventListener('DOMContentLoaded', function() {
    // Inicialização de componentes
    initTooltips();
    initConfirmations();
    initDatePickers();
    initMasks();
    initAutoComplete();
});

/**
 * Inicializar tooltips
 */
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
            tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
            
            this.tooltip = tooltip;
        });
        
        element.addEventListener('mouseleave', function() {
            if (this.tooltip) {
                this.tooltip.remove();
                delete this.tooltip;
            }
        });
    });
}

/**
 * Inicializar confirmações
 */
function initConfirmations() {
    const confirmElements = document.querySelectorAll('[data-confirm]');
    
    confirmElements.forEach(element => {
        element.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
                return false;
            }
        });
    });
}

/**
 * Inicializar seletores de data
 */
function initDatePickers() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    
    dateInputs.forEach(input => {
        // Definir data mínima como hoje
        if (input.hasAttribute('data-min-today')) {
            const today = new Date().toISOString().split('T')[0];
            input.min = today;
        }
        
        // Adicionar validação
        input.addEventListener('change', function() {
            validateDateInput(this);
        });
    });
}

/**
 * Validar input de data
 */
function validateDateInput(input) {
    const date = new Date(input.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    if (input.hasAttribute('data-min-today') && date < today) {
        input.setCustomValidity('A data não pode ser anterior a hoje');
    } else {
        input.setCustomValidity('');
    }
}

/**
 * Inicializar máscaras de input
 */
function initMasks() {
    // Máscara para telefone
    const phoneInputs = document.querySelectorAll('input[data-mask="phone"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 0) {
                if (value.length <= 9) {
                    value = value.replace(/(\d{3})(\d{3})(\d{3})/, '$1 $2 $3');
                } else {
                    value = value.replace(/(\d{3})(\d{3})(\d{3})(\d+)/, '$1 $2 $3 $4');
                }
            }
            e.target.value = value;
        });
    });
    
    // Máscara para código postal
    const postalInputs = document.querySelectorAll('input[data-mask="postal"]');
    postalInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 4) {
                value = value.replace(/(\d{4})(\d+)/, '$1-$2');
            }
            e.target.value = value;
        });
    });
    
    // Máscara para NIF
    const nifInputs = document.querySelectorAll('input[data-mask="nif"]');
    nifInputs.forEach(input => {
        input.addEventListener('input', function(e) {
            e.target.value = e.target.value.replace(/\D/g, '').slice(0, 9);
        });
    });
}

/**
 * Inicializar autocomplete
 */
function initAutoComplete() {
    const autoCompleteInputs = document.querySelectorAll('input[data-autocomplete]');
    
    autoCompleteInputs.forEach(input => {
        const url = input.getAttribute('data-autocomplete');
        let timeout;
        
        input.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value.trim();
            
            if (query.length < 2) {
                hideAutoComplete(this);
                return;
            }
            
            timeout = setTimeout(() => {
                fetchAutoComplete(url, query, this);
            }, 300);
        });
        
        input.addEventListener('blur', function() {
            setTimeout(() => hideAutoComplete(this), 200);
        });
    });
}

/**
 * Buscar dados para autocomplete
 */
function fetchAutoComplete(url, query, input) {
    fetch(url + '?q=' + encodeURIComponent(query))
        .then(response => response.json())
        .then(data => {
            showAutoComplete(input, data);
        })
        .catch(error => {
            console.error('Erro no autocomplete:', error);
        });
}

/**
 * Mostrar resultados do autocomplete
 */
function showAutoComplete(input, data) {
    hideAutoComplete(input);
    
    if (data.length === 0) return;
    
    const list = document.createElement('div');
    list.className = 'autocomplete-list';
    
    data.forEach(item => {
        const listItem = document.createElement('div');
        listItem.className = 'autocomplete-item';
        listItem.textContent = item.label || item.name || item;
        
        listItem.addEventListener('click', function() {
            input.value = item.label || item.name || item;
            if (item.id) {
                input.setAttribute('data-selected-id', item.id);
            }
            hideAutoComplete(input);
        });
        
        list.appendChild(listItem);
    });
    
    const rect = input.getBoundingClientRect();
    list.style.position = 'absolute';
    list.style.top = (rect.bottom + window.scrollY) + 'px';
    list.style.left = (rect.left + window.scrollX) + 'px';
    list.style.width = rect.width + 'px';
    list.style.zIndex = '1000';
    
    document.body.appendChild(list);
    input.autocompleteList = list;
}

/**
 * Esconder autocomplete
 */
function hideAutoComplete(input) {
    if (input.autocompleteList) {
        input.autocompleteList.remove();
        delete input.autocompleteList;
    }
}

/**
 * Mostrar loading
 */
function showLoading(element) {
    if (element.tagName === 'BUTTON') {
        element.disabled = true;
        const originalText = element.innerHTML;
        element.innerHTML = '<span class="loading"></span> A processar...';
        element.setAttribute('data-original-text', originalText);
    }
}

/**
 * Esconder loading
 */
function hideLoading(element) {
    if (element.tagName === 'BUTTON' && element.hasAttribute('data-original-text')) {
        element.disabled = false;
        element.innerHTML = element.getAttribute('data-original-text');
        element.removeAttribute('data-original-text');
    }
}

/**
 * Enviar formulário com AJAX
 */
function submitFormAjax(form, successCallback, errorCallback) {
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    
    showLoading(submitButton);
    
    fetch(form.action, {
        method: form.method || 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading(submitButton);
        if (data.success) {
            if (successCallback) successCallback(data);
        } else {
            if (errorCallback) errorCallback(data);
        }
    })
    .catch(error => {
        hideLoading(submitButton);
        console.error('Erro:', error);
        if (errorCallback) errorCallback({message: 'Ocorreu um erro. Tente novamente.'});
    });
}

/**
 * Filtrar tabela
 */
function filterTable(input, tableId) {
    const filter = input.value.toLowerCase();
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const cells = rows[i].getElementsByTagName('td');
        let found = false;
        
        for (let j = 0; j < cells.length; j++) {
            const cellText = cells[j].textContent || cells[j].innerText;
            if (cellText.toLowerCase().indexOf(filter) > -1) {
                found = true;
                break;
            }
        }
        
        rows[i].style.display = found ? '' : 'none';
    }
}

/**
 * Exportar para Excel
 */
function exportToExcel(tableId, filename) {
    const table = document.getElementById(tableId);
    const rows = table.getElementsByTagName('tr');
    let csv = [];
    
    for (let i = 0; i < rows.length; i++) {
        const row = [];
        const cells = rows[i].getElementsByTagName('td');
        
        for (let j = 0; j < cells.length; j++) {
            row.push('"' + cells[j].innerText + '"');
        }
        
        csv.push(row.join(','));
    }
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = filename || 'export.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

/**
 * Imprimir tabela
 */
function printTable(tableId) {
    const table = document.getElementById(tableId);
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <html>
            <head>
                <title>Imprimir</title>
                <style>
                    table { border-collapse: collapse; width: 100%; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f2f2f2; }
                </style>
            </head>
            <body>
                <h2>Relatório</h2>
                ${table.outerHTML}
            </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}

/**
 * Formatar moeda
 */
function formatCurrency(value) {
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(value);
}

/**
 * Formatar data
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-PT');
}

/**
 * Calcular dias entre datas
 */
function daysBetween(date1, date2) {
    const oneDay = 24 * 60 * 60 * 1000;
    const firstDate = new Date(date1);
    const secondDate = new Date(date2);
    
    return Math.round(Math.abs((firstDate - secondDate) / oneDay));
}

// Funções globais para uso nos atributos onclick
window.showLoading = showLoading;
window.hideLoading = hideLoading;
window.filterTable = filterTable;
window.exportToExcel = exportToExcel;
window.printTable = printTable;
window.formatCurrency = formatCurrency;
window.formatDate = formatDate;
window.daysBetween = daysBetween;
