import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const data = JSON.parse(document.getElementById('dashboard-data').textContent);
const dark = document.documentElement.classList.contains('dark');

Chart.defaults.color = dark ? '#94A3B8' : '#64748B';
Chart.defaults.borderColor = dark ? '#334155' : '#E2E8F0';
Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";

const legend = (padding, extra = {}) => ({ position: 'bottom', labels: { padding, usePointStyle: true, ...extra } });

new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: data.trend.labels,
        datasets: [
            { label: 'Present', data: data.trend.present, borderColor: '#16A34A', backgroundColor: 'rgba(22,163,74,.08)', tension: 0.4, fill: true, pointRadius: 4, pointHoverRadius: 6 },
            { label: 'Absent', data: data.trend.absent, borderColor: '#DC2626', backgroundColor: 'rgba(220,38,38,.06)', tension: 0.4, fill: true, pointRadius: 4, pointHoverRadius: 6 },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: legend(16) },
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
    },
});

const total = data.today.reduce((sum, s) => sum + s.count, 0);

new Chart(document.getElementById('pieChart'), {
    type: 'doughnut',
    data: {
        labels: total ? data.today.map((s) => s.label) : ['No Data'],
        datasets: [{
            data: total ? data.today.map((s) => s.count) : [1],
            backgroundColor: total ? data.today.map((s) => s.color) : ['#E2E8F0'],
            borderWidth: 2,
            borderColor: dark ? '#1E293B' : '#FFFFFF',
        }],
    },
    options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: legend(14, { font: { size: 11 } }) } },
});
