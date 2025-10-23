<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $casts = [
        'doc_permit' => 'array', // otomatis decode JSON ke array
    ];

    public function getRouteKeyName()
    {
        return 'id'; // atau kolom lain yang unik seperti 'kode_po'
    }
}
