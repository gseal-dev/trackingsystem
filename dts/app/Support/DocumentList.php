<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Helpers for the staff and admin document lists. The generic parts live in
 * Paging (rows per page) and CsvExport (download of everything that matches).
 */
class DocumentList
{
    public const DEFAULT_PER_PAGE = Paging::DEFAULT_PER_PAGE;
    public const PER_PAGE_OPTIONS = Paging::PER_PAGE_OPTIONS;

    public static function perPage(Request $request): int
    {
        return Paging::perPage($request);
    }

    public static function csv(Builder $query, array $headers, callable $row, string $filename = 'documents_export.csv'): StreamedResponse
    {
        return CsvExport::stream($query, $headers, $row, $filename);
    }

    public static function safeCell($value): string
    {
        return CsvExport::safeCell($value);
    }
}
