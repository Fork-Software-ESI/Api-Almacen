<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Lote
 * 
 * @property int $ID
 * @property string|null $Descripcion
 * @property int $Peso_Kg
 * @property int $ID_Estado
 * 
 * @property Collection|Forma[] $formas
 * @property GerenteLote $gerente_lote
 * @property Collection|Camion[] $camions
 *
 * @package App\Models
 */
class Lote extends Model
{
	use \Illuminate\Database\Eloquent\SoftDeletes;
	protected $table = 'lote';
	protected $primaryKey = 'ID';
	public $timestamps = true;

	protected $casts = [
		'Descripcion' => 'string',
		'Peso_Kg' => 'int',
		'ID_Estado' => 'int'
	];

	protected $fillable = [
		'Descripcion',
		'Peso_Kg',
		'ID_Estado'
	];

	public function formas()
	{
		return $this->hasMany(Forma::class, 'ID_Lote');
	}

	public function gerente_lote()
	{
		return $this->hasOne(GerenteLote::class, 'ID_Lote');
	}

	public function camions()
	{
		return $this->belongsToMany(Camion::class, 'lote_camion', 'ID_Lote', 'ID_Camion')
					->withPivot('Fecha_Hora_Inicio', 'Estado');
	}
}
