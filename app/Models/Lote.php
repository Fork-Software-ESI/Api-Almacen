<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    use HasFactory;

    protected $fillable = ['descripcion'];

    protected $table = 'lotes';

    protected $primaryKey = 'id';
}
