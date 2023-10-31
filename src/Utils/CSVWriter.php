<?php

declare(strict_types=1);

namespace App\Utils;

class CSVWriter
{
    public function write(string $filePath, array $commissions): void
    {
        $handle = fopen($filePath, 'wb');

        if ($handle !== false) {
            foreach ($commissions as $commission) {
                fputcsv($handle, [(string)$commission]);
            }
            fclose($handle);
        }
    }
}