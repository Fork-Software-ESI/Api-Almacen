<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Camion
 * 
 * @property int $ID
 * @property string $Matricula
 * @property int $PesoMaximoKg
 * 
 * @property Collection|Plataforma[] $plataformas
 * @property Collection|Chofer[] $chofers
 * @property Collection|Lote[] $lotes
 *
 * @package App\Models
 */
class Camion extends Model
{
	protected $table = 'camion';
	protected $primaryKey = 'ID';
	public $timestamps = false;

	protected $casts = [
		'PesoMaximoKg' => 'int'
	];

	protected $fillable = [
		'Matricula',
		'PesoMaximoKg'
	];

	public function plataformas()
	{
		return $this->belongsToMany(Plataforma::class, 'camion_plataforma', 'ID_Camion', 'ID_Almacen')
					->withPivot('Numero_Plataforma', 'Fecha_Hora_Llegada');
	}

	public function chofers()
	{
		return $this->belongsToMany(Chofer::class, 'chofer_camion', 'ID_Camion', 'ID_Chofer')
					->withPivot('Fecha_Hora_Inicio');
	}

	public function lotes()
	{
		return $this->belongsToMany(Lote::class, 'lote_camion', 'ID_Camion', 'ID_Lote')
					->withPivot('Fecha_Hora_Inicio', 'Estado');
	}
}
