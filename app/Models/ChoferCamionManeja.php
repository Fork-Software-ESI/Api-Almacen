<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ChoferCamionManeja
 * 
 * @property int $ID_Chofer
 * @property int $ID_Camion
 * @property Carbon $Fecha_Hora_Fin
 * 
 * @property ChoferCamion $chofer_camion
 *
 * @package App\Models
 */
class ChoferCamionManeja extends Model
{
	use SoftDeletes;
	protected $table = 'chofer_camion_maneja';
	public $incrementing = false;
	public $timestamps = true;

	protected $casts = [
		'ID_Chofer' => 'int',
		'ID_Camion' => 'int',
		'Fecha_Hora_Fin' => 'datetime'
	];

	protected $fillable = [
		'Fecha_Hora_Fin'
	];

	public function chofer_camion()
	{
		return $this->belongsTo(ChoferCamion::class, 'ID_Chofer')
					->where('chofer_camion.ID_Chofer', '=', 'chofer_camion_maneja.ID_Chofer')
					->where('chofer_camion.ID_Camion', '=', 'chofer_camion_maneja.ID_Camion');
	}
}
