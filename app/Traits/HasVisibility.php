<?php
/**
 * Indique que les modèles possèdent une visibilité.
 *
 * @author Natan Danous <natous.danous@hotmail.fr>
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 * @author Rémy Huet <remyhuet@gmail.com>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

namespace App\Traits;

use App\Exceptions\PortailException;
use App\Models\Visibility;
use App\Models\User;
use App\Models\AuthCas;
use App\Facades\Ginger;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;

trait HasVisibility
{
    /**
     * Permet de savoir si un même type de ressource est visible pour une même visibilité à un même utilisateur.
     * @var array of arrays
     */
    private $is = [];

    /**
     * Fonction qui renvoie une nouvelle instance du modèle si celui-ci n'est pas visible par l'utilisateur.
     *
     * @param  mixed    $data
     * @param  boolean  $remove
     * @param  callable $callback
     * @return mixed
     */
    public function hide($data, bool $remove=true, callable $callback=null)
    {
        if ($data instanceof Collection) {
            return $this->hideCollection($data, $remove, $callback);
        } else if ($data instanceof Model) {
            return $this->hideModel($data, $remove, $callback);
        }
    }

    /**
     * Cache les éléments non-visible d'une collection.
     *
     * @param  Collection $collection
     * @param  boolean    $remove
     * @param  callable   $callback
     * @return Collection
     */
    protected function hideCollection(Collection $collection, bool $remove=true, callable $callback=null)
    {
        foreach ($collection as $key => $model) {
            if ($this->isVisible($model)) {
                if (method_exists($model, 'hide')) {
                    $collection[$key] = $model->hide();
                }

                if ($callback) {
                    $collection[$key] = $callback($collection[$key]);
                }
            } else {
                if ($remove) {
                    $collection->forget($key);
                } else {
                    $name = get_class($model);
                    $hidden = new $name;
                    $hidden->id = $model->id;
                    $hidden->visibility = $model->visibility;

                    $collection[$key] = $hidden;
                }
            }
        }

        return $collection->values();
    }

    /**
     * Cache les éléments non-visible d'un modèle.
     *
     * @param  Model    $model
     * @param  boolean  $remove
     * @param  callable $callback
     * @return Model|null
     */
    protected function hideModel(Model $model, bool $remove=true, callable $callback=null)
    {
        if ($this->isVisible($model)) {
            if (method_exists($model, 'hide')) {
                $model = $model->hide();
            }

            if ($callback) {
                $model = $callback($model);
            }

            return $model;
        } else {
            if ($remove) {
                return null;
            } else {
                $name = get_class($model);
                $hidden = new $name;
                $hidden->id = $model->id;
                $hidden->visibility = $model->visibility;

                return $hidden;
            }
        }
    }

    /**
     * Fonction permettant d'indiquer si la ressource peut-être visible ou non pour l'utilisateur actuel.
     *
     * @param Model  $model
     * @param string $user_id
     * @return boolean
     */
    public function isVisible(Model $model, string $user_id=null)
    {
        // TODO: Il faut faire passer un userid en option.
        $user_id = ($user_id ?? \Auth::id());

        // Si on est pas connecté, on regarde si la visibilité est publique ou non.
        if ($user_id === null) {
            return is_null($model->visibility_id) || ($model->visibility_id === Visibility::getTopStage()->first()->id);
        }

        // Si le modèle n'a pas de visibilité, on prend la première visibilité, la plus ouverte.
        if ($model->visibility_id) {
            $visibility = $model->visibility;
        } else {
            $visibility = Visibility::getTopStage()->first();
        }

        if ($visibility === null) {
            throw new PortailException('La relation de visibilité n\'a pas été trouvée');
        }

        $type = 'is'.ucfirst($visibility->type);

        if (!isset($this->is[$user_id])) {
            $this->is[$user_id] = [];
        }

        if (!isset($this->is[$user_id][$visibility->type])) {
            $isVisible = (method_exists(get_class(), $type) && $this->$type($user_id, $model));

            if ($visibility->type === 'private') {
                // Quand c'est privée ça dépend de la ressource.
                return $isVisible;
            } else {
                $this->is[$user_id][$visibility->type] = $isVisible;
            }
        }

        return $this->is[$user_id][$visibility->type];
    }

    /**
     * Indique que l'utilisateur est publique.
     *
     * @param  string $user_id
     * @param  mixed  $model
     * @return boolean
     */
    public function isPublic(string $user_id=null, $model=null)
    {
        return true;
    }

    /**
     * Indique que l'utilisateur possède un vrai compte.
     *
     * @param  string $user_id
     * @param  mixed  $model
     * @return boolean
     */
    public function isActive(string $user_id, $model=null)
    {
        return ($user = User::find($user_id)) && $user->isActive();
    }

    /**
     * Indique que l'utilisateur est connectable au CAS.
     *
     * @param  string $user_id
     * @param  mixed  $model
     * @return boolean
     */
    public function isCas(string $user_id, $model=null)
    {
        $cas = AuthCas::where('user_id', $user_id)->first();

        return $cas && $cas->where('is_active', true)->exists();
    }

    /**
     * Indique que l'utilisateur est contributeur au CAS.
     *
     * @param  string $user_id
     * @param  mixed  $model
     * @return boolean
     */
    public function isContributorBDE(string $user_id, $model=null)
    {
        $cas = AuthCas::where('user_id', $user_id)->first();

        return $cas && Ginger::user($cas->login)->isContributor();
    }

    /**
     * Indique que l'utilisateur est membre de l'instance.
     *
     * @param  string $user_id
     * @param  mixed  $model
     * @return boolean
     */
    abstract public function isPrivate(string $user_id, $model=null);

    /**
     * Indique que l'utilisateur peut voir les données internes.
     *
     * @param  string $user_id
     * @param  mixed  $model
     * @return boolean
     */
    public function isInternal(string $user_id, $model=null)
    {
        return User::find($user_id)->hasOneRole('superadmin');
    }
}
