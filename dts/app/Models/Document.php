<?php

namespace App\Models;

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
        'documentId', 'documentNo', 'title', 'description', 'documentType', 'documentDate',
        'ownerID', 'currentStatus', 'currentDepartmentID', 'filePath'
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'ownerID', 'userID');
    }

    public function status()
    {
        return $this->belongsTo(DocumentStatus::class, 'currentStatus', 'statusID');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'currentDepartmentID', 'depID');
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
            'editUrl' => route('staff.document.edit', $this->documentId),
            'deleteUrl' => route('staff.document.delete', $this->documentId),
        ];
    }
}