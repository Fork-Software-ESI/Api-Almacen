<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Chofer
 * 
 * @property int $ID
 * 
 * @property Persona $persona
 * @property Collection|Camion[] $camions
 * @property Collection|ChoferTipoLibretum[] $chofer_tipo_libreta
 *
 * @package App\Models
 */
class Chofer extends Model
{
	protected $table = 'chofer';
	protected $primaryKey = 'ID';
	public $incrementing = false;
	public $timestamps = true;

	protected $fillable = [
		'ID'
	];
	protected $casts = [
		'ID' => 'int'
	];

	public function persona()
	{
		return $this->belongsTo(Persona::class, 'ID');
	}

	public function camions()
	{
		return $this->belongsToMany(Camion::class, 'chofer_camion', 'ID_Chofer', 'ID_Camion')
					->withPivot('Fecha_Hora_Inicio');
	}

	public function chofer_tipo_libreta()
	{
		return $this->hasMany(ChoferTipoLibretum::class, 'ID');
	}
}
