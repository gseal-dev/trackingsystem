<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $primaryKey = 'depID';
    protected $fillable = ['depName', 'description'];
    public $timestamps = true;
}