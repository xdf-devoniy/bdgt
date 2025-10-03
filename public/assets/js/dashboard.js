const trendCanvas = document.getElementById('trendChart');
if (trendCanvas) {
    const trendData = JSON.parse(trendCanvas.dataset.chart ?? '[]');
    const labels = trendData.map(item => item.date);
    const income = trendData.map(item => item.income);
    const expense = trendData.map(item => item.expense);

    new Chart(trendCanvas.getContext('2d'), {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Income',
                    data: income,
                    tension: 0.4,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    fill: true,
                },
                {
                    label: 'Expense',
                    data: expense,
                    tension: 0.4,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    ticks: {
                        callback(value) {
                            return new Intl.NumberFormat('uz-UZ').format(value);
                        }
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                }
            }
        }
    });
}

const categoryCanvas = document.getElementById('categoryChart');
if (categoryCanvas) {
    const categoryData = JSON.parse(categoryCanvas.dataset.chart ?? '[]');
    new Chart(categoryCanvas.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: categoryData.map(item => item.label),
            datasets: [
                {
                    data: categoryData.map(item => item.value),
                    backgroundColor: ['#0d6efd', '#6610f2', '#198754', '#fd7e14', '#6c757d'],
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%'
        }
    });
}
