<style>
    .param-add,.param {
        margin-bottom: 10px;
    }

    hr {
        margin-top: 30px;
        margin-bottom: 30px;
        overflow: visible; /* For IE */
        padding: 0;
        border-color: #333;
        color: #333;
        text-align: center;
    }

    hr:after {
        content: "OU";
        display: inline-block;
        position: relative;
        top: -0.7em;
        padding: 0 0.25em;
        background: white;
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

    .submit-wrapper{
        padding-right: 15px;
        display: flex;
        justify-content: end;
    }
</style>

<script>
    function handleSubmit(event){
        document.getElementById("quick_search").value = '';
    }
</script>

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
                        <p style="padding:1rem;" class="bg-danger">{{ $errors->get('general')[0] }}</p>
                    @endif

                    @foreach ($fields as $name => $label)
                        <div class="form-group">
                            <label for="{{ $name }}" class="col-sm-2 control-label">{{ ucfirst(__($label)) }}</label>

                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="{{ $name }}" name="{{ $name }}">
                            </div>
                        </div>
                    @endforeach
                    <div class="form-group" style="margin-bottom: 0px; margin-left: auto;">
                        <div class="submit-wrapper">
                            <button onclick="handleSubmit()" class="btn btn-primary">Rechercher</button>
                        </div>
                    </div>
                </form>
                <hr>
                <form class="form-horizontal user-form" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="quick_search" class="col-sm-2 control-label">Recherche rapide</label>

                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="quick_search" name="quick_search">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0px;">
                        <div class="submit-wrapper">
                            <button type="submit" class="btn btn-primary">Rechercher</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
