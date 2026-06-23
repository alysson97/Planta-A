<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    /**
     * Linhas de produto da Planta A. Espelha o ENUM da coluna `line` e serve
     * como fonte unica de verdade para validacao de filtro e montagem do
     * seletor na view.
     */
    public const LINES = ['Geladeira', 'Máquina de Lavar', 'TV', 'Ar-Condicionado'];

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
