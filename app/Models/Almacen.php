<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;


/**
 * Class Almacen
 * 
 * @property int $ID
 * @property string $Direccion
 * 
 * @property Collection|Estante[] $estantes
 * @property Collection|GerenteAlmacen[] $gerente_almacens
 * @property Collection|Plataforma[] $plataformas
 *
 * @package App\Models
 */
class Almacen extends Model
{
	protected $table = 'almacen';
	protected $primaryKey = 'ID';
	public $timestamps = true;

	protected $fillable = [
		'Direccion'
	];

	public function estantes()
	{
		return $this->hasMany(Estante::class, 'ID_Almacen');
	}

	public function gerente_almacens()
	{
		return $this->hasMany(GerenteAlmacen::class, 'ID_Almacen');
	}

	public function plataformas()
	{
		return $this->hasMany(Plataforma::class, 'ID_Almacen');
	}
}
