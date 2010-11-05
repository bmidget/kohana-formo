<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract Formo_Driver_Core class.
 *
 * @package  Formo
 */
abstract class Formo_Driver_Core {

	/**
	 * Decorator object
	 *
	 * @var object
	 * @access protected
	 */
	protected $decorator;

	/**
	 * Field or form object
	 *
	 * @var object
	 * @access protected
	 */
	protected $field;

	/**
	 * The name of the variable passed into the view file
	 *
	 * (default value: 'field')
	 *
	 * @var string
	 * @access public
	 */
	public $alias = 'field';

	/**
	 * Indicates whether this kind of field potentially does not have a post value
	 *
	 * (default value: FALSE)
	 *
	 * @var bool
	 * @access public
	 */
	public $empty_input = FALSE;
	
	/**
	 * Indicates whether this kind of field must use the $_FILES array
	 * 
	 * (default value: FALSE)
	 * 
	 * @var bool
	 * @access public
	 */
	public $file = FALSE;
	
	/**
	 * General factory method
	 *
	 * @access public
	 * @static
	 * @param mixed Formo_Container $field
	 * @return void
	 */
	public static function factory(Formo_Container $field)
	{
		return new Formo_Driver($field);
	}

	/**
	 * Populate the field and decorator objects
	 *
	 * @access public
	 * @param mixed Formo_Container $field
	 * @return void
	 */
	public function __construct(Formo_Container $field)
	{
		// Load the field instance
		$this->field = $field;

		// Determine the original decorator type
		$type = ($type = $this->field->get('type'))
			? $type
			// Fall back on the default form type
			: Kohana::config('formo')->type;

		$this->decorator($type);
	}

	/**
	 * Run methods on the decorator object
	 *
	 * @access public
	 * @param mixed $method
	 * @param mixed $args
	 * @return void
	 */
	public function __call($func, $args)
	{
		// At this point we need to run the method through the decorator
		$method = new ReflectionMethod($this->decorator, $func);
		return $method->invokeArgs($this->decorator, $args);
	}

	/**
	 * Create the decorator instance
	 *
	 * @access public
	 * @param mixed $type
	 * @return void
	 */
	public function decorator($type)
	{
		// Make the class name
		$class = 'Formo_Decorator_'.ucfirst($type);

		// Create the actual decorator object
		$this->decorator = new $class($this->field, $this);
	}
	
	/**
	 * Append event takes place after field has been appended
	 * to its parent
	 * 
	 * @access public
	 * @return void
	 */
	public function append()
	{
		$this->decorator->append();
	}

	public function set($variable, $value)
	{
		// If the variable is inside the decorator, set that
		if (isset($this->decorator->$variable))
			$this->decorator->set($variable, $value);

		// Otherwise just set the field value
		$this->field->$variable = $value;

		return $this->field;
	}

	public function get($variable, $default)
	{
		if (isset($this->decorator->$variable))
			// If the variable is inside the decorator, return that
			return $this->decorator->get($variable);

		// Otherwise return the field value if it's set, or the default value if it's not
		return (isset($this->field->$variable))
			? $this->field->$variable
			: $default;
	}

	/**
	 * Called at field's construct. Gives driver chance to do stuff
	 *
	 * @access public
	 * @return void
	 */
	public function post_construct(){}

	/**
	 * Called just before running validate()
	 *
	 * @access public
	 * @return void
	 */
	public function pre_validate(){}

	/**
	 * Called just after running validate()
	 *
	 * @access public
	 * @return void
	 */
	public function post_validate(){}

	/**
	 * Run when loading input data
	 *
	 * @access public
	 * @param mixed $value
	 * @return void
	 */
	public function load($value)
	{
		// Just set the value to what was passed
		$this->val($value);
	}

	/**
	 * Retrive a field's value
	 *
	 * @access protected
	 * @return void
	 */
	protected function get_val()
	{
		$new_value = $this->field->get('new_value');

		return (Formo::is_set($new_value) === TRUE)
			? $new_value
			: $this->field->get('value');
	}

	/**
	 * Set the field's value
	 *
	 * @access protected
	 * @param mixed $value
	 * @return void
	 */
	protected function set_val($value)
	{
		$this->field->set('new_value', $value);
	}

	/**
	 * Interface for setting, retrieving field's value
	 *
	 * @access public
	 * @param mixed $value. (default: NULL)
	 * @return void
	 */
	public function val($value = NULL)
	{
		if (func_num_args() === 0)
			return $this->get_val();

		// Run pre_filters on the value
		$this->run_pre_filters($value);

		// Set the value
		$this->set_val($value);

		// Run ORM methods
		$this->set_orm_fields($value);

		return $this;
	}
	
