<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Cog\Contracts\Ownership\Ownable as OwnableContract;
use Cog\Contracts\Ownership\CanBeOwner;
use Cog\Laravel\Ownership\Traits\HasMorphOwner;
use App\Traits\Model\HasCreatorSelection;
use App\Interfaces\Model\CanHaveComments;
use App\Interfaces\Model\CanComment;

class Comment extends Model implements CanBeOwner, OwnableContract, CanHaveComments
{
  use HasMorphOwner, HasCreatorSelection, SoftDeletes;

  protected $fillable = [
    'body', 'created_by_id', 'created_by_type', 'owned_by_id', 'owned_by_type',
  ];

  protected $dates = [
    'deleted_at'
  ];

  protected $with = [
    'user',
  ];

	protected $withModelName = [
		'created_by', 'owned_by',
	];

	protected $must = [
		'body', 'created_by', 'created_at',
	];

  // Correspond à celui qui publie le commentaire
  protected function created_by() {
    return $this->morphTo('created_by');
  }

  // Sur quoi le commentaire est réalisé
  protected function owned_by() {
    return $this->morphTo('owned_by');
  }

  public function comments() {
    return $this->morphMany(Comment::class, 'owned_by');
  }

  // Pour connaitre son accessibilité, on retrouve celle du parent qui agit sur un modèle
  public function isCommentAccessibleBy(string $user_id): bool {
    return $this->owned_by->isCommentAccessibleBy($user_id);
  }

  // Pour connaitre sa possibilité de modification, on retrouve celle du parent qui agit sur un modèle
  public function isCommentManageableBy(CanComment $model): bool {
    return $this->owned_by->isCommentManageableBy($model);
  }
}
