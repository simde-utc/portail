<?php
/**
 * Supprime les outils inutiles de Laravel Admin.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2018, SiMDE-UTC
 * @license GNU GPL-3.0
 */

use Encore\Admin\Form;

Form::forget(['editor', 'map']);
