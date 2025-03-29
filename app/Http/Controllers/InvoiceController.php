<?php

namespace App\Http\Controllers;

use Mpdf\Mpdf;

class InvoiceController extends Controller
{
    public function generateInvoice()
    {
        // بيانات الفاتورة
        $data = [
            'invoice_number' => 'INV-2024-001',
            'date' => now()->format('Y-m-d'),
            'customer_name' => 'محمد أحمد',
            'items' => [
                ['name' => 'منتج 1', 'quantity' => 2, 'price' => 50],
                ['name' => 'منتج 2', 'quantity' => 1, 'price' => 100],
            ],
            'total' => 200
        ];

        // إنشاء HTML لمحتوى الفاتورة
        $html = view('invoice', compact('data'))->render();

        // إعداد mPDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8', // لدعم اللغة العربية
            'format' => 'A4',
            'default_font' => 'dejavusans', // خط يدعم العربية
        ]);

        // إضافة المحتوى إلى ملف PDF
        $mpdf->WriteHTML($html);

        // تحميل الملف كفاتورة
        return $mpdf->Output('invoice.pdf', 'I'); // 'I' لعرض الملف مباشرة، 'D' لتحميله
    }
}
