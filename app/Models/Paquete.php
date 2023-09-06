<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paquete extends Model
{
    use HasFactory;
    protected $fillable = [
        'descripcion',
        'peso_kg',
        'lote_id',
        'estanteria_id',
    ];

    protected $table = 'paquetes';
    protected $primaryKey = 'id';
    public $incrementing = true; 
}
