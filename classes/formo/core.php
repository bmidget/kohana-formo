<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core extends Formo_Validator {
	
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
		'sent'					=> FALSE,
		// The input object ($_GET/$_POST/etc)
		'input'					=> NULL,
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
	);
	
	public static function factory($alias = NULL, $driver = NULL)
	{
		return new Formo($alias, $driver);
	}
	
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
	
	public function __get($value)
	{
		if ($value == 'orm')
		{
			// If the driver's already been created, retrieve it
			if ($instance = $this->get('orm_driver_instance'))
				return $instance;
				
			$instance = Formo_ORM_Factory::factory($this);
			$this->set('orm_driver_instance', $instance);
			
			return $instance;
		}
		
		return parent::__get($value);
	}
	
	// Add a field to the form
	public function add($alias, $driver = NULL, $value = NULL, array $options = NULL)
	{
		// If Formo object was passed, add it as a subform
		if ($driver instanceof Formo)
			return $this->add_subform($driver);
			
		if ($alias instanceof Formo)
			return $this->add_subform($alias->alias($driver));
			
		if ($value instanceof Formo)
			return $this->add_subform($value->alias($alias)->set('driver', $driver));
		
		$orig_options = $options;
		$options = func_get_args();
		$options = self::args(__CLASS__, __FUNCTION__, $options);

		// If a driver is named but not an alias, make the driver text and the alias the driver
		if (empty($options['driver']))
		{
			$options['driver'] = Arr::get($this->config, 'default_driver', 'text');
		}
		
		// Allow loading rules, callbacks, filters upon adding a field
		$validate_options = array('rule', 'trigger', 'filter');
		// Create the array
		$validate_settings = array();
				
		foreach ($validate_options as $option)
		{
			$option_name = Inflector::plural($option);
			if ( ! empty($options[$option_name]))
			{
				$validate_settings[$option] = $options[$option_name];
				unset($options[$option_name]);
			}
		}
								
		// Create the new field
		$field = Formo_Field::factory($options);
		
		$this->append($field);
		
		// Add the validation rules
		foreach ($validate_settings as $method => $array)
		{
			foreach ($array as $callback => $opts)
			{
				$args = array(NULL, $callback, $opts);
				call_user_func_array(array($field, $method), $args);
			}
		}		
		
		return $this;
	}
	
	// Add a subform to the form
	protected function add_subform(Formo $subform)
	{
		$this->append($subform);
		
		return $this;
	}
		
	// Determine whether data was sent
	public function sent()
	{
		if ($val = Arr::get($this->get('input'), '_formo') AND $val == $this->alias())
		{
			$this->set('sent', TRUE);
		}

		return $this->get('sent');
	}

	// Return all fields in order
	public function fields($field = NULL)
	{
		if (func_num_args() === 1)
			return $this->field($field);
			
		$unordered = array();
		$ordered = array();
		
		foreach ($this->defaults('fields') as $field)
		{
			$alias = $field->alias();
			$ordered[$alias] = $field;
		}
				
		return $ordered;
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
		
		$this->input = $input;
		
		$this->sent();
		
		return $this;
	}
	
	// Call ORM drivers specifically. This requires ORM settings in the config file
	public function orm($method, & $model, $data = NULL)
	{
		$this->set('model', $model);
		$this->orm->$method($model, $data);
		
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
						
		$this->driver->pre_render($type);
		$view = $this->driver->view();
		
		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Formo', __FUNCTION__);
		}
		
		return $view;
				
	}
	
}