<div class="btn-group">
  <button class="btn btn-sm btn-twitter" data-toggle="modal" data-target="#accept-modal-{{ $access->id }}">
    <i class="fa fa-check"></i><span class="hidden-xs"> Répondre</span>
  </button>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="accept-modal-{{ $access->id }}">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Demande d'accès - <b>{{ $access->access['name'] }}</b> (n°{{ $access->access['utc_access'] }})</h4>
      </div>
      <form action="{{ url('/admin/management/access/'.$access->id) }}" method="post">
		<input type="hidden" name="_method" value="put" />
        {{ csrf_field() }}

        <div class="modal-body">
          <p>Une demande d'accès a été réalisée par <b>{{ $access->member['name'] }}</b> (id: {{ $access->member['id'] }}).</p>
          <p>La personne possède le rôle de <b>{{ $access->role['name'] }}</b> dans l'association <b>{{ $access->asso['shortname'] }}</b> (id: {{ $access->asso['id'] }}).</p>
          <br />
          <p>La demande est confirmée par <b>{{ $access->confirmed_by['name'] }}</b> (id: {{ $access->asso['id'] }}).</p>
          <div class="col-md-offset-2" style="padding-right: 25%">
            <p>Commentaire:</p>
            <textarea style="width: 100%" rows="2" name="comment"></textarea>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
          <button type="submit" name="validate" value="0" class="btn btn-danger">Refuser</button>
          <button type="submit" name="validate" value="1" class="btn btn-success">Valider</button>
        </div>
      </form>
    </div>
  </div>
</div>
