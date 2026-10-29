<?php

namespace App\Support;

use Illuminate\Http\Request;

/** Rows-per-page rules shared by every paginated list (default 25). */
class Paging
{
    public const DEFAULT_PER_PAGE = 25;
    public const PER_PAGE_OPTIONS = [10, 25, 50, 100];

    /** Rows per page from ?per_page=, limited to the allowed choices (default 25). */
    public static function perPage(Request $request): int
    {
        $value = (int) $request->input('per_page');

        return in_array($value, self::PER_PAGE_OPTIONS, true) ? $value : self::DEFAULT_PER_PAGE;
    }
}
