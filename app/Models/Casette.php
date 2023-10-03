<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Casette extends Model
{
    use HasFactory;

    protected $fillable = [
        'atm_id',
        'casette_1',
        'casette_2',
        'casette_3',
        'casette_4',
        'casette_5',
        'casette_6',
        'casette_7',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
