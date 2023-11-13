<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ChoferCamion
 * 
 * @property int $ID_Chofer
 * @property int $ID_Camion
 * @property int $ID_Estado
 * @property Carbon $Fecha_Hora_Inicio
 * 
 * @property Camion $camion
 * @property Chofer $chofer
 * @property ChoferCamionManeja $chofer_camion_maneja
 *
 * @package App\Models
 */
class ChoferCamion extends Model
{
	use SoftDeletes;
	protected $table = 'chofer_camion';
	protected $primaryKey = ['ID_Chofer', 'ID_Camion'];
	public $incrementing = false;
	public $timestamps = true;

	protected $casts = [
		'ID_Chofer' => 'int',
		'ID_Camion' => 'int',
		'ID_Estado'=> 'int',
		'Fecha_Hora_Inicio' => 'datetime'
	];

	protected $fillable = [
		'ID_Chofer',
		'ID_Camion',
		'ID_Estado',
		'Fecha_Hora_Inicio'
	];

	public function camion()
	{
		return $this->belongsTo(Camion::class, 'ID_Camion');
	}

	public function chofer()
	{
		return $this->belongsTo(Chofer::class, 'ID_Chofer');
	}

	public function chofer_camion_maneja()
	{
		return $this->hasOne(ChoferCamionManeja::class, 'ID_Chofer');
	}
}
