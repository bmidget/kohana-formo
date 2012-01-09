<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Field_Core class.
 *
 * @package   Formo
 * @category  Forms and Fields
 */
class Formo_Core_Field extends Formo_Validator_Field {

	/**
	 * Field-specific settings
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_settings = array
	(
		// The field's driver
		'driver'       => 'input',
		// The wah this field is rendered
		'render_type'  => 'html',
		// Whether the field should be rendered
		'render'       => TRUE,
		// Whether the field is editable
		'editable'     => TRUE,
		// Original value added to field
		'value'        => NULL,
		// New values added to field
		'new_value'    => Formo::NOTSET,
		// A custom message file
		'message_file' => NULL,
		// Group items as in select options, individual radios, individual checkboxes, etc.
		'options'      => array(),
	);

	/**
	 * Create a new field
	 *
	 * @access public
	 * @param mixed $alias
	 * @param mixed $driver. (default: NULL)
	 * @param mixed array $options. (default: NULL)
	 * @return void
	 */
	public function __construct($alias, $driver = NULL, $value = NULL, array $options = NULL)
	{
		$options = func_get_args();
		$orig_options = $options;
		$options = Formo::args(__CLASS__, __FUNCTION__, $options);

		// Always process the driver first
		$driver = $options['driver'];
		unset($options['driver']);
		$options = Arr::merge(array('driver' => $driver), $options);

		// Add all the options to the object
		$this->_load_options($options);

		// Run the driver's post_construct() method
		$this->driver()->post_construct();
	}

	/**
	 * Render the field according to default render_type
	 *
	 * @access public
	 * @return view object
	 */
	public function __toString()
	{
		// Render the field
		try
		{
			return $this->render();
		}
		catch (Exception $e)
		{
			return "Error rendering field:".$e->getMessage();
		}
	}
	
	/**
	 * Overloaded sent method
	 *
	 * @access public
	 * @param mixed array $input. (default: NULL)
	 * @return bool
	 */
	public function sent(array $input = NULL)
	{
		if (method_exists($this->driver(), 'sent'))
		{
			// The driver may have something to say about this method
			return $this->driver()->sent();
		}

		// Let the parent determine if the field was sent
		return $this->parent()->sent();
	}

}
