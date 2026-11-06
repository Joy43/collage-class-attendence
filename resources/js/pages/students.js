/* Student directory: profile modal. */
import { openModal, closeModal } from '../modal.js';

const el = (html) => Object.assign(document.createElement('div'), { innerHTML: html });
const escapeHtml = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

function infoRow(label, value, extra = '') {
    return `<div class="flex justify-between border-b border-line py-2.5 text-[.85rem] last:border-b-0"><span class="font-semibold text-muted">${label}</span><span class="${extra}">${escapeHtml(value)}</span></div>`;
}

function showProfile(button) {
    const s = JSON.parse(button.dataset.profile);
    const tone = s.percent >= 75 ? ['text-present', 'bg-present'] : s.percent >= 50 ? ['text-late', 'bg-late'] : ['text-absent', 'bg-absent'];

    document.getElementById('profile-summary').innerHTML = `
        <div class="mb-1.5 flex size-[72px] items-center justify-center rounded-full bg-linear-to-br ${s.gradient} text-[1.6rem] font-extrabold text-white shadow-[0_4px_12px_rgba(37,99,235,.3)]">${escapeHtml(s.initial)}</div>
        <div class="text-[1.1rem] font-bold">${escapeHtml(s.name)}</div>
        <div class="text-[.8rem] text-muted">Roll No: ${escapeHtml(s.roll_no)}</div>
        <div class="mt-3 w-full text-center">
            <div class="mb-1.5 text-xs font-bold ${tone[0]}">Attendance: ${s.percent}%</div>
            <div class="h-2 overflow-hidden rounded-full bg-line"><div class="h-full rounded-full ${tone[1]}" style="width:${s.percent}%"></div></div>
            <div class="mt-1.5 text-xs text-muted">${s.present} present of ${s.total} sessions</div>
        </div>`;
    document.getElementById('profile-rows').innerHTML =
        infoRow('Student ID', `#${s.id}`) + infoRow('Roll Number', s.roll_no) + infoRow('Full Name', s.name)
        + infoRow('Total Sessions', s.total) + infoRow('Present', s.present, 'font-bold text-present')
        + infoRow('Absent/Late/Leave', s.total - s.present, 'font-bold text-absent');

    // "Edit" in the modal re-uses the row's edit button.
    document.getElementById('profile-edit-btn').onclick = () => {
        closeModal(document.getElementById('profile-modal'));
        document.querySelector(`[data-crud-edit][data-student="${s.id}"]`).click();
    };
    openModal('profile-modal');
}

document.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-profile]');
    if (trigger) showProfile(trigger);
});
