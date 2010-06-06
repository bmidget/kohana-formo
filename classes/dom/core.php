<?php defined('SYSPATH') or die('No direct script access.');

class DOM_Core extends Formo_Render {

	// HTML tag
	public $tag;
	// Array of attributes
	public $attr = array
	(
		'class' => array(),
	);
	// Styles
	public $css = array();
	// Text inside thag
	public $text;
	
	// Label string
	public $label;
	// Quote to use
	public $quote = '"';
		
	protected $_variables = array
	(
		// Cached html objects for quickly accessing later
		'generated_objects'	=> array(),
		// Self-closing html tags
		'singles'			=> array('br', 'hr', 'input'),
		// Data for being passed into view
		'data'				=> array(),
	);

	public static function factory($object)
	{
		return new DOM($object);
	}
	
	public function __construct($object)
	{		
		foreach ($object as $item => $value)
		{			
			$this->$item = $value;
		}
		
		// Copy settings and defaults and error messages
		if ($object instanceof Container)
		{
			$this->_settings = $object->get('_settings');
			$this->_defaults = $object->get('_defaults');
			$this->_errors = $object->get('_errors');
		}
	}
	
	// Get or set an html attribute
	public function attr($attr, $value = NULL)
	{
		if (is_array($attr))
		{
			foreach ($attr as $_attr => $_value)
			{
				$this->attr($_attr, $_value);
			}
			
			return $this;
		}
		
		if (func_num_args() < 2)
			return (isset($this->attr[$attr])) ? $this->attr[$attr] : NULL;
			
		if ( ! $value)
		{
			unset($this->attr[$attr]);
		}
		else
		{
			$this->attr[$attr] = (array_key_exists($attr, $this->attr) AND is_array($this->attr[$attr]))
				? (array) $value
				: $value;
		}
		
		return $this;
	}
	
	// Set or get a "style" attribute
	public function css($style, $value = NULL)
	{
		if (is_array($style))
		{
			foreach ($style as $_style => $_value)
			{
				$this->css($_style, $_value);
			}
			
			return $this;
		}
		
		if (func_num_args() < 2)
			return (isset($this->css[$style])) ? $this->css[$style] : NULL;
			
		if ( ! $value)
		{
			unset($this->css[$style]);
		}
		else
		{
			$this->css[$style] = $value;
		}
		
		return $this;
	}

	// Add class to element
	public function add_class($class)
	{
		if (is_array($class))
		{
			foreach ($class as $_class)
			{
				$this->add_class($_class);
			}
			
			return $this;
		}
		
		$this->attr['class'][] = $class;
			
		return $this;
	}
		
	// Remove a class if it exists
	public function remove_class($class)
	{
		if ($key = array_search($class, $this->attr['class']) !== FALSE)
		{
			unset($this->attr['class'][$key]);
		}
					
		return $this;
	}
	
	// Return the correct label
	public function label(array $options = NULL)
	{
		return $this->label ? $this->label : $this->alias();
	}

	// Set or return the text
	public function text()
	{
		$vals = func_get_args();
		
		// Return the text if nothing was entered
		if ( ! $vals)
			return $this->text;
			
		if (is_array($vals[0]))
		{
			foreach ($vals[0] as $key => $val)
			{
				if (is_string($key))
				{
					$this->text($key, $val);
				}
				else
				{
					$this->text($val);
				}
			}
			
			return $this;
		}

		// If two args were given perform special functions
		if (count($vals) == 2)
		{
			switch($vals[0])
			{
			case '.=':
				$this->text .= $vals[1];
				break;
			case '=.':
				$this->text = $vals[1].$this->text;
				break;
			case 'callback':
				$this->text = call_user_func($vals[1], $this->text);
				break;
			}
	
			return $this;
		}

		// If one arg was given set text to that value
		(count($vals) == 1 AND $this->text = $vals[0]);

		return $this;
	}

	// Turn attributes into a string (tag="val" tag2="val2")
	protected function attr_str()
	{
		$str = '';
		
		foreach ($this->attr as $attr => $value)
		{
			$value = (is_array($value)) ? implode(' ', $value) : $value;
			$str.= " $attr = $this->quote$value$this->quote";
		}
		
		// Then attach styles
		if ($this->css)
		{
			$str.= ' style='.$this->quote;
			foreach ($this->css as $style => $value)
			{
				$str.= $style.':'.$value.';';
			}
			$str.= $this->quote;
		}
		
		return $str;
	}
	
	// Allows just the opening tag to be returned
	public function open($append_str = NULL)
	{
		$singletag = in_array($this->tag, $this->_variables['singles']);

		$str = '<'.$this->tag.$this->attr_str();
		$str.= ( ! $singletag) ? '>'."\n" : '';
		
		return $str;
	}
	
	// Allows just the closing tag to be returned
	public function close()
	{
		$singletag = in_array($this->tag, $this->_variables['singles']);
		
		$str = ( ! $singletag) ? '<' : '';
		$str.= '/';
		$str.= ( ! $singletag) ? $this->tag : '';
		$str.= '>'."\n";
		
		return $str;
	}
	
	// Convenience method for finding all fields
	public function fields()
	{
		return $this->get('fields');
	}
	
	// Render fields as html
	public function __toString()
	{
		return $this->render();
	}
	
	// Return rendered element
	public function render()
	{	
		$singletag = in_array($this->tag, $this->_variables['singles']);
		
		$str = $this->open();
			
		if ( ! $singletag)
		{
			$str.= $this->text;
			foreach ($this->fields() as $field)
			{
				$method = new ReflectionMethod($field, 'render');
				$args = func_get_args();
				$str.= $method->invokeArgs($field, $args);
			}
		}
		
		return $str.= $this->close();
	}
	
}