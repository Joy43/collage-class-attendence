/*
 * Reusable create/edit switch for forms (modal or inline).
 *
 *   <form data-crud data-store-action="/things">   … must contain <input type="hidden" name="_method" value="PUT" disabled>
 *   <button data-crud-create="formId">             → reset the form to "create" mode
 *   <button data-crud-edit="formId"
 *           data-action="/things/5"
 *           data-values='{"name":"Foo"}'>          → fill the form and switch to "edit" (PUT) mode
 *
 * Elements inside the form (or its [data-crud-scope] ancestor) with data-create-text / data-edit-text swap their label,
 * and the form gets data-mode="create|edit" (usable in Tailwind: data-[mode=edit]:…).
 */
import { openModal } from './modal.js';

function setMode(form, mode, action, values = {}) {
    form.reset();
    form.action = action;
    form.dataset.mode = mode;

    const method = form.querySelector('input[name="_method"]');
    if (method) method.disabled = mode !== 'edit';

    for (const [name, value] of Object.entries(values)) {
        if (form.elements[name]) form.elements[name].value = value;
    }

    const scope = form.closest('[data-crud-scope]') ?? form; // lets titles live outside the <form>
    scope.querySelectorAll('[data-create-text]').forEach((el) => {
        el.textContent = mode === 'edit' ? el.dataset.editText : el.dataset.createText;
    });

    const modal = form.closest('[data-modal]');
    if (modal) openModal(modal.id);
    form.querySelector('input:not([type=hidden])')?.focus();
}

document.addEventListener('click', (e) => {
    const edit = e.target.closest('[data-crud-edit]');
    if (edit) {
        const form = document.getElementById(edit.dataset.crudEdit);
        setMode(form, 'edit', edit.dataset.action, JSON.parse(edit.dataset.values));
        form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        return;
    }

    const create = e.target.closest('[data-crud-create]');
    if (create) {
        const form = document.getElementById(create.dataset.crudCreate);
        setMode(form, 'create', form.dataset.storeAction);
    }
});
