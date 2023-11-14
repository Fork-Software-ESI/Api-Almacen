<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class GerentePaquete
 * 
 * @property int $ID_Gerente
 * @property int $ID_Paquete
 * 
 * @property GerenteAlmacen $gerente_almacen
 * @property Paquete $paquete
 *
 * @package App\Models
 */
class GerentePaquete extends Model
{
    protected $table = 'gerente_paquete';
    protected $primaryKey = 'ID_Paquete';
    public $incrementing = false;
    public $timestamps = true;

    protected $casts = [
        'ID_Gerente' => 'int',
        'ID_Paquete' => 'int'
    ];

    protected $fillable = [
        'ID_Gerente',
        'ID_Paquete'
    ];

    public function gerente_almacen()
    {
        return $this->belongsTo(GerenteAlmacen::class, 'ID_Gerente');
    }

    public function paquete()
    {
        return $this->belongsTo(Paquete::class, 'ID_Paquete');
    }
}
