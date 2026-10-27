<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentHistory extends Model
{
    protected $primaryKey = 'historyID';
    protected $fillable = [
        'documentId', 'prevDepartmentID', 'currentDepartmentID', 'userID', 'action'
    ];
    public $timestamps = true;

    public function document()
    {
        return $this->belongsTo(Document::class, 'documentId', 'documentId')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'userID');
    }
}