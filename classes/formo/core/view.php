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

	/**
	 * Retrieve the label text
	 *
	 * @access public
	 * @return void
	 */
	public function label($utf8 = FALSE)
	{
		$label = $this->_field->get('label');
		
		if ( ! $label)
		{
			if (Formo::config($this->_field, 'use_messages') === TRUE)
			{
				$label = $this->_field->message_label();
			}
			else
			{
				$label = $this->_field->alias();
			}
			
			$label = $this->translate($label);
		}
		
		return $label;
	}
	
	public function translate($str)
	{
		$new_str = $str;

		if (Formo::config($this->_field, 'use_messages') === TRUE)
		{
			$msg_file = Formo::config($this->_field, 'message_file');
			$new_str = Kohana::message($msg_file, $str, $str);
		}
		
		if (Formo::config($this->_field, 'translate') === TRUE)
		{
			$new_str = __($new_str);
		}
		
		return $new_str;
	}

	public function pre_render()
	{
		if (method_exists($this->_field->driver(), 'html') === FALSE)
			return;

		// Run the html() setup method if it's defined in the driver
		$this->_field->driver()->html();
	}

	public function append()
	{
		if (method_exists($this->_field->driver(), 'html_append'))
		{
			$this->_field->driver()->html_append();
		}
	}
	
	public static function factory($file = NULL, array $data = NULL)
	{
		return new Formo_View($file, $data);
	}
	
	protected function _capture($kohana_view_filename, array $kohana_view_data)
	{
		// Import the view variables to local namespace
		extract($kohana_view_data, EXTR_SKIP);

		if (View::$_global_data)
		{
			// Import the global view variables to local namespace
			extract(View::$_global_data, EXTR_SKIP);
		}

		// Capture the view output
		ob_start();

		try
		{
			// Load the view within the current scope
			include $kohana_view_filename;
		}
		catch (Exception $e)
		{
			// Delete the output buffer
			ob_end_clean();

			// Re-throw the exception
			throw $e;
		}

		// Get the captured output and close the buffer
		return ob_get_clean();
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
		
		if ($file !== NULL)
		{
			$this->set_filename($file);
		}

		if (empty($this->_file))
		{
			throw new Kohana_View_Exception('You must set the file to use within your view before rendering');
		}

		// Combine local and global data and capture the output
		$return = $this->_capture($this->_file, $this->_data);

		if (isset($benchmark))
		{
			// Stop benchmarking
			Profiler::stop($benchmark);
		}

		return $return;
	}

	/**
	 * Access Formo field 'fields()`
	 * 
	 * @access public
	 * @return array
	 */
	public function fields()
	{
		return $this->_field->fields();
	}
	
	/**
	 * Access Formo field 'val()`
	 * 
	 * @access public
	 * @return mixed
	 */
	public function val()
	{
		return $this->_field->val();
	}
	
	
	/**
	 * Access Formo field 'get()`
	 * 
	 * @access public
	 * @return mixed
	 */
	public function get($variable, $default = FALSE, $shallow_look = FALSE)
	{
		return $this->_field->get($variable, $default, $shallow_look);
	}
	
	/**
	 * Retrieve error
	 * 
	 * @access public
	 * @return mixed
	 */
	public function error()
	{
		return $this->_field->error();
	}
	
	/**
	 * Retrieve driver
	 * 
	 * @access public
	 * @return string
	 */
	public function driver()
	{
		return $this->_field->get('driver');
	}
	
	/**
	 * Retrieve editable setting
	 * 
	 * @access public
	 * @return boolean
	 */
	public function editable()
	{
		return $this->_field->get('editable');
	}
	
	/**
	 * Retrieve alias
	 * 
	 * @access public
	 * @return string
	 */
	public function alias()
	{
		return $this->_field->alias();
	}

}
