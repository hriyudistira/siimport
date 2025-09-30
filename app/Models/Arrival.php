<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Arrival extends Model
{
    public function purchases()
    {
        return $this->belongsTo(Purchase::class);
    }
}
