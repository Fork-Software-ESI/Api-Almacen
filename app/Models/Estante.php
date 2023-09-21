<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Estante
 * 
 * @property int $ID
 * @property int $ID_Almacen
 * 
 * @property Almacen $almacen
 * @property Collection|Paquete[] $paquetes
 *
 * @package App\Models
 */
class Estante extends Model
{
	protected $table = 'estante';
	public $timestamps = false;

	protected $casts = [
		'ID_Almacen' => 'int'
	];

	public function almacen()
	{
		return $this->belongsTo(Almacen::class, 'ID_Almacen');
	}

	public function paquetes()
	{
		return $this->belongsToMany(Paquete::class, 'paquete_estante', 'ID_Estante', 'ID_Paquete')
					->withPivot('ID_Almacen');
	}
}
