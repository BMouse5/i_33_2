<?php

namespace App\Http\Controllers;

use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ReportImport; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PDF;
use Mpdf\Mpdf;

class GenerationController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    // Загрузка и обработка отчетов
    public function upload(Request $request)
    {
        
        $request->validate([
            'report_file' => 'required|mimes:xls,xlsx,csv'
        ]);

        $filePath = $request->file('report_file')->store('reports');

        // Парсинг файла и анализ данных
        $analysisResults = $this->analyzeReport($filePath);
        
        return view('reports.analysis', compact('analysisResults'));
    }

    // Имитация анализа загруженного отчета
    private function analyzeReport($filePath)
    {
        // Загрузка данных из Excel
        $data = Excel::toArray(new ReportImport, storage_path('app/' . $filePath));
        
        $labels = [];
        $numericalValues = [];
        $categoricalValues = [];
        
        foreach ($data[0] as $row) {
            // Проверяем, если значения в нужных колонках не равны null или 0
            if (!is_null($row[0]) && $row[0] !== '' && !is_null($row[1]) && $row[1] !== 0) {
                $labels[] = $row[0]; // Предполагаем, что в первой колонке категории
                $numericalValues[] = (int) $row[1]; // Во второй колонке числовые значения
                $categoricalValues[] = (int) ($row[2] ?? 0); // В третьей колонке могут быть другие данные
            }
        }
    
        // Пример анализа данных
        return [
            'numerical_data' => [
                'type' => 'number',
                'values' => $numericalValues,
            ],
            'categorical_data' => [
                'type' => 'category',
                'labels' => $labels,
                'values' => $categoricalValues,
            ],
            'scatter_data' => [
                'type' => 'scatter',
                'points' => array_map(function($x, $y) {
                    return ['x' => $x, 'y' => $y];
                }, $numericalValues, $categoricalValues),
            ]
        ];
    }
    
    // Сохранение графиков на сервере
    public function saveChart(Request $request)
    {
        if ($request->hasFile('chart')) {
            $path = $request->file('chart')->store('charts', 'public');
            return response()->json(data: ['path' => $path]);
        }

        return response()->json(['error' => 'No chart uploaded'], 400);
    }

    // Генерация PDF с графиками
    public function generatePdf()
    {
        $chartPaths = Storage::disk('public')->files('charts');

        // Генерация PDF с использованием Blade-шаблона
        $pdf = PDF::loadView('reports.pdf', ['chartPaths' => $chartPaths]);

        return $pdf->download('report.pdf');

    }


}
