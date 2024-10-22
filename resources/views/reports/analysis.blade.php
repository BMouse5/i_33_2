@include('layouts.header')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Анализ отчета</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/charts.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
<h1>Графики по анализу данных</h1>

<!-- Столбчатая диаграмма -->
<h2>Столбчатая диаграмма</h2>
<canvas id="barChart" width="400" height="200"></canvas>

<!-- Линейный график -->
<h2>Линейный график</h2>
<canvas id="lineChart" width="400" height="200"></canvas>

<!-- Круговая диаграмма -->
<h2>Круговая диаграмма</h2>
<canvas id="pieChart" width="400" height="200"></canvas>

<!-- Кольцевая диаграмма -->
<h2>Кольцевая диаграмма</h2>
<canvas id="doughnutChart" width="400" height="200"></canvas>

<!-- Радарная диаграмма -->
<h2>Радарная диаграмма</h2>
<canvas id="radarChart" width="400" height="200"></canvas>

<!-- Полярная диаграмма -->
<h2>Полярная диаграмма</h2>
<canvas id="polarAreaChart" width="400" height="200"></canvas>

<!-- График рассеяния -->
<h2>График рассеяния</h2>
<canvas id="scatterChart" width="400" height="200"></canvas>

<!-- Bubble Chart -->
<h2>Bubble Chart</h2>
<canvas id="bubbleChart" width="400" height="200"></canvas>

<form action="{{ route('reports.generatePdf') }}" method="POST" id="pdfForm">
    @csrf
    <button type="submit" id="downloadPdf">Download PDF</button>
</form>



<script>
    const analysis = @json($analysisResults);

// Столбчатая диаграмма
// Функция для создания графиков
function createChart(ctx, chartType, data, options = {}) {
    new Chart(ctx, {
        type: chartType,
        data: data,
        options: options
    });
}

// Проверка наличия данных и создание графиков
function initializeCharts(analysis) {
    // Проверка наличия данных для столбчатой диаграммы
    if (analysis.numerical_data.values.length > 0 && analysis.categorical_data.labels.length > 0) {
        const barChartCtx = document.getElementById('barChart').getContext('2d');
        createChart(barChartCtx, 'bar', {
            labels: analysis.categorical_data.labels,
            datasets: [{
                label: 'Числовые данные',
                data: analysis.numerical_data.values,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        }, {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        });
    }

    // Проверка наличия данных для линейного графика
    if (analysis.numerical_data.values.length > 0) {
        const lineChartCtx = document.getElementById('lineChart').getContext('2d');
        createChart(lineChartCtx, 'line', {
            labels: analysis.categorical_data.labels,
            datasets: [{
                label: 'Числовые данные',
                data: analysis.numerical_data.values,
                fill: false,
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.1
            }]
        });
    }

    // Проверка наличия данных для круговой диаграммы
    if (analysis.categorical_data.values.length > 0) {
        const pieChartCtx = document.getElementById('pieChart').getContext('2d');
        createChart(pieChartCtx, 'pie', {
            labels: analysis.categorical_data.labels,
            datasets: [{
                label: 'Категории',
                data: analysis.categorical_data.values,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
            }]
        });
        
        const doughnutChartCtx = document.getElementById('doughnutChart').getContext('2d');
        createChart(doughnutChartCtx, 'doughnut', {
            labels: analysis.categorical_data.labels,
            datasets: [{
                label: 'Категории',
                data: analysis.categorical_data.values,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
            }]
        });

        const radarChartCtx = document.getElementById('radarChart').getContext('2d');
        createChart(radarChartCtx, 'radar', {
            labels: analysis.categorical_data.labels,
            datasets: [{
                label: 'Категории',
                data: analysis.categorical_data.values,
                backgroundColor: 'rgba(179,181,198,0.2)',
                borderColor: 'rgba(179,181,198,1)',
                pointBackgroundColor: 'rgba(179,181,198,1)',
                pointBorderColor: '#fff',
                pointHoverBackgroundColor: '#fff',
                pointHoverBorderColor: 'rgba(179,181,198,1)'
            }]
        });

        const polarAreaChartCtx = document.getElementById('polarAreaChart').getContext('2d');
        createChart(polarAreaChartCtx, 'polarArea', {
            labels: analysis.categorical_data.labels,
            datasets: [{
                label: 'Категории',
                data: analysis.categorical_data.values,
                backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56'],
            }]
        });
    }

    // Проверка наличия данных для графика рассеяния
    if (analysis.scatter_data.points.length > 0) {
        const scatterChartCtx = document.getElementById('scatterChart').getContext('2d');
        createChart(scatterChartCtx, 'scatter', {
            datasets: [{
                label: 'Точки рассеяния',
                data: analysis.scatter_data.points,
                backgroundColor: 'rgba(75, 192, 192, 1)',
            }]
        }, {
            scales: {
                x: {
                    type: 'linear',
                    position: 'bottom'
                }
            }
        });
    }

    // Проверка наличия данных для bubble chart
    if (analysis.scatter_data.points.length > 0) {
        const bubbleChartCtx = document.getElementById('bubbleChart').getContext('2d');
        createChart(bubbleChartCtx, 'bubble', {
            datasets: [{
                label: 'Bubble Chart',
                data: analysis.scatter_data.points,
                backgroundColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        });
    }
}

// Вызов функции инициализации графиков с вашими данными
initializeCharts(analysis);


    // Скачивание PDF
    document.getElementById('downloadPdf').addEventListener('submit', function (e) {
    e.preventDefault(); // Остановить стандартное поведение формы

    // Сохраняем графики перед генерацией PDF
    saveCharts().then(() => {
        // Делает POST-запрос для генерации PDF
        fetch('{{ route('reports.generatePdf') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Убедитесь, что включен CSRF токен
            },
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Ошибка сети: ' + response.statusText);
            }
            return response.blob(); // Получаем ответ как blob
        })
        .then(blob => {
            // Создание ссылки для скачивания PDF
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'report.pdf'; // Указываем имя файла
            document.body.appendChild(a);
            a.click(); // Программное нажатие на ссылку для скачивания
            a.remove(); // Удаление ссылки после скачивания
        })
        .catch((error) => {
            console.error("Ошибка при генерации PDF:", error);
        });
    }).catch((error) => {
        console.error("Ошибка при сохранении графиков:", error);
    });
});




function saveCharts() {
    const chartElements = [
        'barChart',
        'lineChart',
        'pieChart',
        'doughnutChart',
        'radarChart',
        'polarAreaChart',
        'scatterChart',
        'bubbleChart'
    ];

    const promises = chartElements.map(chartId => {
        const canvas = document.getElementById(chartId);
        if (canvas) {
            const chartDataUrl = canvas.toDataURL('image/png');
            return fetch('{{ route('reports.saveChart') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ chart: chartDataUrl })
            }).then(response => {
                if (!response.ok) {
                    throw new Error('Ошибка при сохранении графика: ' + response.statusText);
                }
                return response.json();
            }).catch((error) => {
                console.error("Ошибка при отправке графика:", error);
                return Promise.reject(error);
            });
        }
        return Promise.resolve(); 
    });

    return Promise.all(promises); 
}





</script>
</body>
</html>
