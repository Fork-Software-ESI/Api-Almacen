<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CamionLlevaLote
 * 
 * @property int $ID_Lote
 * @property Carbon $Fecha_Hora_Fin
 * 
 * @property LoteCamion $lote_camion
 *
 * @package App\Models
 */
class CamionLlevaLote extends Model
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'camion_lleva_lote';
	protected $primaryKey = 'ID_Lote';
	public $incrementing = false;
	public $timestamps = true;

	protected $casts = [
		'ID_Lote' => 'int',
		'Fecha_Hora_Fin' => 'datetime'
	];

	protected $fillable = [
		'Fecha_Hora_Fin'
	];

	public function lote_camion()
	{
		return $this->belongsTo(LoteCamion::class, 'ID_Lote');
	}
}