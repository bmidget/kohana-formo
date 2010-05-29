<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core extends Validator {
	
	protected $_settings = array
	(
		// The config array
		'config'		=> array(),
		// The orm config array
		'orm_config'	=> array(),
		// Driver for handling validation/rendering
		'driver'		=> 'form',
		// A model associated with this form
		'model'			=> NULL,
		// Whether the form was sent
		'sent'			=> FALSE,
		// The input object ($_GET/$_POST/etc)
		'input'			=> NULL,
		// If the object should be namespaces
		'namespace'		=> FALSE,
		// The view path prefix
		'view_prefix'	=> 'formo/',
		// Whether the form is 'post' or 'get'
		'type'			=> 'post',
		// html, json, xml, etc
		'render_type'	=> NULL,
	);
	
	public static function factory($alias = 'form', $driver = 'form')
	{
		return new Formo($alias, $driver);
	}
	
	public function __construct($alias = 'form', $driver = 'form')
	{
		// Setup options array
		$options = func_get_args();
		$options = Container::args(__CLASS__, __FUNCTION__, $options);
				
		// Load the config file
		$this->set('config', Kohana::config('formo'));
		
		// Load the orm config file
		if ($orm_file = Arr::get($this->get('config'), 'ORM') !== NULL)
		{
			$this->set('orm_config', $orm_file);
		}

		// Load the options
		$this->load_options($options);
	}
			
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
		$validate_options = array('rule', 'callback', 'filter');
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
		$field = Ffield::factory($options);
		
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
				$field->driver->val($value);
				continue;
			}
			
			if ($field = $this->find(str_replace('_', ' ', $name)))
			{
				$field->driver->val($value);
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

		$class = new ReflectionMethod('Formo_Orm_Jelly', $method);

		$args = array($model, $this);

		(func_num_args() === 3 AND $args[] = $data);

		$return_val = $class->invokeArgs($model, $args);

		($return_val !== NULL AND $model = $return_val);		
		
		return $this;
	}
	
	public function render($type, $view_prefix = FALSE)
	{
		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Formo', __FUNCTION__);
		}

		$this->set('render_type', $type);

		$class = 'Formo_Render_'.$type;
		
		$view_prefix = $view_prefix ? $view_prefix : $this->get('view_prefix');
		$this->set('view_prefix', $view_prefix);
		
		$this->driver->pre_render();

		$render_obj = new $class($this);
		$render_obj->defaults('field', $this->fields());
		
		$method = 'pre_render_'.$type;
		$this->driver->$method($render_obj);
		
		$view = View::factory($this->driver->view())
			->bind('form', $render_obj);

		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Formo', __FUNCTION__);
		}
		
		return $view;
				
	}
	
}