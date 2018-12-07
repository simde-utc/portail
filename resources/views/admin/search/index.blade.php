<style>
    .param-add,.param {
        margin-bottom: 10px;
    }

    .param-add .form-group,.param .form-group {
        margin: 0;
    }

    .status-label {
        margin: 10px;
    }

    .response-tabs pre {
        border-radius: 0px;
    }

    .param-remove {
        margin-left: 5px !important;
    }

    .param-desc {
        display: block;
        margin-top: 5px;
        margin-bottom: 10px;
        color: #737373;
    }

    .nav-stacked>li {
        border-bottom: 1px solid #f4f4f4;
        margin: 0 !important;
    }

    .nav>li>a {
        padding: 10px 10px;
    }
</style>

<div class="row">
    <div class="col-md-6 col-md-offset-3">
        <div class="box box-info">
            <form class="form-horizontal user-form" method="post">
                {{ csrf_field() }}

                <div class="box-body">
                    <div class="form-group">
                        <div class="col-sm-10">
                            Recherche d'un utilisateur (tous les champs peuvent être à moitié rempli).
                        </div>
                    </div>

                    @if ($errors->has('general'))
                        <p class="bg-danger">{{ $errors->get('general')[0] }}</p>
                    @endif

                    @foreach ($fields as $name)
                        <div class="form-group">
                            <label for="{{ $name }}" class="col-sm-2 control-label">{{ ucfirst(__($name)) }}</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="{{ $name }}" name="{{ $name }}">
                            </div>
                        </div>
                    @endforeach

                    <div class="form-group" style="margin-bottom: 0px;">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-primary">Rechercher</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
