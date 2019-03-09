<div class="btn-group">
  <button class="btn btn-sm btn-twitter" data-toggle="modal" data-target="#edit-modal-{{ implode('-', $ids) }}">
    <i class="fa fa-check"></i><span class="hidden-xs"> Modifier</span>
  </button>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="edit-modal-{{ implode('-', $ids) }}">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		<h4 class="modal-title">Modification d'une permission utilisateur</h4>
      </div>
      <form action="{{ url('/admin/management/users/permissions/'.implode('/', $ids)) }}" method="post">
	    <input type="hidden" name="_method" value="put" />
        {{ csrf_field() }}

        <div class="modal-body">
		  <ul>
			  <li>Membre: {{ $data->user->name }}</li>
			  <li>Validé par: {{ $data->validated_by->name ?? 'personne' }}</li>
			  <li>
				  Role:
				  <select name="permission_id">
				  @foreach ($permissions as $permission)
				    <option value="{{ $permission->id }}" {{ $permission->id === $data->permission_id ? 'selected' : ''}}>{{ $permission->name }}</option>
				  @endforeach
				  </select>
			  </li>
		  </ul>
          <p>Confirmer les modifications ?</p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="submit" class="btn btn-success">Modifier</button>
        </div>
      </form>
    </div>
  </div>
</div>
