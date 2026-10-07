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
}