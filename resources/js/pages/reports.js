import { showToast } from '../toast.js';

/* Export the visible rows of the report table as CSV. */
document.getElementById('export-csv')?.addEventListener('click', (e) => {
    const rows = [['#', 'Student', 'Roll No', 'Course', 'Session', 'Date', 'Status', 'Faculty']];

    document.querySelectorAll('#report-table tbody tr[data-search]:not([hidden])').forEach((tr, i) => {
        const cells = [...tr.querySelectorAll('td')].slice(1).map((td) => td.innerText.trim());
        rows.push([i + 1, ...cells]);
    });

    const csv = rows.map((r) => r.map((c) => `"${String(c ?? '').replace(/"/g, '""')}"`).join(',')).join('\n');
    const a = Object.assign(document.createElement('a'), {
        href: 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv),
        download: `attendance_report_${e.currentTarget.dataset.date}.csv`,
    });
    a.click();
    showToast('Export Complete', 'CSV report downloaded successfully.', 'success');
});
