<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentStatus extends Model
{
    protected $primaryKey = 'statusID';
    protected $fillable = ['statusName', 'description'];
    public $timestamps = true;
}