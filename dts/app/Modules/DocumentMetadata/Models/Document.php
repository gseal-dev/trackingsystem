<?php

namespace App\Modules\DocumentMetadata\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Document extends Model
{
    use HasFactory;

    protected $primaryKey = 'documentId';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'documentId',
        'documentNo',
        'title',
        'description',
        'documentType',
        'ownerID',
        'currentStatus',
        'filePath',
        'googleDriveId'
    ];

    // const CREATED_AT = 'createdAt';
    // const UPDATED_AT = 'updatedAt';

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($document) {
            if (empty($document->documentId)) {
                $document->documentId = self::generateDocumentId();
            }
        });
    }

    public static function generateDocumentId()
    {
        do {
            $id = 'DOC' . strtoupper(Str::random(6)) . rand(100, 999);
        } while (self::where('documentId', $id)->exists());
        
        return $id;
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'ownerID', 'userID');
    }
}