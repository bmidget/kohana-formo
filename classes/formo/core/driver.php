<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Abstract Formo_Driver_Core class.
 *
 * @package   Formo
 * @category  Drivers
 */
abstract class Formo_Core_Driver {

	/**
	 * View object
	 *
	 * @var object
	 * @access protected
	 */
	protected $_view;

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
	 * Populate the field and view objects
	 *
	 * @access public
	 * @param mixed Formo_Container $field
	 * @return void
	 */
	public function __construct(Formo_Container $field)
	{
		// Load the field instance
		$this->field = $field;
		
		// Determine the original view type
		$kind = ($kind = $this->field->get('kind'))
			? $kind
			// Fall back on the default form type
			: Kohana::config('formo')->kind;

		$this->make_view($kind);
	}

	/**
	 * Create the view instance
	 *
	 * @access public
	 * @param mixed $type
	 * @return void
	 */
	public function make_view($type)
	{
		// Make the class name
		$class = 'Formo_View_'.$type;

		// Create the actual decorator object
		$this->_view = new $class();
		$this->_view->_field  = $this->field;
	}
	
	public function view()
	{
		return $this->_view;
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
		$this->view()->append();
	}

	public function set($variable, $value)
	{
		// Just set the field value
		$this->field->$variable = $value;

		return $this->field;
	}

	public function get($variable, $default, $shallow_look = FALSE)
	{
		// Return the field value if it's set, or the default value if it's not
		return (isset($this->field->$variable))
			? $this->field->$variable
			: $default;
	}
	
	public function format_alias($alias)
	{
		return str_replace(' ', '_', $alias);
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
	public function pre_validate()
	{
		// Add not_empty rule for 'required'
		if ($this->field->get('required') === TRUE)
		{
			$val_field = ($this->field instanceof Formo_Form)
				? $this->field
				: $this->field->parent();
			
			$val_field->rule($this->field->alias(), 'not_empty');
		}
	}

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
		if ( ! Kohana::config('formo')->namespaces)
			return $this->field->alias();

		if ( ! $parent = $this->field->parent())
			// If there isn't a parent, don't namespace the name
			return $this->field->alias();

		return $parent->alias().'['.$this->field->alias().']';
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
	
	public function has_orm()
	{
		return empty($this->field->orm) === FALSE;
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

		$this->view()->pre_render();

		return $this->field;
	}

	/**
	 * Run when open is called on the view
	 *
	 * @access public
	 * @return void
	 */
	public function open()
	{
		// First run and do any pre_render stuff
		$this->pre_render();

		return $this->view()->open();
	}

	/**
	 * Generate a view from a field or form object
	 *
	 * @access public
	 * @return void
	 */
	public function render($view_file = FALSE, $view_prefix = NULL)
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

		$this->view()
			->set('open', View::factory("$prefix/_open_tag", array('view' => $this->view(), 'field' => $this->field)))
			->set('close', View::factory("$prefix/_close_tag", array('view' => $this->view(), 'field' => $this->field)))
			->set('message', View::factory("$prefix/_message", array('view' => $this->view(), 'field' => $this->field)))
			->set('label', View::factory("$prefix/_label", array('view' => $this->view(), 'field' => $this->field)));

		return $this->view()->render("$prefix/$view");
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

		return $new_value !== Formo::NOTSET;
	}

}
