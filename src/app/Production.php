<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'line', 'production_date', 'produced', 'defects',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'production_date' => 'date',
        'produced' => 'integer',
        'defects' => 'integer',
    ];
}
