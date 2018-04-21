<?php

namespace App\Traits;

use App\Services\Ginger;
use App\Models\Visibility;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait HasVisibility
{
    /**
     * Fonction qui renvoie une nouvelle instance du modèle si celui-ci
     * n'est pas visible par l'utilisateur.
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function hide() {
        if ($this->isVisible()) {
            $model = new static;

            // On ne renvoie pas l'id du modèle par sécurité, 
            // ainsi on ne peut plus accèder aux relations du modèle caché
            $model->message = "Vous ne pouvez pas voir cela.";
            $model->visibility = $this->visibility;

            // Debug
            $model->visibility_id = $this->visibility_id;
            
            return $model;
        } else {
            return $this;
        } 
    }

    /**
     * Fonction permettant de retourner le type du niveau de visibilité de l'utilisateur actuel
     *
     * @return string
     */
    public function getVisibilityType() {
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

            if (method_exists(get_class(), $type) && self::$type(null, Auth::user()->id))
                $result = $visibility->type;
            else
                break;

            $visibility_id = $visibility->parent_id;
        }

        return $result;
    }

    /**
     * Fonction permettant d'indiquer si la ressource peut-être visible ou non pour l'utilisateur actuel
     *
     * @param Illuminate\Database\Eloquent\Collection $visibilities
     * @param Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    public function isVisible() {
        $visibilities = Visibility::all();

        if ($visibilities === null)
            return true;

        // Si le modèle n'a pas de visibilité, on prend la première visibilité,
        // la plus ouverte.
        if ($this->visibility_id === null)
            $visibility_id = $visibilities->first()->id;
        else 
            $visibility_id = $this->visibility_id;

        // Si on est pas connecté, on regarde si la visibilité est publique ou non
        if (Auth::user() === null)
            return $visibilities->find($visibility_id)->type === 'public';

        while ($visibility_id !== null) {
            $visibility = $visibilities->find($visibility_id);

            if ($visibility === null)
                return false;

            $type = 'is'.ucfirst($visibility->type);

            if (method_exists(get_class(), $type) && $this->$type(Auth::user()->id))
                return true;

            $visibility_id = $visibility->parent_id;
        }

        return false;
    }

    public function isPublic($user_id) {
        return true;
    }

    public function isLogged($user_id) {
        return User::find($user_id)->exists();
    }

    public function isCasOrWasCas($user_id) {
        return AuthCas::find($user_id)->exists();
    }

    public function isCas($user_id) {
        return AuthCas::find($user_id)->where('is_active', true)->exists();
    }

    public function isStudent($user_id) {
        return Ginger::user(AuthCas::find($user_id)->login)->getType() === 'etu';
    }

    public function isContributor($user_id) {
        return Ginger::user(AuthCas::find($user_id)->login)->isContributor();
    }

    public function isPrivate($user_id) {
        try {
            $member = $this->members->find($user_id);
        }
        catch (Exception $e) {
            $member = null;
        }

        return $member !== null;
    }

    public function isOwner($user_id) {
        return $this->user_id === $user_id;
    }

    public function isInternal($user_id) {
        return User::find($user_id)->hasOneRole('superadmin');
    }
}