<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Clearance extends Model
{
    protected $casts = [
        'doc_awb' => 'array', // otomatis decode JSON ke array
        'doc_invdoc' => 'array', // otomatis decode JSON ke array
    ];
}
