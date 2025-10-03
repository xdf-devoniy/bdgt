const categorySelect = document.getElementById('transactionCategory');
const typeSelect = document.getElementById('transactionType');

const renderCategories = () => {
    if (!categorySelect || !typeSelect) {
        return;
    }

    const raw = categorySelect.dataset.categoryOptions;
    if (!raw) {
        return;
    }

    let options;
    try {
        options = JSON.parse(raw);
    } catch (error) {
        console.error('Failed to parse category options', error);
        return;
    }

    const type = typeSelect.value;
    const allowed = options[type] ?? [];

    const currentValue = categorySelect.value;
    const placeholder = categorySelect.querySelector('option[value=""]');

    categorySelect.innerHTML = '';
    if (placeholder) {
        categorySelect.appendChild(placeholder);
    } else {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Select category';
        categorySelect.appendChild(option);
    }

    allowed.forEach((item) => {
        const option = document.createElement('option');
        option.value = String(item.id);
        option.textContent = item.name;
        categorySelect.appendChild(option);
    });

    if (allowed.some((item) => String(item.id) === currentValue)) {
        categorySelect.value = currentValue;
    } else {
        categorySelect.value = '';
    }
};

if (categorySelect && typeSelect) {
    renderCategories();
    typeSelect.addEventListener('change', renderCategories);
}

const transactionModalEl = document.getElementById('transactionModal');
if (transactionModalEl) {
    const shouldShow = transactionModalEl.dataset.forceShow === '1';
    if (shouldShow && typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(transactionModalEl);
        modal.show();
    }
}

const transactionForm = transactionModalEl?.querySelector('form');
if (transactionForm) {
    transactionForm.addEventListener('submit', (event) => {
        if (!transactionForm.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }

        transactionForm.classList.add('was-validated');
    });
}
