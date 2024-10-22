@php
    use Illuminate\Support\Facades\Storage;
    use Illuminate\Support\Str;
@endphp
@include('layouts.header')
<div class="container">

<h1>Все публичные отчеты</h1>

<div class="row">

    @foreach($reports as $report)

        <div class="col-md-4">

            <div class="card">

            <img src="{{ Storage::url($report->first_page_image) }}" alt="Первая страница отчета" class="img-fluid">



                <div class="card-body">

                    <h5 class="card-title">{{ $report->project_name }}</h5>

                    <p class="card-text">{{ Str::limit($report->description, 100) }}</p>

                    <p class="card-text">Автор: {{ $report->user->name }}</p>

                    <a href="{{ route('report.show', $report->id) }}" class="btn btn-primary">Подробнее</a>


                </div>

            </div>

        </div>

    @endforeach

</div>

</div>
