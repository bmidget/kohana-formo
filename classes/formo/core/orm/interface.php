<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_ORM_interface_Core interface.
 *
 * @package   Formo
 * @category  Decorators
 */
interface Formo_Core_ORM_Interface {

	public function pre_render();
	public function set_field(Formo_Container $field, $value);
}