	/**
	 * Return the namespaced name
	 * 
	 * @access public
	 * @return void
	 */
	public function name()
	{
		if ( ! $parent = $this->field->parent())
			// If there isn't a parent, don't namespace the name
			return $this->field->alias();

		return $parent->alias().'['.$this->field->alias().']';
	}

	/**
	 * Pre-filter field value
	 *
	 * @access protected
	 * @param mixed & $value
	 * @return void
	 */
	protected function run_pre_filters( & $value)
	{
		foreach ($this->field->get_filter('pre') as $filter)
		{
			// Resolve pseudo args
			$this->field->pseudo_args($filter->args, array(':value' => $value));

			// Run the filters
			$value = $filter->execute();
		}
	}

	/**
	 * Make ORM field values match Formo field values
	 *
	 * @access protected
	 * @param mixed $value
	 * @return void
	 */
	protected function set_orm_fields($value)
	{
		if ($orm = $this->field->model(TRUE))
		{
			$orm->set_field($this->field, $value);
		}
	}

	/**
	 * Make every option an array of options
	 *
	 * @access public
	 * @param mixed $options
	 * @return void
	 */
	public function set_options($options)
	{
		// Create the new array
		$array = array();
		foreach ($options as $alias => $value)
		{
			$array[$alias] = ( ! is_array($value))
				// Make the value part of an array
				? array('value' => $value)
				: $value;
		}

		return $array;
	}

	/**
	 * Run a method through the orm driver
	 *
	 * @access public
	 * @param mixed $method
	 * @return void
	 */
	public function orm($method)
	{
		$args = array_slice(func_get_args(), 1);
		return call_user_func_array(array($this->field->orm_driver(), $method), $args);
	}

	/**
	 * Runs just prior to rendering a form/field
	 *
	 * @access public
	 * @return void
	 */
	public function pre_render()
	{
		if (isset($this->field->orm))
		{
			$this->field->orm_driver()->pre_render();
		}

		$this->decorator->pre_render();

		return $this->field;
	}

	/**
	 * Render the field
	 *
	 * @access public
	 * @return void
	 */
	public function render()
	{
		// First run and do any pre_render stuff
		$this->pre_render();

		return $this->decorator->render();
	}

	/**
	 * Run when open is called on the decorator
	 *
	 * @access public
	 * @return void
	 */
	public function open()
	{
		// First run and do any pre_render stuff
		$this->pre_render();

		return $this->decorator->open();
	}

	/**
	 * Generate a view from a field or form object
	 *
	 * @access public
	 * @return void
	 */
	public function generate($view_file = FALSE, $view_prefix = NULL)
	{
		if ($this->field->get('render', NULL) === FALSE)
			return;

		// First run and do any pre_render stuff
		$this->pre_render();

		// Prefix acts as a templating system for views
		$prefix = $this->get_view_prefix($view_prefix);

		// Determine the view file
		$view = $this->get_view($view_file);

		// Skip the prefix if view prefix is FALSE
		$skip_prefix = $view_prefix === FALSE;

		return $this->decorator->generate($view, $prefix);
	}

	protected function get_view($view = FALSE)
	{
		// The defined view file takes precendence over the default one
		// and the parameter passed into generate() takes first precedence
		return ($view !== FALSE)
			// Always choose the passed view if it exists
			? $view
			// Next look for the field-level view
			: ($view = $this->field->get('view'))
				? $view
				: $this->view;
	}

	protected function get_view_prefix($prefix = NULL)
	{
		// If the specified prefix is FALSE, no prefix
		if ($prefix === FALSE)
			return FALSE;

		// If the prefix was specified, use it
		if ($prefix !== NULL)
			return rtrim($prefix, '/');

		// Find the appropriate view_prefix
		$prefix = $this->field->get('view_prefix', NULL);

		if ($prefix === NULL)
		{
			$prefix = ($parent = $this->field->parent())
				? $parent->get('view_prefix', NULL)
				: NULL;
		}

		// If prefix is still set to NULL and config file has one defined, use the config prefix
		if ($prefix === NULL AND $_prefix = Kohana::config('formo')->view_prefix)
		{
			$prefix = $_prefix;
		}

		return $prefix;
	}

	public function not_empty()
	{
		$new_value = $this->field->get('new_value');

		if (Formo::is_set($new_value) === FALSE AND ! $this->field->get('value'))
			return FALSE;

		return (bool) $new_value;
	}

}
