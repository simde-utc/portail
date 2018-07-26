@extends('layouts.app')

@section('content')
	<div class="row">
		<div class="col-md-8 drop-shadow rounded-corners mx-auto bg-white p-4">
			<span>Vous allez être redirigé(e) vers le CAS-UTC dans quelques secondes...</span>
			<br />
			<input type="button" class="btn btn-primary mt-3" id="connectCAS" value="Se connecter" onClick="window.location.href = '{{ config('portail.cas.url').'login?service='.route('login.process', ['provider' => $provider]) }}'"></input>
		</div>
	</div>
@endsection

@section('script')
	<script>
		$(document).ready(function() {
			if (!window.history.state) {
				window.history.replaceState(true, null, null);
				$('#connectCAS').click();
			}
		});
	</script>
@endsection
