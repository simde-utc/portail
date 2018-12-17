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

class Contact extends Model implements OwnableContract
{
    use HasMorphOwner;

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
