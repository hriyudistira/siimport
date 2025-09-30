<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

use Illuminate\Database\Eloquent\Relations\HasOne;


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
	protected $casts = [
        'doc_pr' => 'array', // otomatis decode JSON ke array
    ];

    public function getRouteKeyName()
    {
        return 'id'; // atau kolom lain yang unik seperti 'kode_po'
    }
	
	//Accessor untuk Document PR (doc_pr)
    protected function docPrText(): Attribute
    {
        return Attribute::get(
            fn() => is_array($this->doc_pr)
                ? implode(', ', $this->doc_pr)
                : ($this->doc_pr ?: '-')
        );
    }

    protected $table = 'registers';

    public function clearance(): HasOne
    {
        return $this->hasOne(Clearance::class, 'kode_po', 'kode_po');
    }

    public function dutyTax(): HasOne
    {
        return $this->hasOne(DutyTax::class, 'kode_po', 'kode_po');
    }

    public function document(): HasOne
    {
        return $this->hasOne(Document::class, 'kode_po', 'kode_po');
    }
    public function schedule(): HasOne
    {
        return $this->hasOne(Schedule::class, 'kode_po', 'kode_po');
    }
	
}
