import './dashboard.js';
import './transactions.js';

const shortcutButton = document.querySelector('[data-shortcut="true"][data-bs-target="#transactionModal"]');
if (shortcutButton) {
    document.addEventListener('keydown', (event) => {
        if (event.key.toLowerCase() === 'n' && !event.metaKey && !event.ctrlKey && !event.altKey) {
            event.preventDefault();
            shortcutButton.click();
        }
    });
}
