<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $primaryKey = 'documentId';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'documentId', 'documentNo', 'title', 'description', 'documentType',
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