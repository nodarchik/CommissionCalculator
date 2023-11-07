<?php

// Enforces strict typing for the entire file.
declare(strict_types=1);

// Namespace declaration for utility classes.
namespace App\Utils;

/**
 * Utility class for writing data to CSV files.
 */
class CSVWriter
{
    /**
     * Writes an array of commissions to a CSV file.
     *
     * @param string $filePath The path to the CSV file where data will be written.
     * @param array $commissions An array of commission amounts to write to the file.
     */
    public function write(string $filePath, array $commissions): void
    {
        // Attempts to open the file for writing; binary-safe mode.
        $handle = fopen($filePath, 'wb');

        // Proceeds if the file handle is valid.
        if ($handle !== false) {
            // Iterates through each commission and writes it to the file.
            foreach ($commissions as $commission) {
                // Writes the commission as a string to the CSV file.
                fputcsv($handle, [(string)$commission]);
            }
            // Closes the file handle after writing is complete.
            fclose($handle);
        }
    }
}
