<?php

if (!function_exists('validation_between')) {
	/**
	 * Donne la chaine between pour les validations Requests
	 *
	 * @return string
	 */
	function validation_between(string $id) {
		$values = config("validation.$id");
		return "between:" . $values['min'] . "," . $values['min'];
	}
}

if (!function_exists('validation_max')) {
	/**
	 * Donne la valeur entière max pour les Migrations
	 *
	 * @return int
	 */
	function validation_max(string $id) {
		return config("validation.$id.max");
	}
}
