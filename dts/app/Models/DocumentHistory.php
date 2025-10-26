<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentHistory extends Model
{
    protected $primaryKey = 'historyID';
    protected $fillable = [
        'documentId', 'prevDepartmentID', 'currentDepartmentID', 'statusID', 'userID', 'action'
    ];
    public $timestamps = true;

    public function document()
    {
        return $this->belongsTo(Document::class, 'documentId', 'documentId');
    }
}