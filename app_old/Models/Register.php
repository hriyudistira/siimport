<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Register extends Model
{
    // IZINKAN FIELD DISIMPAN SECARA MASSAL
    protected $fillable = [
        'country_id',
        'country',
        // tambahkan field lain jika diperlukan
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }
}
