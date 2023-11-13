<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GerenteAlmacen
 * 
 * @property int $ID_Gerente
 * @property int $ID_Almacen
 * 
 * @property Persona $persona
 * @property Almacen $almacen
 * @property Collection|GerenteForma[] $gerente_formas
 * @property Collection|GerenteLote[] $gerente_lotes
 * @property Collection|GerentePaquete[] $gerente_paquetes
 *
 * @package App\Models
 */
class GerenteAlmacen extends Model
{
	protected $table = 'gerente_almacen';
	protected $primaryKey = 'ID_Gerente';
	public $incrementing = false;
	public $timestamps = true;

	protected $casts = [
		'ID_Gerente' => 'int',
		'ID_Almacen' => 'int'
	];

	protected $fillable = [
		'ID_Gerente',
		'ID_Almacen'
	];

	public function persona()
	{
		return $this->belongsTo(Persona::class, 'ID_Gerente');
	}

	public function almacen()
	{
		return $this->belongsTo(Almacen::class, 'ID_Almacen');
	}

	public function gerente_formas()
	{
		return $this->hasMany(GerenteForma::class, 'ID_Gerente');
	}

	public function gerente_lotes()
	{
		return $this->hasMany(GerenteLote::class, 'ID_Gerente');
	}

	public function gerente_paquetes()
	{
		return $this->hasMany(GerentePaquete::class, 'ID_Gerente');
	}
}