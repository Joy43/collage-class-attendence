/* Take-attendance form: pick a status chip per student, or mark everyone at once. */
import { showToast } from '../toast.js';

const rows = () => document.querySelectorAll('[data-status-row]');

function select(row, status) {
    row.querySelectorAll('[data-status]').forEach((chip) => {
        chip.dataset.selected = String(chip.dataset.status === status);
    });
    row.querySelector('input[type=hidden]').value = status;
}

document.addEventListener('click', (e) => {
    const chip = e.target.closest('[data-status]');
    if (chip) return select(chip.closest('[data-status-row]'), chip.dataset.status);

    const bulk = e.target.closest('[data-mark-all]');
    if (bulk) {
        const status = bulk.dataset.markAll;
        rows().forEach((row) => select(row, status));
        showToast('Bulk Action', `All students marked as ${status}`, status === 'Present' ? 'success' : 'warning');
    }
});

// Restore selections after a validation error redirect.
rows().forEach((row) => {
    const current = row.querySelector('input[type=hidden]').value;
    if (current) select(row, current);
});
