<div class="btn-group">
  <button class="btn btn-sm btn-success" data-toggle="modal" data-target="#validate-modal-{{ implode('-', $ids) }}">
    <i class="fa fa-check"></i><span class="hidden-xs"> Valider</span>
  </button>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="validate-modal-{{ implode('-', $ids) }}">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Validation d'un membre</h4>
      </div>
      <form action="{{ url('/admin/management/assos/members/'.implode('/', $ids)) }}" method="post">
        {{ csrf_field() }}

        <div class="modal-body">
		  <ul>
			  <li>Membre: {{ $member->user->name }}</li>
			  <li>Association: {{ $member->asso->name }}</li>
		  </ul>
          <p>Confirmer ?</p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success">Confirmer</button>
        </div>
      </form>
    </div>
  </div>
</div>
