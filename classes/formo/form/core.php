<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Form_Core extends Formo_Validator {

	protected $_settings = array
	(
		// The config array
		'config'				=> array(),
		// The orm config array
		'orm_config'			=> array(),
		// Driver name for handling validation/rendering
		'driver'				=> 'form',
		// Driver instance that handles orm comm
		'orm_driver_instance'	=> NULL,
		// A model associated with this form
		'model'					=> NULL,
		// Whether the form was sent
		'sent'					=> '__NOTSET',
		// The input object ($_GET/$_POST/etc)
		'input'					=> array(),
		// If the object should be namespaces
		'namespace'				=> FALSE,
		// The view path prefix
		'view_prefix'			=> 'formo/',
		// Whether the form is 'post' or 'get'
		'type'					=> 'post',
		// Whether the field should render
		'render'				=> TRUE,
		// Whether the field is editable
		'editable'				=> TRUE,
		// A custom message file
		'message_file'			=> NULL,
	);
	
	public function __construct($alias = NULL, $driver = NULL)
	{
		// Setup options array
		$options = func_get_args();
		$options = Formo_Container::args(__CLASS__, __FUNCTION__, $options);
				
		// Load the config file
		$this->set('config', Kohana::config('formo'));
		
		// Set the default alias and driver if necessary
		(empty($options['alias']) AND $options['alias'] = $this->get('config')->form_alias);
		(empty($options['driver']) AND $options['driver'] = $this->get('config')->form_driver);
				
		// Load the orm config file
		if ($orm_file = Arr::get($this->get('config'), 'ORM') !== NULL)
		{
			$this->set('orm_config', $orm_file);
		}

		// Load the options
		$this->load_options($options);
	}
	
	// Add a field to the form
	public function add($alias, $driver = NULL, $value = NULL, array $options = NULL)
	{
		// If Formo instnace was passed
		if ($alias instanceof Formo)
			return $this->add_object($alias);
			
		if ($driver instanceof Formo)
			return $this->add_object($driver->alias($alias));
			
		if ($value instanceof Formo)
			return $this->add_object($value->set('driver', $driver)->alias($alias));
					
		$orig_options = $options;
		$options = func_get_args();
		$options = self::args(__CLASS__, __FUNCTION__, $options);

		// If a driver is named but not an alias, make the driver text and the alias the driver
		if (empty($options['driver']))
		{
			$options['driver'] = Arr::get($this->config, 'default_driver', 'text');
		}
		
		// Allow loading rules, callbacks, filters upon adding a field
		$validate_options = array('rules', 'triggers', 'filters');
		// Create the array
		$validate_settings = array();
				
		foreach ($validate_options as $option)
		{
			if ( ! empty($options[$option]))
			{
				$validate_settings[$option] = $options[$option];
				unset($options[$option]);
			}
		}
								
		// Create the new field
		$field = Formo::field($options);
		
		$this->append($field);

		// Add the validation rules
		foreach ($validate_settings as $method => $array)
		{
			foreach ($array as $callback => $opts)
			{
				if ($opts instanceof Formo_Validator_Item)
				{
					// The rules method will suffice for all Formo_Validator_Item objects
					$field->rules(NULL, $opts);
					continue;
				}
				
				$args = array(NULL, $callback, $opts);
				call_user_func_array(array($field, $method), $args);
			}
		}		
		
		return $this;
	}
	
	// For adding select, checkboxes, radios, etc
	public function add_group($alias, $driver, $options, $value = NULL, array $settings = NULL)
	{		
		$settings['alias'] = $alias;
		$settings['driver'] = $driver;
		$settings['options'] = $options;
		$settings['value'] = $value;
						
		return $this->add($settings);
	}
	
	// Add a subform to the form
	protected function add_object(Formo $subform)
	{
		($subform instanceof Formo_Form AND $subform->bind('_settings', 'input', $this->_settings['input']));
		
		$this->append($subform);
		
		return $this;
	}
			
	// Load data, auto-works with get/post
	public function load(array $input = NULL)
	{
		($input === NULL AND $input = Arr::get($this->config, 'type', 'post'));
		
		if (is_string($input))
		{
			switch ($input)
			{
				case 'get':
					$input = $_GET;
					break;
				case 'post':
				default:
					$input = $_POST;
			}
		}
		
		foreach ($input as $name => $value)
		{
			if ($field = $this->find($name))
			{
				$field->driver->load($value);
				continue;
			}
			
			if ($field = $this->find(str_replace('_', ' ', $name)))
			{
				$field->driver->load($value);
			}
		}
		
		$this->set('input', $input);
		
		return $this;
	}

	// Render
	public function render($type, $view_prefix = FALSE)
	{
		if ($this->get('render') === FALSE)
			return;
			
		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Formo', __FUNCTION__);
		}
						
		$view = $this->driver->view($type);
		
		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Formo', __FUNCTION__);
		}
		
		return $view;
				
	}
}