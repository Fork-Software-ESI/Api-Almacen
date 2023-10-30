<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Estadof
 * 
 * @property int $ID
 * @property string $Estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Forma[] $formas
 *
 * @package App\Models
 */
class Estadof extends Model
{
	use SoftDeletes;
	protected $table = 'estadof';
	protected $primaryKey = 'ID';

	protected $fillable = [
		'Estado'
	];

	public function formas()
	{
		return $this->hasMany(Forma::class, 'ID_Estado');
	}
}
