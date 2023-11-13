<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FuncionarioPaqueteEstante
 * 
 * @property int $ID_Funcionario
 * @property int $ID_Paquete
 * 
 * @property FuncionarioAlmacen $funcionario_almacen
 * @property PaqueteEstante $paquete_estante
 *
 * @package App\Models
 */
class FuncionarioPaqueteEstante extends Model
{
	protected $table = 'funcionario_paquete_estante';
	protected $primaryKey = 'ID_Paquete';
	public $incrementing = false;
	public $timestamps = true;

	protected $casts = [
		'ID_Funcionario' => 'int',
		'ID_Paquete' => 'int'
	];

	protected $fillable = [
		'ID_Funcionario',
		'ID_Paquete'
	];

	public function funcionario_almacen()
	{
		return $this->belongsTo(FuncionarioAlmacen::class, 'ID_Funcionario');
	}

	public function paquete_estante()
	{
		return $this->belongsTo(PaqueteEstante::class, 'ID_Paquete');
	}
}