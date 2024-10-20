@include('layouts.header')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Загрузка отчетов</title>
</head>
<body>
    <h1>Загрузка отчетов для сравнения</h1>

    @if(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @endif

    <form action="{{ route('report.upload') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="report_file_1">Отчет 1:</label>
        <input type="file" name="report_file_1" id="report_file_1" accept=".xls,.xlsx,.csv" required><br><br>
        <label for="report_file_2">Отчет 2:</label>
        <input type="file" name="report_file_2" id="report_file_2" accept=".xls,.xlsx,.csv" required><br><br>

        <button type="submit">Загрузить и сравнить</button>
    </form>
</body>
</html>
