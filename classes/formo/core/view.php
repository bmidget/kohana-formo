<?php defined('SYSPATH') or die('No direct script access.');

abstract class Formo_Core_View extends View {

	/**
	 * The formo_container object
	 *
	 * @var mixed
	 * @access protected
	 */
	public $_container;


	/**
	 * Decorator-specific variables
	 *
	 * (default value: array())
	 *
	 * @var array
	 * @access protected
	 */
	protected $_vars = array();

	public function set_var($var, $value)
	{
		// Look for class inside 'attr' specifically
		if ($var == 'attr' AND is_array($value) AND isset($value['class']))
		{
			$this->add_class($value['class']);
			unset($value['class']);
		}

		if (func_num_args() === 3)
		{
			$this->_vars[$var][$value] = func_get_arg(2);
		}
		else
		{
			$this->_vars[$var] = $value;
		}

		return $this;
	}

	public function add_var($var, $key, $value)
	{
		$this->_vars[$var][$key] = $value;
	}

	public function __call($method, $args)
	{
		if (is_callable(array($this->_container, $method)))
			return call_user_func_array(array($this->_container, $method), $args);
	}

	/**
	 * Retrieve the label text
	 *
	 * @access public
	 * @return void
	 */
	public function label()
	{
		$label = ($label = $this->_container->get('label'))
			? $label
			: $this->_container->alias();

		// Translate if needed
		return (Kohana::config('formo')->translate === TRUE)
			? __($label)
			: $label;
	}

	public function pre_render()
	{
		if (method_exists($this->_container->driver(), 'html') === FALSE)
			return;

		// Run the html() setup method if it's defined in the driver
		$this->_container->driver()->html();
	}

	public function append()
	{
		if (method_exists($this->_container->driver(), 'html_append'))
		{
			$this->_container->driver()->html_append();
		}
	}

	/**
	 * Inject $field into new scope
	 *
	 * @access public
	 * @param mixed $file. (default: NULL)
	 * @return void
	 */
	public function render($file = NULL)
	{
		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$benchmark = Profiler::start('Formo', __FUNCTION__);
		}

		$this->set('field', $this);

		$return = parent::render($file);

		if (isset($benchmark))
		{
			// Stop benchmarking
			Profiler::stop($benchmark);
		}

		return $return;
	}

}
