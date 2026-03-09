import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;

Alpine.data('themeToggle', () => ({
    dark: localStorage.getItem('theme') === 'dark',
    init() {
        document.documentElement.classList.toggle('dark', this.dark);
    },
    toggle() {
        this.dark = !this.dark;
        document.documentElement.classList.toggle('dark', this.dark);
        localStorage.setItem('theme', this.dark ? 'dark' : 'light');
    },
}));

document.addEventListener('DOMContentLoaded', () => {
    const chartNode = document.getElementById('result-chart');
    if (chartNode && chartNode.dataset.points) {
        const points = JSON.parse(chartNode.dataset.points);
        new Chart(chartNode, {
            type: 'line',
            data: {
                labels: points.map((point) => point.label),
                datasets: [{
                    label: 'النمو المتوقع',
                    data: points.map((point) => point.value),
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.15)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.28,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false,
                    },
                },
            },
        });
    }
});

Alpine.start();
