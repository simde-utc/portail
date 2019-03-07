<div class="btn-group">
  <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#delete-modal-{{ implode('-', $ids) }}">
    <i class="fa fa-check"></i><span class="hidden-xs"> Supprimer</span>
  </button>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="delete-modal-{{ implode('-', $ids) }}">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Retrait d'un membre</h4>
      </div>
      <form action="{{ url('/admin/assos/members/'.implode('/', $ids)) }}" method="post">
		<input type="hidden" name="_method" value="delete" />
        {{ csrf_field() }}

        <div class="modal-body">
		  <ul>
			  <li>Membre: {{ $member->user->name }}</li>
			  <li>Association: {{ $member->asso->name }}</li>
			  <li>Validé par: {{ $member->validated_by->name ?? 'personne' }}</li>
		  </ul>
          <p>Supprimer ?</p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success">Supprimer</button>
        </div>
      </form>
    </div>
  </div>
</div>
