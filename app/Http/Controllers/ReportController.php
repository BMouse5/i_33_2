<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mpdf\Mpdf;
use Illuminate\Support\Facades\Log;
class ReportController extends Controller
{
    public function index()
    {
        return view('index');
    }

    public function saveReport(Request $request)
    {
       
        return response()->json(['message' => 'Отчет сохранен!', 'report' => $request->report]);
    }
    public function generatePDF(Request $request)
{
    // Логируем входные данные
    Log::info('Generating PDF with data: ', $request->input('pages'));

    try {
        $pages = $request->input('pages');
        
        // Инициализация mPDF
        $mpdf = new \Mpdf\Mpdf();
        
        foreach ($pages as $pageId => $blocks) {
            $html = "<h1>{$pageId}</h1><div style='page-break-after: always;'>";
            
            foreach ($blocks as $block) {
                $html .= "<div style='position:absolute; left:{$block['left']}; top:{$block['top']};'>";

                // Проверяем наличие изображения графика
                if (!empty($block['chartImage'])) {
                    $chartImage = $block['chartImage'];

                    // Удаляем префикс data:image/png;base64,
                    $chartImage = str_replace('data:image/png;base64,', '', $chartImage);
                    $chartImage = base64_decode($chartImage); // Декодируем изображение из Base64

                    // Создаем временный файл для изображения
                    $tempFile = tempnam(sys_get_temp_dir(), 'chart_') . '.png';
                    if (file_put_contents($tempFile, $chartImage) === false) {
                        Log::error('Failed to write temporary chart image to file: ' . $tempFile);
                        return response()->json(['error' => 'Failed to create chart image'], 500);
                    }

                    // Вставляем изображение в PDF
                    $html .= "<img src='{$tempFile}' style='max-width: 100%; max-height: 300px;'/>";
                } else {
                    $html .= $block['html']; // Добавляем HTML контент
                }

                $html .= "</div>";
            }
            $html .= "</div>"; // Закрытие div для страницы
            $mpdf->WriteHTML($html);
        }

        // Возвращаем PDF на клиент
        return $mpdf->Output('report.pdf', 'D'); // 'D' - скачать файл
    } catch (\Mpdf\MpdfException $e) {
        Log::error('mPDF Exception: ' . $e->getMessage());
        return response()->json(['error' => 'PDF generation failed'], 500);
    } catch (\Exception $e) {
        Log::error('General Exception: ' . $e->getMessage());
        return response()->json(['error' => 'An error occurred while generating PDF'], 500);
    }
}







    

}
