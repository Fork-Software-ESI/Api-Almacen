<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cliente
 * 
 * @property int $ID
 * 
 * @property Persona $persona
 * @property Collection|Paquete[] $paquetes
 *
 * @package App\Models
 */
class Cliente extends Model
{
	protected $table = 'cliente';
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

	public function paquetes()
	{
		return $this->hasMany(Paquete::class, 'ID_Cliente');
	}
}
