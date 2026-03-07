<script setup>
import { Bar } from 'vue-chartjs';
import { Chart as ChartJS, CategoryScale, LinearScale, BarElement, Tooltip } from 'chart.js';

ChartJS.register(CategoryScale, LinearScale, BarElement, Tooltip);

const props = defineProps({
    data: Array, // [{ date, total }]
});

const chartData = {
    labels: props.data.map(d => {
        const date = new Date(d.date);
        return date.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
    }),
    datasets: [{
        data: props.data.map(d => d.total),
        backgroundColor: '#6366f1',
        borderRadius: 4,
        barPercentage: 0.7,
    }],
};

const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: { legend: { display: false } },
    scales: {
        x: { grid: { display: false } },
        y: {
            beginAtZero: true,
            ticks: {
                callback: (v) => '₹' + v.toLocaleString('en-IN'),
            },
        },
    },
};
</script>

<template>
    <div class="h-48">
        <Bar :data="chartData" :options="options" />
    </div>
</template>
