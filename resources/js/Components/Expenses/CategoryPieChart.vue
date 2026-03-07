<script setup>
import { Pie } from 'vue-chartjs';
import { Chart as ChartJS, ArcElement, Tooltip, Legend } from 'chart.js';

ChartJS.register(ArcElement, Tooltip, Legend);

const props = defineProps({
    data: Array, // [{ category, total }]
});

const colors = [
    '#6366f1', '#f43f5e', '#10b981', '#f59e0b',
    '#3b82f6', '#8b5cf6', '#ec4899', '#14b8a6', '#64748b',
];

const chartData = {
    labels: props.data.map(d => d.category),
    datasets: [{
        data: props.data.map(d => d.total),
        backgroundColor: props.data.map((_, i) => colors[i % colors.length]),
        borderWidth: 0,
    }],
};

const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10 },
        },
    },
};
</script>

<template>
    <div class="h-64">
        <Pie :data="chartData" :options="options" />
    </div>
</template>
