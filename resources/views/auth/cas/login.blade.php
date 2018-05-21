@extends('layouts.app')

@section('content')
	<div style="position: absolute; width: 100%; top: 50%; transform: translateY(-50%); display: flex; justify-content: center;">
		<div>
			<span style="color: green" class="glyphicon glyphicon-ok"></span><br />
			Vous allez être redirigé(e) vers le CAS-UTC pour vous connecter dans quelques secondes...<br />
			<br />
			<input type="button" id="connectCAS" value="Se connecter" onClick="window.location.href = '{{ config('portail.cas.url').'login?service='.route('login.process', ['provider' => $provider]) }}'"></input>
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
