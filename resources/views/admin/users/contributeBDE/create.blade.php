<div class="btn-group pull-right" style="margin-left: 25px; margin-right: 5px">
    <button class="btn btn-sm btn-twitter" data-toggle="modal" data-target="#contributeBde-modal">
        <i class="fa fa-money"></i><span class="hidden-xs"> Cotiser</span>
    </button>
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="contributeBde-modal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Cotisation - {{ $user->name }}</h4>
      </div>
      <form id="contributeBde-user" action="{{ url('/admin/resources/users/'.$user->id.'/contributeBde') }}" method="post">
          {{ csrf_field() }}

          <div class="modal-body">
            <p>Voulez-vous faire cotiser {{ $user->name }} (email: {{ $user->email }}) ?</p>
            <p>Une notification de confirmation sera envoyée.</p>
            <div class="col-md-offset-3">
                <p>Montant payé:</p>
                <input type="radio" id="contributeBde-classic" name="money" value="20.00" checked />
                <label for="contributeBde-classic"> 20€ (UTC/ESCOM/exté)</label><br />
                <input type="radio" id="contributeBde-special" name="money" value="1.00" />
                <label for="contributeBde-special"> 1€ (membre d'honneur)</label><br />
                <input type="radio" id="contributeBde-custom" name="money" value="" />
                <label for="contributeBde-custom"> Autre montant </label>
                <input type="number" name="custom" min="0.00" max="100.00" step="0.10" value="0.00" style="margin-left: 20px; width: 50px" /> €
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
            <button type="submit" class="btn btn-primary">Faire cotiser</button>
          </div>
      </form>
    </div>
  </div>
</div>
