<?php

namespace App\Traits;

use App\Models\Visibility;
use Illuminate\Support\Facades\Auth;

trait HasVisibility
{
    /**
     * Fonction permettant de renvoyer toutes les informations tout en cachant celles privées
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function hide() {
        $visibilities = Visibility::all();
        $collection = self::all();

        foreach ($collection as $key => $model) {
            if (!self::isVisible($visibilities, $model)) {
                $collection[$key] = self::hideData($visibilities, $model);
            }
        }

        return $collection;
    }

    // Natan : Utilité de cette fonction ?
    /**
     * Fonction permettant de renvoyer uniquement les informations privées en les cachant
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function hideWithout() {
        $visibilities = Visibility::all();
        $collection = self::all();

        foreach ($collection as $key => $model) {
            if (!self::isVisible($visibilities, $model)) {
                $collection[$key] = self::hideData($visibilities, $model);
            }
            else
                $collection->forget($key);
        }

        return $collection;
    }

    /**
     * Fonction permettant de renvoyer toutes les informations visibles
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function withVisible() {
        return self::removeData(false);
    }

    /**
     * Fonction permettant de renvoyer toutes les informations non-visibles
     *
     * @return Illuminate\Database\Eloquent\Collection
     */
    public static function withoutVisible() {
        return self::removeData(true);
    }

    /**
     * Fonction permettant de retirer toutes les informations visibles ou non-visibles
     *
     * @param bool
     * @return Illuminate\Database\Eloquent\Collection
     */
    protected static function removeData($visible) {
        $visibilities = Visibility::all();
        $collection = self::all();

        foreach ($collection as $key => $model) {
            if (self::isVisible($visibilities, $model) === $visible)
                $collection->forget($key);
        }

        return $collection;
    }

    /**
     * Fonction permettant de retourner le type du niveau de visibilité de l'utilisateur actuel
     *
     * @return string
     */
    public static function getVisibilityType() {
        $visibilities = Visibility::all();
        $visibility_id = $visibilities->first()->id;

        if (Auth::user() === null)
            return 'public';

        $result = 'public';

        while ($visibility_id !== null) {
            $visibility = $visibilities->find($visibility_id);

            if ($visibility === null)
                return false;

            $type = 'is'.ucfirst($visibility->type);

            if (method_exists(get_class(), $type) && $this->$type(null, Auth::user()->id))
                $result = $visibility->type;
            else
                break;

            $visibility_id = $visibility->parent_id;
        }

        return $result;
    }

    /**
     * Fonction permettant de cacher les infos d'un modèle
     *
     * @param Illuminate\Database\Eloquent\Collection $visibilities
     * @param Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    protected static function hideData($visibilities, $model) {
        
        // TODO: Besoin de choisir entre retourner soit un array soit un group avec attribut vides.

        return [
            'id' => $model->id,
            'hidden' => true,
            'visibility' => $visibilities->find($model->visibility_id),
        ];

        // return new self;
    }

    /**
     * Fonction permettant d'indiquer si la ressource peut-être visible ou non pour l'utilisateur actuel
     *
     * @param Illuminate\Database\Eloquent\Collection $visibilities
     * @param Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public static function isVisible($visibilities, $model) {
        if ($visibilities === null || $visibilities->count() === 0)
            return true;

        if ($model === null || $model->visibility_id === null)
            return false;

        $visibility_id = $model->visibility_id;

        if ($visibility_id === null)
            $visibility_id = $visibilities->first()->id;

        // Si on est pas co, on check si la visibilité est publique ou non
        if (Auth::user() === null)
            return $visibilities->find($visibility_id)->type === 'public';

        while ($visibility_id !== null) {
            $visibility = $visibilities->find($visibility_id);

            if ($visibility === null)
                return false;

            $type = 'is'.ucfirst($visibility->type);

            if (method_exists(get_class(), $type) && $this->$type($model, Auth::user()->id))
                return true;

            $visibility_id = $visibility->parent_id;
        }

        return false;
    }

    public function isPublic($model, $user_id) {
        return true;
    }

    public function isLogged($model, $user_id) {
        return Models\User::find($user_id)->exists();
    }

    public function isCasOrWasCas($model, $user_id) {
        return Models\AuthCas::find($user_id)->exists();
    }

    public function isCas($model, $user_id) {
        return Models\AuthCas::find($user_id)->where('is_active', true)->exists();
    }

    public function isContributor($model, $user_id) {
        return Ginger::userExists(Models\AuthCas::find($user_id)->login);
    }

    public function isPrivate($model, $user_id) {
        if ($model === null)
            return false;

        try {
            $member = $model->members->find($user_id);
        }
        catch (Exception $e) {
            $member = null;
        }

        return $member !== null;
    }

    public function isOwner($model, $user_id) {
        return $model !== null && $model->user_id === $user_id;
    }
}