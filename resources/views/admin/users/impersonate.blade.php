<div class="btn-group pull-right" style="margin-left: 25px; margin-right: 5px">
    <button class="btn btn-sm btn-twitter"  data-toggle="modal" data-target="#impersonate-modal">
        <i class="fa fa-exchange"></i><span class="hidden-xs"> Personnifier</span>
    </button>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="impersonate-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Personnification - {{ $user->name }}</h4>
      </div>
      <form id="impersonate-user" action="{{ url('/admin/users/'.$user->id.'/impersonate') }}" method="post">
          {{ csrf_field() }}

          <div class="modal-body">
            <p>Voulez-vous vraiment devenir {{ $user->name }} (email: {{ $user->email }}) ?<br />
            Ceci enverra une notification à la personne concernée.</p>
            <br/>
            <div class="col-md-offset-3">
                <p>Raisons:</p>
                <textarea style="width: 100%" rows="2" name="description" required></textarea>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Personnifier</button>
          </div>
      </form>
    </div>
  </div>
</div>
