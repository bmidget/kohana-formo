<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core_View_HTML extends Formo_View {

	/**
	 * The formo_container object
	 *
	 * @var mixed
	 * @access protected
	 */
	public $_field;

	/**
	 * List of HTML tags without closing tags
	 *
	 * (default value: array('br', 'hr', 'input'))
	 *
	 * @var array
	 * @access protected
	 */
	protected $_singles = array('br', 'hr', 'input');

	/**
	 * View-specific variables. All accessible thorugh __get($var)
	 *
	 * @var mixed
	 * @access protected
	 */
	protected $_vars = array
	(
		'tag'     => NULL,
		'attr'    => array(),
		'classes' => array(),
		'css'     => array(),
		'text'    => NULL,
		'data'    => array(),
	);


	/**
	 * Get or set an html attribute
	 *
	 * @access public
	 * @param mixed $attr
	 * @param mixed $value. (default: NULL)
	 * @return void
	 */
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

		if ($attr == 'class')
		{
			$args = func_get_args();
			return $this->classes($value, $args);
		}

		if (func_num_args() < 2)
			return Arr::get($this->_vars['attr'], $attr, NULL);

		if ($value === NULL)
		{
			// Remove the attribute tag only if the value is NULL
			// Empty strings remain empty attributes
			unset($this->_vars['attr'][$attr]);
		}
		else
		{
			$this->set_var('attr', $attr, $value);
		}

		return $this;
	}

	/**
	 * Set or retrieve class
	 *
	 * @access public
	 * @param mixed $class
	 * @param mixed $retrieve. (default: FALSE)
	 * @return void
	 */
	public function classes($class, $retrieve = FALSE)
	{
		if ($retrieve === TRUE)
			// The value of classes is a string
			return implode(' ', $this->_vars['classes']);

		if (in_array($class, $this->_vars['classes']))
			// No need re-add a class
			return $this;

		// Add the class
		$this->_vars['classes'][] = $class;

		return $this;
	}

	/**
	 * Set or get a "style" attribute
	 *
	 * @access public
	 * @param mixed $style
	 * @param mixed $value. (default: NULL)
	 * @return void
	 */
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
			return (isset($this->_css[$style])) ? $this->_css[$style] : NULL;

		if ( ! $value)
		{
			unset($this->_vars['css'][$style]);
		}
		else
		{
			$this->set_var('css', $style, $value);
		}

		return $this;
	}

	/**
	 * Add class to element
	 *
	 * @access public
	 * @param mixed $class
	 * @return void
	 */
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
		elseif (strpos($class, ' ') !== FALSE)
		{
			foreach (explode(' ', $class) as $_class)
			{
				$this->add_class($_class);
			}

			return $this;
		}

		return $this->classes($class);
	}

	/**
	 * Remove a class if it exists
	 *
	 * @access public
	 * @param mixed $class
	 * @return void
	 */
	public function remove_class($class)
	{
		$classes =& $this->_vars['classes'];
		if (($key = array_search($class, $classes)) !== FALSE)
		{
			unset($classes[$key]);
		}

		return $this;
	}

	protected function _make_id()
	{
		$id = $this->alias();

		if ( ! $parent = $this->_field->parent())
			// If there isn't a parent, don't namespace the name
			return $id;

		if ($parent->alias() == Formo::config($this->_field, 'form_alias'))
			return $id;

		return $parent->alias().'-'.$id;
	}

	/**
	 * Auto-create the id if necessary
	 *
	 * @access protected
	 * @return void
	 */
	protected function _auto_id()
	{
		if (Formo::config($this->_field, 'auto_id') AND ! $this->attr('id'))
		{
			$this->attr('id', $this->_make_id());
		}
	}

	/**
	 * Set or return the text
	 *
	 * @access public
	 * @return void
	 */
	public function text()
	{
		// Return the text if nothing was entered
		if (func_num_args() === 0)
			return $this->_vars['text'];

		// Fetch the args
		$vals = func_get_args();

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
				$this->_vars['text'] .= $vals[1];
				break;
			case '=.':
				$this->_vars['text'] = $vals[1].$this->_vars['text'];
				break;
			case 'callback':
				$this->_vars['text'] = $vals[1]($this->_vars['text']);
				break;
			}

			return $this;
		}

		// If one arg was given set text to that value
		(count($vals) == 1 AND $this->_vars['text'] = $vals[0]);

		return $this;
	}

	/**
	 * Turn attributes into a string (tag="val" tag2="val2")
	 *
	 * @access protected
	 * @return void
	 */
	protected function _attr_to_str()
	{
		$classes_str = implode(' ', $this->_vars['classes']);

		// Begin with _classes
		$str = $classes_str
			? " class=\"$classes_str\""
			: NULL;

		$attrs = $this->_vars['attr'];

		foreach ($attrs as $attr => $value)
		{
			$value = HTML::entities($value);
			$str.= " $attr=\"$value\"";
		}

		// Then attach styles
		if ($this->_vars['css'])
		{
			$str.= ' style="';

			$styles = $this->_vars['css'];

			foreach ($styles as $style => $value)
			{
				$str.= "$style:$value;";
			}
			$str.= '"';
		}

		return $str;
	}

	/**
	 * Create option attributes options
	 *
	 * @access public
	 * @param mixed $option
	 * @param mixed $key
	 * @return array
	 */
	public function get_option_attr($type, $option_value, $key = NULL)
	{
		if ($type == 'select')
			return $this->_get_select_option_attr($option_value, $key);

		$array = array('type' => $type);
		if ( ! is_array($option_value))
		{
			$array += array
			(
				'value' => $key,
				'name' => $this->_field->option_name(),
			);

			if (in_array($key, (array) $this->val()))
			{
				$array += array('checked' => 'checked');
			}
		}
		
		return $array;
	}

	/**
	 * Create option attributes
	 *
	 * @access public
	 * @param mixed $option_value
	 * @return void
	 */
	public function _get_select_option_attr($option_value)
	{
		$array = array('value' => $option_value);

		if ( (string) $option_value == (string) $this->val())
		{
			$array += array('selected' => 'selected');
		}

		return $array;
	}

	/**
	 * Return label for option
	 *
	 * @access public
	 * @param mixed $option
	 * @param mixed $key
	 * @return void
	 */
	public function option_label($option, $key = NULL)
	{
		if ( ! is_array($option))
		{
			return $this->translate($option);
		}
	}

	/**
	 * Allows just the opening tag to be returned
	 *
	 * @access public
	 * @return void
	 */
	public function open()
	{
		$singletag = in_array($this->_vars['tag'], $this->_singles);

		// return the string tag
		return '<'
			 . $this->_vars['tag']
			 . $this->_attr_to_str()
			 . (($singletag === TRUE)
			    // Do not end the tag if it's a single tag
			    ? NULL
			    // Otherwise close the tag
			    : ">");
	}

	// Allows just the closing tag to be returned
	public function close()
	{
		$singletag = in_array($this->_vars['tag'], $this->_singles);

		// Let the config file determine whether to close the tags
		$closetag = (Formo::config($this->_field, 'close_single_html_tags') === TRUE)
			? '/'
			: NULL;

		if ($singletag === TRUE)
		{
			return $closetag.'>'."\n";
		}
		else
		{
			return '</'.$this->_vars['tag'].'>'."\n";
		}
	}

	public function pre_render()
	{
		if ($attr = $this->_field->get('attr'))
		{
			$this->attr($attr);
		}

		if ($css = $this->_field->get('css'))
		{
			$this->css($css);
		}

		if ($text = $this->_field->get('text'))
		{
			$this->text($text);
		}

		$this->_auto_id();
		return parent::pre_render();
	}

	public function field()
	{
		return $this->_field;
	}

	/**
	 * Return HTML element
	 *
	 * @access public
	 * @return void
	 */
	public function html()
	{
		$this->_auto_id();
		$singletag = in_array($this->_vars['tag'], $this->_singles);

		$str = $this->open();

		if ( ! $singletag)
		{
			$str.= $this->_vars['text'];
			foreach ($this->_field->fields() as $field)
			{
				$str.= $field->view->html();
			}
		}

		return $str.= $this->close();
	}

}
