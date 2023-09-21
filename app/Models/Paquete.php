<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Paquete
 * 
 * @property int $ID
 * @property int $ID_Cliente
 * @property string|null $Descripcion
 * @property int $Peso_Kg
 * @property string $Estado
 * @property string $Destino
 * 
 * @property Cliente $cliente
 * @property Forma $forma
 * @property FuncionarioForma $funcionario_forma
 * @property GerentePaquete $gerente_paquete
 * @property Collection|Estante[] $estantes
 *
 * @package App\Models
 */
class Paquete extends Model
{
	protected $table = 'paquete';
	protected $primaryKey = 'ID';
	public $timestamps = false;

	protected $casts = [
		'ID_Cliente' => 'int',
		'Peso_Kg' => 'int'
	];

	protected $fillable = [
		'ID_Cliente',
		'Descripcion',
		'Peso_Kg',
		'Estado',
		'Destino'
	];

	public function cliente()
	{
		return $this->belongsTo(Cliente::class, 'ID_Cliente');
	}

	public function forma()
	{
		return $this->hasOne(Forma::class, 'ID_Paquete');
	}

	public function funcionario_forma()
	{
		return $this->hasOne(FuncionarioForma::class, 'ID_Paquete');
	}

	public function gerente_paquete()
	{
		return $this->hasOne(GerentePaquete::class, 'ID_Paquete');
	}

	public function estantes()
	{
		return $this->belongsToMany(Estante::class, 'paquete_estante', 'ID_Paquete', 'ID_Estante')
					->withPivot('ID_Almacen');
	}
}
