import { Chart, registerables } from 'chart.js';

Chart.register(...registerables);

const dataEl = document.getElementById('dashboard-data');
if (dataEl) {
    const data = JSON.parse(dataEl.textContent);

    Chart.defaults.color = '#94A3B8';
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.10)';
    Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";

    const legend = (padding, extra = {}) => ({
        position: 'bottom',
        labels: {
            padding,
            usePointStyle: true,
            boxWidth: 8,
            color: '#94A3B8',
            ...extra,
        },
    });

    const trendCanvas = document.getElementById('trendChart');
    if (trendCanvas) {
        new Chart(trendCanvas, {
            type: 'line',
            data: {
                labels: data.trend.labels,
                datasets: [
                    {
                        label: 'Present',
                        data: data.trend.present,
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.15)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#10B981',
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#38BDF8',
                    },
                    {
                        label: 'Absent',
                        data: data.trend.absent,
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.10)',
                        tension: 0.4,
                        fill: true,
                        pointRadius: 4,
                        pointBackgroundColor: '#EF4444',
                        pointHoverRadius: 6,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: legend(16) },
                scales: {
                    x: {
                        grid: { color: 'rgba(255, 255, 255, 0.06)' },
                        ticks: { color: '#94A3B8' },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(255, 255, 255, 0.06)' },
                        ticks: { stepSize: 1, color: '#94A3B8' },
                    },
                },
            },
        });
    }

    const total = data.today.reduce((sum, s) => sum + s.count, 0);
    const pieCanvas = document.getElementById('pieChart');

    if (pieCanvas) {
        new Chart(pieCanvas, {
            type: 'doughnut',
            data: {
                labels: total ? data.today.map((s) => s.label) : ['No Data'],
                datasets: [
                    {
                        data: total ? data.today.map((s) => s.count) : [1],
                        backgroundColor: total
                            ? data.today.map((s) => s.color)
                            : ['rgba(255, 255, 255, 0.1)'],
                        borderWidth: 2,
                        borderColor: '#0B1020',
                        hoverOffset: 4,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: legend(14, { font: { size: 11 } }),
                },
            },
        });
    }
}
