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

    const liveAgeNode = document.querySelector('[data-live-age]');
    if (liveAgeNode) {
        const payload = JSON.parse(liveAgeNode.dataset.liveAge);
        const targets = {
            weeks: liveAgeNode.querySelector('[data-live-age-value="weeks"]'),
            days: liveAgeNode.querySelector('[data-live-age-value="days"]'),
            hours: liveAgeNode.querySelector('[data-live-age-value="hours"]'),
            minutes: liveAgeNode.querySelector('[data-live-age-value="minutes"]'),
            seconds: liveAgeNode.querySelector('[data-live-age-value="seconds"]'),
        };
        const birthTime = new Date(payload.birthIso).getTime();
        const formatter = new Intl.NumberFormat('ar-EG');

        const updateLiveAge = () => {
            const seconds = Math.max(0, Math.floor((Date.now() - birthTime) / 1000));
            const minutes = Math.floor(seconds / 60);
            const hours = Math.floor(seconds / 3600);
            const days = Math.floor(seconds / 86400);
            const weeks = Math.floor(days / 7);

            if (targets.weeks) {
                targets.weeks.textContent = `${formatter.format(weeks)} أسبوع`;
            }

            if (targets.days) {
                targets.days.textContent = `${formatter.format(days)} يوم`;
            }

            if (targets.hours) {
                targets.hours.textContent = `${formatter.format(hours)} ساعة`;
            }

            if (targets.minutes) {
                targets.minutes.textContent = `${formatter.format(minutes)} دقيقة`;
            }

            if (targets.seconds) {
                targets.seconds.textContent = `${formatter.format(seconds)} ثانية`;
            }
        };

        updateLiveAge();
        window.setInterval(updateLiveAge, 1000);
    }
});

Alpine.start();
