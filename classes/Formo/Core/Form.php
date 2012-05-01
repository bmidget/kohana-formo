<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Formo_Form_Core class.
 *
 * @package   Formo
 * @category  Forms and Fields
 */
class Formo_Core_Form extends Formo_Validator {

	/**
	 * Form-specific settings
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_settings = array
	(
		// The config array
		'config'              => array(),
		// The orm config array
		'orm_config'          => array(),
		// Driver name for handling validation/rendering
		'driver'              => 'form',
		// Driver instance that handles orm comm
		'orm_driver_instance' => NULL,
		// A model associated with this form
		'model'               => NULL,
		// Whether the form was sent
		'sent'                => Formo::NOTSET,
		// The input object ($_GET/$_POST/etc)
		'input'               => array(),
		// If the object should be namespaces
		'namespace'           => FALSE,
		// The view path prefix
		'view_prefix'         => NULL,
		// Whether the field should render
		'render'              => TRUE,
		// Whether the field is editable
		'editable'            => TRUE,
		// A custom message file
		'message_file'        => NULL,
	);

	/**
	 * Construct the form object
	 *
	 * @access public
	 * @param mixed $alias. (default: NULL)
	 * @param mixed $driver. (default: NULL)
	 * @return void
	 */
	public function __construct($alias = NULL, $driver = NULL, array $options = NULL)
	{
		// Setup options array
		$options = func_get_args();
		$options = Formo::args(__CLASS__, __FUNCTION__, $options);

		// Load the config file
		$this->set('config', Kohana::$config->load('formo'));

		// Set the default alias and driver if necessary
		(empty($options['alias']) AND $options['alias'] = $this->get('config')->form_alias);
		(empty($options['driver']) AND $options['driver'] = $this->get('config')->form_driver);
		(empty($options['kind']) AND $options['kind'] = $this->get('config')->kind);
		
		// Always process the driver first
		$driver = $options['driver'];
		unset($options['driver']);
		$options = Arr::merge(array('driver' => $driver), $options);

		// Load the orm config file
		if ($orm_file = Arr::get($this->get('config'), 'ORM') !== NULL)
		{
			$this->set('orm_config', $orm_file);
		}

		// Run validator setup
		$this->_setup_validation();

		// Load the options
		$this->_load_options($options);
	}

	/**
	 * Adds a field to a form
	 *
	 * @access public
	 * @param mixed $alias
	 * @param mixed $driver. (default: NULL)
	 * @param mixed $value. (default: NULL)
	 * @param mixed array $options. (default: NULL)
	 * @return object
	 */
	public function add($alias, $driver = NULL, $value = NULL, array $options = NULL)
	{
		// If Formo instnace was passed
		if ($alias instanceof Formo_Form)
			return $this->_add_object($alias);

		if ($driver instanceof Formo_Form)
			return $this->_add_object($driver->alias($alias));

		if ($value instanceof Formo_Form)
			return $this->_add_object($value->set('driver', $driver)->alias($alias));

		$orig_options = $options;
		$options = func_get_args();
		$options = Formo::args(__CLASS__, __FUNCTION__, $options);


		// If a driver is named but not an alias, make the driver text and the alias the driver
		if (empty($options['driver']))
		{
			$options['driver'] = ($driver = Formo::config($this, 'default_driver'))
				? $driver
				: 'input';
		}

		// Create the new field
		$field = Formo::field($options);

		$this->append($field);

		return $this;
	}

	/**
	 * For adding select, checkboxes, radios, etc
	 *
	 * @access public
	 * @param mixed $alias
	 * @param mixed $driver
	 * @param mixed $options
	 * @param mixed $value. (default: NULL)
	 * @param mixed array $settings. (default: NULL)
	 * @return object
	 */
	public function add_group($alias, $driver, $options, $value = NULL, array $settings = NULL)
	{
		$settings['alias'] = $alias;
		$settings['driver'] = $driver;
		$settings['options'] = $options;
		$settings['value'] = $value;

		return $this->add($settings);
	}

	/**
	 * Add a subform to the form
	 *
	 * @access protected
	 * @param mixed Formo $subform
	 * @return object
	 */
	protected function _add_object(Formo_Container $subform)
	{
		($subform instanceof Formo_Form AND $subform->bind('_settings', 'input', $this->_settings['input']));

		$this->append($subform);

		return $this;
	}
	
	public function values(array $input)
	{
		foreach ($input as $name => $value)
		{
			if ($field = $this->get_field($name))
			{
				$field->val($value);
			}
		}
		
		return $this;
	}

	/**
	 * Load data, works automatcially with with post
	 *
	 * @access public
	 * @param mixed array $input. (default: NULL)
	 * @return void
	 */
	public function load(array $input = NULL)
	{
		// Set input to $_POST if it's not explicitly definied
		($input === NULL AND $input = $_POST);

		if ($this->sent($input) === FALSE)
			// Stop if input doesn't match the form's fields
			return $this;

		foreach ($this->fields() as $field)
		{
			if ($field->get('editable') === FALSE)
				// Don't ever adjust values for not editable fields
				continue;

			// post keys never have spaces
			$input_key = str_replace(' ', '_', $field->alias());

			if ($field instanceof Formo_Form)
			{
				// Recursively load values
				$field->load($input);
				continue;
			}

			// Fetch the namespace for this form
			$namespaced_input = (Formo::config($this, 'namespaces') === TRUE)
				? Arr::get($input, $this->alias(), array())
				: $input;

			if (isset($namespaced_input[$input_key]))
			{
				// Set the value
				$field->driver()->load($namespaced_input[$input_key]);
			}
			elseif ($field->driver()->file === TRUE AND isset($_FILES[$input_key]))
			{
				// Load the $_FILES params as the value
				$field->driver()->load($_FILES[$input_key]);
			}
			elseif ($field->driver()->empty_input === TRUE)
			{
				// If the an empty input is allowed, pass an empty value
				$field->driver()->load(array());
			}
		}

		$this->set('input', $input);

		return $this;
	}

	/**
	 * Renders the form according to the config file's "render" setting
	 *
	 * @access public
	 * @return void
	 */
	public function __toString()
	{
		// Render as the default render type
		try
		{
			return $this->render(Formo::config($this, 'render_type'));
		}
		catch (Exception $e)
		{
			return "Error rendering form:".$e->getMessage();
		}
	}

}
