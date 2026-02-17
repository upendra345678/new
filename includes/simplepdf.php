<?php

namespace DoctorlyDashboard\Includes;

if (! defined('ABSPATH')) {
    exit;
}

class SimplePdf
{
    public function generate(string $title, string $text): string
    {
        $safe = substr(preg_replace('/[^\x20-\x7E]/', '', $title . "\n\n" . $text), 0, 3500);
        $stream = "BT /F1 10 Tf 40 800 Td (" . $this->escape($safe) . ") Tj ET";

        $objects = [];
        $objects[] = '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj';
        $objects[] = '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj';
        $objects[] = '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 5 0 R /Resources << /Font << /F1 4 0 R >> >> >> endobj';
        $objects[] = '4 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj';
        $objects[] = '5 0 obj << /Length ' . strlen($stream) . ' >> stream ' . $stream . ' endstream endobj';

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $obj) {
            $offsets[] = strlen($pdf);
            $pdf .= $obj . "\n";
        }

        $xref = strlen($pdf);
        $pdf .= 'xref 0 ' . (count($objects) + 1) . "\n0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$i]) . "\n";
        }

        $pdf .= 'trailer << /Size ' . (count($objects) + 1) . ' /Root 1 0 R >>' . "\n";
        $pdf .= 'startxref ' . $xref . "\n%%EOF";

        return $pdf;
    }

    private function escape(string $text): string
    {
        $text = str_replace(["\\", "(", ")", "\r", "\n"], ["\\\\", "\\(", "\\)", '', ' '], $text);

        return $text;
    }
}
