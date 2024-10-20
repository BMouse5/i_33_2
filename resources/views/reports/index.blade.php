@include('layouts.header')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Загрузка отчета</title>
</head>
<body>
<h1>Загрузить отчет</h1>

<form action="{{ route('reports.upload') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="report_file" accept=".xls,.xlsx,.csv" required>
    <button type="submit">Загрузить</button>
</form>



@if($errors->any())
    <div>
        <strong>Ошибка!</strong> {{ $errors->first() }}
    </div>
@endif
</body>
</html>