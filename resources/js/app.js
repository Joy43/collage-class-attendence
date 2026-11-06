import './theme.js';
import './sidebar.js';
import './modal.js';
import './crud-form.js';
import './table-filter.js';
import './notifications.js';

/* Page-specific modules, loaded only where needed (keyed by route name on <body data-page>). */
const pages = {
    'dashboard': () => import('./pages/dashboard.js'),
    'attendance.create': () => import('./pages/attendance.js'),
    'students.index': () => import('./pages/students.js'),
    'reports.index': () => import('./pages/reports.js'),
    'notifications.index': () => import('./pages/notifications.js'),
};

pages[document.body.dataset.page]?.();
