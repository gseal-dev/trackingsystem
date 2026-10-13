<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'documentId';
    public $incrementing = false;
    protected $keyType = 'string';

    public function getRouteKeyName()
    {
        return 'documentId';
    }

    protected $fillable = [
        'documentId', 'documentNo', 'documentTitle', 'title', 'description', 'documentType', 'documentDate',
        'ownerID', 'currentDepartmentID', 'filePath'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'ownerID', 'userID');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'currentDepartmentID', 'depID');
    }

    /**
     * Filter by year / month / day of the document date
     * (falls back to the upload date for documents that have no document date).
     */
    public function scopeDateFilter(Builder $query, $year = null, $month = null, $day = null): Builder
    {
        foreach (['whereYear' => $year, 'whereMonth' => $month, 'whereDay' => $day] as $method => $value) {
            if ($value === null || $value === '' || is_array($value)) {
                continue;
            }
            $value = (int) $value;
            $query->where(function ($q) use ($method, $value) {
                $q->$method('documentDate', $value)
                  ->orWhere(function ($sub) use ($method, $value) {
                      $sub->whereNull('documentDate')->$method('created_at', $value);
                  });
            });
        }

        return $query;
    }

    /**
     * Search reference number, title, subject, type and office (and the uploader when asked).
     * Every word typed has to match somewhere, in any order.
     */
    public function scopeSearch(Builder $query, ?string $term, bool $withUploader = false): Builder
    {
        $words = preg_split('/\s+/', trim((string) $term), -1, PREG_SPLIT_NO_EMPTY);

        foreach ($words as $word) {
            $like = '%' . $word . '%';
            $query->where(function ($q) use ($like, $withUploader) {
                $q->where('documentNo', 'like', $like)
                  ->orWhere('documentTitle', 'like', $like)
                  ->orWhere('title', 'like', $like)
                  ->orWhere('documentType', 'like', $like)
                  ->orWhereHas('department', fn ($d) => $d->where('depName', 'like', $like));

                if ($withUploader) {
                    $q->orWhereHas('owner', fn ($o) => $o->where('username', 'like', $like)
                        ->orWhere('firstName', 'like', $like)
                        ->orWhere('lastName', 'like', $like));
                }
            });
        }

        return $query;
    }

    public function histories()
    {
        return $this->hasMany(\App\Models\DocumentHistory::class, 'documentId', 'documentId');
    }

    /** Timezone used when showing upload times to users (the database stores UTC). */
    public const DISPLAY_TIMEZONE = 'Asia/Manila';

    /** The original file name (uploads are stored as "<uuid>_<original name>"). */
    public function displayFileName(): ?string
    {
        if (! $this->filePath) {
            return null;
        }

        return preg_replace('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}_/i', '', basename($this->filePath));
    }

    /**
     * Everything the "View" popup shows. Needs the owner and department relations loaded.
     */
    public function viewData(): array
    {
        $owner = $this->owner;
        $fullName = $owner ? trim(($owner->firstName ?? '') . ' ' . ($owner->lastName ?? '')) : '';
        $uploadedBy = $owner
            ? ($fullName !== '' ? $fullName . ' (' . $owner->username . ')' : $owner->username)
            : 'Unknown';

        $date = $this->documentDate ?? $this->created_at;

        return [
            'id' => $this->documentId,
            'referenceNo' => $this->documentNo,
            'heading' => $this->documentTitle ?: $this->title,
            'documentTitle' => $this->documentTitle,
            'subject' => $this->title,
            'description' => $this->description ?: 'No description',
            'type' => $this->documentType,
            'office' => $this->department->depName ?? '-',
            'date' => $date ? \Carbon\Carbon::parse($date)->format('F d, Y') : '-',
            'fileName' => $this->displayFileName(),
            'uploadedBy' => $uploadedBy,
            'uploadedAt' => $this->created_at
                ? $this->created_at->copy()->timezone(self::DISPLAY_TIMEZONE)->format('F d, Y \\a\\t h:i A')
                : '-',
            'viewUrl' => $this->filePath ? asset('storage/' . $this->filePath) : null,
            'downloadUrl' => $this->filePath ? route('document.download', $this->documentId) : null,
            'updateUrl' => route('staff.document.update', $this->documentId),
            'edit' => [
                'documentNo' => $this->documentNo,
                'documentTitle' => $this->documentTitle,
                'fromOffice' => $this->department->depName ?? '',
                'documentType' => $this->documentType,
                'title' => $this->title,
                'documentDate' => $date ? \Carbon\Carbon::parse($date)->format('Y-m-d') : '',
            ],
            'deleteUrl' => route('staff.document.delete', $this->documentId),
        ];
    }
}