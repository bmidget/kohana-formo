<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Render_html_Core extends Formo_Render {
	
	// HTML tag
	public $tag;
	// Array of attributes
	public $attr = array();
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
		return new Formo_Render_html($object);
	}
	
	public function __construct($object)
	{		
		foreach ($object as $item => $value)
		{			
			$this->$item = $value;
		}
		
		// Copy settings and defaults
		if ($object instanceof Container)
		{
			$this->_settings = $object->get('_settings');
			$this->_defaults = $object->get('_defaults');
		}
		
	}
			
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
			$this->attr[$attr] = $value;
		}
		
		return $this;
	}
	
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
		
		$this->attr['class'] = ( ! empty($this->attr['class']))
			? $this->attr['class'].' '.$class
			: $class;
			
		return $this;
	}
		
	// Remove a class if it exists
	public function remove_class($class)
	{
		(preg_match('/ /', $class) AND $class = explode(' ', $class));
		
		if (is_array($class))
		{
			foreach ($class as $_class)
			{
				$this->remove_class(trim($_class));
			}
			
			return $this;
		}
				
		$search = array
		(
			'/^'.$class.' /',
			'/ '.$class.'( )/',
			'/ '.$class.'$/'
		);
		
		$this->attr['class'] = preg_replace($search,'$1',$this->attr['class']);
		
		if ( ! $this->attr['class'])
		{
			unset($this->attr['class']);
		}
		else
		{
			$this->attr['class'] = trim($this->attr['class']);
		}
		
		return $this;
	}
	
	// Generate a label object on the fly
	public function label(array $options = NULL)
	{
		if (func_num_args() === 0)
		{	
			// Convert _label to object
			$options = is_array($this->label)
				? $field->label
				: array
				  (
				  	'text' => $this->label ? $this->label : $this->alias(),
				  	'tag'	=> 'label',
				  	'attr'	=> array('for' => $this->attr('id')),
				  );
		}
		
		// Return the generated object
		return $this->html($options);
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
			$str.= ' '.$attr.'='.$this->quote.$value.$this->quote;
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

	// Generate an html object on the fly using this object's parameters
	public function html($tag, $var = NULL, array $options = NULL)
	{
		$options = func_get_args();
		$orig_options = $options;
		$options = Container::args(__CLASS__, __FUNCTION__, $options);
		
		// This is what the stored name will be
		$stored_name = isset($options['var']) ? $options['var'] : $options['tag'];
		
		// If it's already been made, return it
		if (isset($this->_variables['generated_objects'][$stored_name]))
			return $this->_variables['generated_objects'][$stored_name];
			
		// Create the options array to use
		$_options = array();
		
		if (isset($options['var']) AND isset($this->$var))
		{
			$_options = (is_array($this->$var))
				? $this->{$options['param']}
				: array('text' => $this->$var);
		}
				
		foreach ($options as $option => $value)
		{
			$_options[$option] = $value;
		}
				
		// Cache the object and return it
		return $this->_variables['generated_objects'][$var] = self::factory($_options);
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
	
	// Convenience method
	public function fields()
	{
		return $this->defaults('fields');
	}
	
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