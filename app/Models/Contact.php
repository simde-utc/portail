<?php
/**
 * Modèle correspondant aux contacts.
 *
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Models;

use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use Cog\Contracts\Ownership\Ownable as OwnableContract;
use App\Exceptions\PortailException;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\Model\HasVisibilitySelection;

class Contact extends Model implements OwnableContract
{
    use HasMorphOwner, HasVisibilitySelection;

    protected $fillable = [
        'name', 'value', 'type_id', 'visibility_id', 'owned_by_id', 'owned_by_type',
    ];

    protected $with = [
        'type', 'visibility',
    ];

    protected $hidden = [
        'type_id', 'visibility_id', 'owned_by_id', 'owned_by_type',
    ];

    protected $must = [
        'value', 'type'
    ];

    protected $selection = [
        'visibilities' => '*',
        'paginate' => null,
        'order' => null,
        'filter' => [],
    ];

    /**
     * Appelé à la création du modèle
     *
     * @return void
     */
    public static function boot()
    {
        $verificator = function ($model) {
            if ($type = $model->type) {
                if (!preg_match("/$type->pattern/", $model->value)) {
                    throw new PortailException('L\'entrée n\'est pas valide et ne correspond pas
                        au type de contact "'.$type->name.'"', 400);
                }
            } else {
                throw new PortailException('Le type donné n\'existe pas', 400);
            }

            $keys = $model->getKeyName();

            if (!is_array($keys)) {
                $keys = [$model->getKeyName()];
            }

            foreach ($keys as $key) {
                $model->$key = $model->$key ?: \Uuid::generate()->string;
            }

            return $model;
        };

        static::creating($verificator);
        static::updating($verificator);
    }

    /**
     * Scope spécifique pour n'avoir que les ressources privées.
     *
     * @param  Builder $query
     * @return Builder
     */
    public function scopePrivateVisibility(Builder $query)
    {
        $visibility = $this->getSelectionForVisibility('private');
        $user = $this->getUserForVisibility();

        if ($user) {
            $asso_ids = $user->currentJoinedAssos()->pluck('id')->toArray();

            return $query->where('visibility_id', $visibility->id)->where(function ($subQuery) use ($user, $asso_ids) {
                return $subQuery->where(function ($subSubQuery) use ($user) {
                    return $subSubQuery->where('owned_by_type', User::class)->where('owned_by_id', $user->id);
                })->orWhere(function ($subSubQuery) use ($asso_ids) {
                    return $subSubQuery->where('owned_by_type', Asso::class)->whereIn('owned_by_id', $asso_ids);
                })->orWhere(function ($subSubQuery) use ($asso_ids) {
                    return $subSubQuery->where('owned_by_type', Client::class)
                        ->whereIn('owned_by_id', Client::whereIn('asso_id', $asso_ids)->pluck('id')->toArray());
                })->orWhere(function ($subSubQuery) use ($user) {
                    return $subSubQuery->where('owned_by_type', Group::class)
                        ->whereIn('owned_by_id', $user->groups()->pluck('id')->toArray());
                });
            });
        }
    }

    /**
     * Relation avec l'instance possédant le moyen de contact.
     *
     * @return mixed
     */
    public function owned_by()
    {
        return $this->morphTo();
    }

    /**
     * Relation avec la visibilité.
     *
     * @return mixed
     */
    public function visibility()
    {
        return $this->belongsTo(Visibility::class);
    }

    /**
     * Relation avec la type de contact.
     *
     * @return mixed
     */
    public function type()
    {
        return $this->belongsTo(ContactType::class, 'type_id');
    }

    /**
     * Relation avec l'utilisateur.
     *
     * @return mixed
     */
    public function user()
    {
        return $this->morphTo(User::class, 'owned_by');
    }

    /**
     * Relation avec l'association.
     *
     * @return mixed
     */
    public function asso()
    {
        return $this->morphTo(Asso::class, 'owned_by');
    }

    /**
     * Relation avec le client oauth.
     *
     * @return mixed
     */
    public function client()
    {
        return $this->morphTo(Client::class, 'owned_by');
    }

    /**
     * Relation avec le groupe.
     *
     * @return mixed
     */
    public function group()
    {
        return $this->morphTo(Group::class, 'owned_by');
    }
}
