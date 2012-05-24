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
	protected $_field;

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
		$this->_field = $field;

		// Determine the original view type
		$kind = Formo::config($this->_field, 'kind');

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
		$this->_view->_field  = $this->_field;
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
		$this->_view->append();
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
		if ($this->_field->get('required') === TRUE)
		{
			$val_field = ($this->_field instanceof Formo_Form)
				? $this->_field
				: $this->_field->parent();
			
			$val_field->rule($this->_field->alias(), 'not_empty');
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
	protected function _get_val()
	{
		$new_value = $this->_field->get('new_value');

		return (Formo::is_set($new_value) === TRUE)
			? $new_value
			: $this->_field->get('value');
	}

	/**
	 * Set the field's value
	 *
	 * @access protected
	 * @param mixed $value
	 * @return void
	 */
	protected function _set_val($value)
	{
		$this->_field->set('new_value', $value);
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
			return $this->_get_val();

		// Set the value
		$this->_set_val($value);

		// Run ORM methods
		$this->_set_orm_fields($value);

		return $this;
	}
	
	/**
	 * Whether the 'new_value' parameter is set
	 * 
	 * @access public
	 * @return boolean
	 */
	public function val_isset()
	{
		return Formo::is_set($this->_field->get('new_value'));
	}
	
	/**
	 * Determine if the field's value has changed
	 * 
	 * @access public
	 * @return boolean
	 */
	public function is_changed()
	{
		$value = $this->_field->get('value');
		$new_value = $this->_field->get('new_value');
		
		echo Debug::vars($value, $new_value);
		
		if ( ! $this->val_isset())
			return FALSE;

		return $value != $new_value;
	}
	
	/**
	 * Return the previous value
	 * 
	 * @access public
	 * @return mixed
	 */
	public function last_val()
	{
		return $this->_field->get('value');
	}
	
	/**
	 * Return the namespaced name
	 * 
	 * @access public
	 * @return void
	 */
	public function name()
	{
		if ( ! Formo::config($this->_field, 'namespaces'))
			return $this->_field->alias();

		if ( ! $parent = $this->_field->parent())
			// If there isn't a parent, don't namespace the name
			return $this->_field->alias();

		return $parent->alias().'['.$this->_field->alias().']';
	}

	/**
	 * Make ORM field values match Formo field values
	 *
	 * @access protected
	 * @param mixed $value
	 * @return void
	 */
	protected function _set_orm_fields($value)
	{
		if ($orm = $this->_field->model(TRUE))
		{
			$orm->set_field($this->_field, $value);
		}
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

		$orm = $this->_field->orm_driver();
		$method = new ReflectionMethod($orm, $method);
		return $method->invokeArgs($orm, (array) $args);
	}
	
	public function has_orm()
	{
		return ($this->_field instanceof Formo_Form)
			? empty($this->_field->orm) === FALSE
			: empty($this->_field->parent()->orm) === FALSE;
	}

	/**
	 * Runs just prior to rendering a form/field
	 *
	 * @access public
	 * @return void
	 */
	public function pre_render()
	{
		if (isset($this->_field->orm))
		{
			$this->_field->orm_driver()->pre_render();
		}

		$this->_view->pre_render();

		return $this->_field;
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

		return $this->_view->open();
	}
	
	/**
	 * Run the view's close method
	 *
	 * @access public
	 * @return void
	 */
	public function close()
	{
		return $this->_view->close();
	}

	/**
	 * Render a view from a field or form object
	 *
	 * @access public
	 * @return void
	 */
	public function render($view_file = FALSE, $view_prefix = NULL)
	{
		if ($this->_field->get('render', NULL) === FALSE)
			return;

		// First run and do any pre_render stuff
		$this->pre_render();

		// Prefix acts as a templating system for views
		$prefix = $this->_get_view_prefix($view_prefix);

		// Determine the view file
		$view = $this->_get_view($view_file);

		// Skip the prefix if view prefix is FALSE
		$skip_prefix = $view_prefix === FALSE;
		

		$this->_view
			->bind('open', $open)
			->bind('close', $close)
			->bind('message', $message)
			->bind('label', $label);
		
		$prefix = rtrim($prefix, '/');

		$open = Formo_View::factory("$prefix/_open_tag", array('view' => $this->_view));
		$open->_field = $this->_field;

		$close = Formo_View::factory("$prefix/_close_tag", array('view' => $this->_view));
		$close->_field = $this->_field;

		$message = Formo_View::factory("$prefix/_message", array('view' => $this->_view));
		$message->_field = $this->_field;

		$label = Formo_View::factory("$prefix/_label", array('view' => $this->_view));
		$label->_field = $this->_field;

		return $this->_view->render("$prefix/$view");
	}

	protected function _get_view($view = FALSE)
	{
		// The defined view file takes precendence over the default one
		// and the parameter passed into render() takes first precedence
		return ($view)
			// Always choose the passed view if it exists
			? $view
			// Next look for the field-level view
			: ($view = $this->_field->get('view'))
				? $view
				: $this->_view_file;
	}

	protected function _get_view_prefix($prefix = NULL)
	{
		// If the specified prefix is FALSE, no prefix
		if ($prefix === FALSE)
			return FALSE;

		// If the prefix was specified, use it
		if ($prefix !== NULL)
			return rtrim($prefix, '/');

		// Find the appropriate view_prefix
		$prefix = $this->_field->get('view_prefix', NULL);

		if ($prefix === NULL)
		{
			$prefix = ($parent = $this->_field->parent())
				? $parent->get('view_prefix', NULL)
				: NULL;
			
			// Set the view prefix so children can use it
			$this->_field->set('view_prefix', $prefix);
		}

		// If prefix is still set to NULL and config file has one defined, use the config prefix
		if ($prefix === NULL AND $_prefix = Formo::config($this->_field, 'view_prefix'))
		{
			$prefix = $_prefix;
		}

		return $prefix;
	}

	public function not_empty()
	{
		$new_value = $this->_field->get('new_value');

		if (Formo::is_set($new_value) === FALSE AND ! $this->_field->get('value'))
			return FALSE;

		return $new_value !== Formo::NOTSET;
	}

}
