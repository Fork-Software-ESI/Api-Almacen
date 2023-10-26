<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ChoferCamion
 * 
 * @property int $ID_Chofer
 * @property int $ID_Camion
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
	protected $table = 'chofer_camion';
	public $incrementing = false;
	public $timestamps = true;

	protected $casts = [
		'ID_Chofer' => 'int',
		'ID_Camion' => 'int',
		'Fecha_Hora_Inicio' => 'datetime'
	];

	protected $fillable = [
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
