<?php

namespace App\Services;

use App\Models\SigningDocument;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use setasign\Fpdi\Fpdi;

class SignedPdf
{
    public function create(SigningDocument $document, string $signaturePng): string
    {
        $source = tempnam(sys_get_temp_dir(), 'source_pdf_');
        $signature = tempnam(sys_get_temp_dir(), 'signature_png_');

        try {
            file_put_contents($source, Crypt::decryptString(Storage::disk('local')->get($document->file_path)));
            file_put_contents($signature, $signaturePng);

            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($source);
            for ($page = 1; $page <= $pageCount; $page++) {
                $template = $pdf->importPage($page);
                $size = $pdf->getTemplateSize($template);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($template);
                if ($page === $pageCount) {
                    $width = min(50, $size['width'] * .28);
                    $height = 20;
                    $x = min($size['width'] - $width - 5, $size['width'] * $document->signature_x / 100);
                    $y = min($size['height'] - $height - 5, $size['height'] * $document->signature_y / 100);
                    $pdf->Image($signature, $x, $y, $width, $height, 'PNG');
                }
            }

            $path = 'signing/' . $document->id . '/signed-' . Str::uuid() . '.pdf.enc';
            if (!Storage::disk('local')->put($path, Crypt::encryptString($pdf->Output('S')))) {
                throw new \RuntimeException('Could not save signed document.');
            }
            return $path;
        } finally {
            @unlink($source);
            @unlink($signature);
        }
    }
}
