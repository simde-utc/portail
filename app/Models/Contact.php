<?php

namespace App\Models;

use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use Cog\Contracts\Ownership\Ownable as OwnableContract;
use App\Exceptions\PortailException;

class Contact extends Model implements OwnableContract // TODO $must
{
    use HasMorphOwner;

    protected $fillable = [
        'name', 'value', 'contact_type_id', 'visibility_id', 'owned_by_id', 'owned_by_type',
    ];

    protected $with = [
        'type', 'visibility',
    ];

    protected $hidden = [
        'contact_type_id', 'visibility_id', 'owned_by_id', 'owned_by_type',
    ];

    protected $must = [
        'value', 'type'
    ];

    protected $selection = [
        'paginate' => null,
        'order' => null,
		'filter' => [],
    ];

    public static function boot() {
        $verificator = function ($model) {
            if ($type = $model->type) {
                if (!preg_match("/$type->pattern/", $model->value))
                    throw new PortailException('L\'entrée n\'est pas valide et ne correspond pas au type de contact "'.$type->name.'"', 400);
            }
            else
                throw new PortailException('Le type donné n\'existe pas', 400);
        };

        static::creating($verificator);
        static::updating($verificator);
    }

    public function owned_by() {
        return $this->morphTo();
    }

	public function visibility() {
    	return $this->belongsTo(Visibility::class);
    }

    public function type() {
        return $this->belongsTo(ContactType::class, 'contact_type_id');
    }

	public function user() {
		return $this->morphTo(User::class, 'owned_by');
	}

	public function asso() {
		return $this->morphTo(Asso::class, 'owned_by');
	}

	public function client() {
		return $this->morphTo(Client::class, 'owned_by');
	}

	public function group() {
		return $this->morphTo(Group::class, 'owned_by');
	}
}
