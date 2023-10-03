<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Atm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'information',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function casette(): HasOne
    {
        return $this->hasOne(Casette::class);
    }
}
