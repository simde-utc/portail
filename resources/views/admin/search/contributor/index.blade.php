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
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-info">
            <form class="form-horizontal user-form" method="get" onsubmit="window.location.href = '/admin/search/contributor/' + $('#login').val(); return false;">
                {{ csrf_field() }}

                <div class="box-body">
                    <div class="form-group">
                        <div class="col-sm-10">
                            Recherche d'un cotisant.
                        </div>
                    </div>

                    @if ($errors->has('general'))
                        <p class="bg-danger">{{ $errors->get('general')[0] }}</p>
                    @endif

                    <div class="form-group">
                        <label for="login" class="col-sm-2 control-label">Login</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="login" name="login" value="{{ $login ?? null }}">
                        </div>
                    </div>

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
