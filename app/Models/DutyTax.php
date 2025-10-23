<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DutyTax extends Model
{
    //
	public function clearance()
    {
        return $this->belongsTo(\App\Models\Clearance::class, 'kode_po', 'kode_po');
    }
}
