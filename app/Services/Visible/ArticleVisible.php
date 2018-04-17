<?php

namespace App\Services\Visible;

use App\Models\User;

class ArticleVisible extends Visible {

	/**
	 * @param $model
	 * @param $user_id
	 * @return bool|void
	 *
	 * Override du isPrivate de Visible pour les articles. On n'est pas membre d'un article mais membre de l'asso qui le publie
	 */
	public static function isPrivate($model, $user_id) {
		$assos_article = $model->assos(); //On récupère les assos liées à l'article
		$user = User::find($user_id);
		$assos_user = $user->currentAsssos();

		foreach ($assos_article as $asso_article) { //Pour chacune de ces assos
			foreach ($assos_user as $asso_user) {
				if ($asso_article->id === $asso_user->id)
					return true;
			}
		}

		return false;
	}

}
