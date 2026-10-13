<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Small helpers shared by the staff and admin document lists:
 * how many rows per page, and the CSV export of everything that matches the filters.
 */
class DocumentList
{
    public const DEFAULT_PER_PAGE = 25;
    public const PER_PAGE_OPTIONS = [10, 25, 50, 100];

    /** Rows per page from ?per_page=, limited to the allowed choices (default 25). */
    public static function perPage(Request $request): int
    {
        $value = (int) $request->input('per_page');

        return in_array($value, self::PER_PAGE_OPTIONS, true) ? $value : self::DEFAULT_PER_PAGE;
    }

    /**
     * Stream a CSV with ALL rows of the query (not just the current page).
     *
     * @param  callable(\App\Models\Document): array  $row  maps a document to one CSV row
     */
    public static function csv(Builder $query, array $headers, callable $row, string $filename = 'documents_export.csv'): StreamedResponse
    {
        return response()->streamDownload(function () use ($query, $headers, $row) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 byte order mark so Excel reads accents correctly

            fputcsv($out, $headers, ',', '"', '');

            $query->chunk(500, function ($documents) use ($out, $row) {
                foreach ($documents as $document) {
                    fputcsv($out, array_map([self::class, 'safeCell'], $row($document)), ',', '"', '');
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
