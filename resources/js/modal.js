/* Modals: any element with [data-modal]. Open with [data-open-modal="id"]. */
export function openModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

export function closeModal(modal) {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

const closeAll = () => document.querySelectorAll('[data-modal]').forEach(closeModal);

document.addEventListener('click', (e) => {
    const opener = e.target.closest('[data-open-modal]');
    if (opener) return openModal(opener.dataset.openModal);

    if (e.target.closest('[data-close-modal]') || e.target.matches('[data-modal]')) {
        closeModal(e.target.closest('[data-modal]'));
    }
});
document.addEventListener('keydown', (e) => e.key === 'Escape' && closeAll());
