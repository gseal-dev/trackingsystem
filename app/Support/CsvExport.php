<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

/** Streams a CSV with ALL rows of a query (not just the page on screen). */
class CsvExport
{
    /**
     * @param  callable(\Illuminate\Database\Eloquent\Model): array  $row  maps a record to one CSV row
     */
    public static function stream(Builder $query, array $headers, callable $row, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($query, $headers, $row) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 byte order mark so Excel reads accents correctly

            fputcsv($out, $headers, ',', '"', '');

            $query->chunk(500, function ($records) use ($out, $row) {
                foreach ($records as $record) {
                    fputcsv($out, array_map([self::class, 'safeCell'], $row($record)), ',', '"', '');
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** Stop spreadsheet apps from running user-typed text such as "=SUM(...)" as a formula. */
    public static function safeCell($value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
    }
}
