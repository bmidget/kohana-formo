<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core_Formo extends Formo_Innards {

	public static function form( array $array = NULL)
	{
		if ($array === NULL)
		{
			$array = array('formo');
		}

		if (empty($array['driver']))
		{
			$array['driver'] = 'form';
		}

		return new Formo($array);
	}

	public static function factory( array $array = NULL, array $opts = NULL)
	{
		return new Formo($array);
	}

	public function __construct( array $array = NULL, array $opts = NULL)
	{
		// First look for the aliases
		if ($opts !== NULL)
		{
			$array += array('opts' => $opts);
		}

		$array = $this->_resolve_construct_aliases($array, 'alias');

		if ( ! $array['alias'])
		{
			throw new Kohana_Exception('Formo.Every formo field must have an alias');
		}

		foreach ($array as $key => $value)
		{
			if (is_array($value))
			{
				foreach ($value as $_key => $_value)
				{
					$this->set($_key, $_value);
				}
			}
			else
			{
				$this->set($key, $value);
			}
		}
	}

	public function __get($key)
	{
		return $this->find($key, TRUE);
	}

	public function __isset($key)
	{
		return (bool) $this->find($key, TRUE);
	}

	public function __toString()
	{
		return $this->render();
	}

	public function add($args)
	{
		if ($args instanceof Formo)
		{
			// Allow a straight Formo object to be added
			$this->_fields[] = $args;

			return $this;
		}

		if ( ! is_array($args))
		{
			// Treat args the same as a plain array
			$args = func_get_args();
		}

		// Create the field object
		$field = Formo::factory($args);
		$this->_fields[] = $field;

		return $this;
	}

	public function add_class($class)
	{
		// Break current classes into an array
		$all_classes = explode(' ', \Arr::get($this->_attr, 'class'));

		// Use an array for new classes too
		$classes = ( ! is_array($class))
			? explode(' ', $class)
			: $class;

		foreach ($classes as $_class)
		{
			if ( ! in_array($_class, $all_classes))
			{
				// Add the new class if it isn't already there
				$all_classes[] = $_class;
			}
		}


		$this->_attr = \Arr::merge($this->_attr, array('class' => implode(' ', $all_classes)));

		return $this;
	}

	public function alias()
	{
		return $this->_alias;
	}

	public function as_array($value = NULL)
	{
		$array = array();
		foreach ($this->_fields as $field)
		{
			if ($value === NULL)
			{
				$array += array($field->alias() => $field);
			}
			elseif ($value == 'val')
			{
				$array += array($field->alias() => $field->val());
			}
			else
			{
				$array += array($field->alias() => $field->get($value));
			}
		}

		return $array;
	}

	public function attr($get, $set = NULL)
	{
		if (func_num_args() == 1)
		{
			return \Arr::get($this->_attr, $get);
		}

		$this->_attr = \Arr::merge($this->_attr, array($get => $set));

		return $this;
	}

	public function callbacks( array $callbacks)
	{
		
	}

	public function check( array $array)
	{
		return $this->validation($array)->check();
	}

	public function close()
	{
		if ($tag = $this->driver('get_tag'))
		{
			$has_singletag = in_array($tag, $this->_single_tags);
	
			// Let the config file determine whether to close the tags
			return ($has_singletag === TRUE)
				? '>'."\n"
				: '</'.$tag.'>'."\n";
		}
		else
		{
			return NULL;
		}
	}

	public function driver($func, array $args = NULL)
	{
		$class_name = 'Formo_Driver_'.ucfirst($this->_driver);
		return $class_name::$func($args);
	}

	public function error($alias = NULL, $message = NULL, array $params = NULL)
	{
		
	}

	public function errors($file = NULL, $translate = NULL)
	{
		
	}

	public function find($alias, $not_recursive = FALSE)
	{
		if (is_array($alias))
		{
			$array = array();
			foreach ($alias as $_alias)
			{
				if ($field = $this->find($_alias))
				{
					$array += array($field);
				}
			}

			return $array;
		}
		
		foreach ($this->_fields as $field)
		{
			// First look directly at through all this object's fields
			if ($field->alias() == $alias)
			{
				return $field;
			}
		}

		if ($not_recursive === TRUE)
		{
			return NULL;
		}

		foreach ($this->_fields as $field)
		{
			// Next look deeper for the field
			if ($_field = $field->find($alias))
			{
				return $_field;
			}
		}

		// Return NULL if not a match
		return NULL;
	}

	public function get($var, $default = NULL)
	{
		$array_name = $this->_get_var_array($var);

		return $this->{$array_name};
	}

	public function html()
	{
		$str = $this->open();

		$opts = $this->driver('get_opts', array('field' => $this));
		$str.= implode("\n", $opts);

		foreach ($this->_fields as $field)
		{
			$str.= $field->render();
		}
		
		$str.= $this->close();

		return $str;
	}

	public function label()
	{
		return $this->driver('get_label', array('field' => $this));
	}

	public function open()
	{
		if ($tag = $this->driver('get_tag'))
		{
			$has_singletag = in_array($tag, $this->_single_tags);
			
			$str = '<'.$tag.$this->_attr_to_str();
			$str.= ($has_singletag === TRUE)
				? NULL
				: '>';
	
			return $str;
		}
		else
		{
			return NULL;
		}
	}

	public function order($field, $new_order, $relative_field = NULL)
	{
		
	}

	public function remove($field)
	{
		
	}

	public function remove_class($class)
	{
		
	}

	public function render()
	{
		$template = $this->driver('get_template', array('field' => $this));

		$view = View::factory($template)
			->set('field', $this)
			->set('label', $this->label())
			->set('title', $this->title());

		return $view->render();
	}

	public function render_opts()
	{
		if ($template = $this->driver('get_opts_template', array('field' => $this)))
		{
			$view = View::factory($template)
				->set('field', $this)
				->set('opts', $this->get('opts', array()));

			return $view->render();
		}
	}

	public function rule($field, $rule, array $params = NULL)
	{
		
	}

	public function rules($field, array $rules)
	{
		
	}

	public function set($var, $val = NULL)
	{
		if (is_array($var) AND $val === NULL)
		{
			foreach ($var as $_var => $_val)
			{
				$this->set($_var, $_val);
			}

			return $this;
		}

		if ($var == 'val')
		{
			// Special case for value
			$this->val($val);

			return $this;
		}

		$array_name = $this->_get_var_array($var);

		if (is_array($this->{$array_name}))
		{
			// Treat arrays as arrays
			$_val = (is_array($val))
				? $val
				: array($var => $val);

			$this->{$array_name} = \Arr::merge($this->{$array_name}, $_val);
		}
		else
		{
			// Just set non-arrays
			$this->{$array_name} = $val;
		}

		return $this;
	}
	
	public function sent()
	{
		
	}

	public function subform($alias, $driver, array $fields, $order = NULL)
	{
		
	}

	public function text($text, $operation = NULL)
	{
		// Get the args
		$vals = func_get_args();

		if ($operation)
		{
			foreach ($operation as $key => $val)
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

		if ($operation !== NULL)
		{
			switch ($operation)
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
		}
		else
		{
			$this->set('text', $text);
		}

		return $this;
	}

	public function title()
	{
		return $this->driver('get_title', array('field' => $this));
	}

	public function val($new_val = NULL, $force_new = FALSE)
	{
		if (func_num_args() === 0)
		{
			return $this->_get_val();
		}
		else
		{
			$new_val = $this->driver('new_val', array('new_val' => $new_val));
			$this->_set_val($new_val, $force_new);

			return $this;
		}
	}

	public function validate( Closure $func = NULL)
	{
		if ($func !== NULL)
		{
			$errors = $func();
		}
	}

	public function validation( array $array = NULL)
	{
		$validation = $this->_make_validation($array);
		return $validation;
	}

}