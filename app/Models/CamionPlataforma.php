<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CamionPlataforma
 * 
 * @property int $ID_Camion
 * @property int $ID_Almacen
 * @property int $Numero_Plataforma
 * @property Carbon $Fecha_Hora_Llegada
 * 
 * @property Camion $camion
 * @property Plataforma $plataforma
 * @property CamionPlataformaSalida $camion_plataforma_salida
 *
 * @package App\Models
 */
class CamionPlataforma extends Model
{
	use SoftDeletes;
	protected $table = 'camion_plataforma';

	protected $primaryKey = ['ID_Camion', 'ID_Almacen', 'Numero_Plataforma'];
	public $incrementing = false;
	public $timestamps = true;

	protected $casts = [
		'ID_Camion' => 'int',
		'ID_Almacen' => 'int',
		'Numero_Plataforma' => 'int',
		'Fecha_Hora_Llegada' => 'datetime'
	];

	protected $fillable = [
		'ID_Camion',
		'ID_Almacen',
		'Numero_Plataforma',
		'Fecha_Hora_Llegada'
	];

	public function camion()
	{
		return $this->belongsTo(Camion::class, 'ID_Camion');
	}

	public function plataforma()
	{
		return $this->belongsTo(Plataforma::class, ['ID_Almacen', 'Numero_Plataforma'], ['ID_Almacen', 'Numero']);
	}

	public function camion_plataforma_salida()
	{
		return $this->hasMany(CamionPlataformaSalida::class, ['ID_Camion', 'ID_Almacen', 'Numero_Plataforma']);
	}
}
