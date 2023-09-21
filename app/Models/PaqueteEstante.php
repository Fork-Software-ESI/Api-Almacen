<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PaqueteEstante
 * 
 * @property int $ID_Paquete
 * @property int $ID_Estante
 * @property int $ID_Almacen
 * 
 * @property Paquete $paquete
 * @property Estante $estante
 * @property FuncionarioPaqueteEstante $funcionario_paquete_estante
 *
 * @package App\Models
 */
class PaqueteEstante extends Model
{
	protected $table = 'paquete_estante';
	protected $primaryKey = 'ID_Paquete';
	public $incrementing = false;
	public $timestamps = false;

	protected $casts = [
		'ID_Paquete' => 'int',
		'ID_Estante' => 'int',
		'ID_Almacen' => 'int'
	];

	protected $fillable = [
		'ID_Estante',
		'ID_Almacen'
	];

	public function paquete()
	{
		return $this->belongsTo(Paquete::class, 'ID_Paquete');
	}

	public function estante()
	{
		return $this->belongsTo(Estante::class, 'ID_Estante')
					->where('estante.ID', '=', 'paquete_estante.ID_Estante')
					->where('estante.ID_Almacen', '=', 'paquete_estante.ID_Almacen');
	}

	public function funcionario_paquete_estante()
	{
		return $this->hasOne(FuncionarioPaqueteEstante::class, 'ID_Paquete');
	}
}
