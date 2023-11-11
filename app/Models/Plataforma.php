<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Plataforma
 * 
 * @property int $Numero
 * @property int $ID_Almacen
 * 
 * @property Almacen $almacen
 * @property Collection|Camion[] $camions
 *
 * @package App\Models
 */
class Plataforma extends Model
{
	use SoftDeletes;
	protected $table = 'plataforma';
	public $timestamps = true;
	protected $primaryKey = 'Numero';

	protected $fillable = [
		'Numero',
		'ID_Almacen'
	];

	protected $casts = [
		'ID_Almacen' => 'int'
	];

	protected $datos = ['deleted_at'];

	public function almacen()
	{
		return $this->belongsTo(Almacen::class, 'ID_Almacen');
	}

	public function camions()
	{
		return $this->belongsToMany(Camion::class, 'camion_plataforma', 'ID_Almacen', 'ID_Camion')
					->withPivot('Numero_Plataforma', 'Fecha_Hora_Llegada');
	}
}