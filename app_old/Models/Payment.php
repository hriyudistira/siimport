<?php

namespace App\Models;

use App\Models\Purchase;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
// use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    // public function purchase(): BelongsTo
    // {
    //     return $this->BelongsTo(Purchase::class, 'purchase_id');
    // }

    public function purchase(): HasOne
    {
        return $this->hasOne(Purchase::class, 'kode_po', 'kode_po');
    }
}
