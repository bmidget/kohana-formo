<?php defined('SYSPATH') or die('No direct script access.');

class Formo_Core_Formo extends Formo_Innards {

	public static function form( array $array = NULL)
	{
		if (empty($array['alias']))
		{
			if ($array === NULL)
			{
				$array = array();
			}

			// Set the default alias
			$array += array('alias' => 'formo');
		}

		if (empty($array['driver']))
		{
			$array['driver'] = 'form';
		}

		return new Formo($array);
	}

	public static function factory( array $array = NULL)
	{
		return new Formo($array);
	}

	public function __construct( array $array = NULL)
	{
		$array = $this->_resolve_construct_aliases($array);

		if ( ! $array['alias'])
		{
			throw new Kohana_Exception('Formo.Every formo field must have an alias');
		}

		foreach ($array as $key => $value)
		{
			$this->set($key, $value);
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

	public function add($alias, $driver = NULL, $value = NULL, array $opts = NULL)
	{
		$args = $alias;

		if ( ! is_array($args))
		{
			// Treat args the same as a plain array
			$args = func_get_args();
		}

		if (Arr::get($args, 0) instanceof Formo)
		{
			$form = $args[0];

			// Allow a straight Formo object to be added
			$this->_fields[] = $form;

			if ( ! empty($args[1]))
			{
				$form->set($args[1]);
			}

			if ($form->get('driver') === 'form')
			{
				$form->set('driver', 'group');
			}

			$form->parent($this);

			return $this;
		}

		$args['parent'] = $this;

		// Create the field object
		$field = Formo::factory($args);
		$this->_fields[] = $field;

		$field->driver('added');

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
			if (is_array($get))
			{
				foreach ($get as $_get => $_set)
				{
					$this->attr($_get, $_set);
				}

				return $this;
			}
			else
			{
				return \Arr::get($this->_attr, $get);
			}
		}

		$this->_attr = \Arr::merge($this->_attr, array($get => $set));

		return $this;
	}

	public function callbacks($type, array $callbacks)
	{
		if ( ! isset($this->_callbacks[$type]))
		{
			$this->_callbacks[$type] = array();
		}

		$this->_callbacks[$type] = Arr::merge($this->_callbacks[$type], $callbacks);

		return $this;
	}

	public function close()
	{
		if ($tag = $this->driver('get_tag'))
		{
			$has_singletag = in_array($tag, $this->_single_tags);
	
			// Let the config file determine whether to close the tags
			$str = ($has_singletag === TRUE)
				? '>'."\n"
				: '</'.$tag.'>'."\n";

			return $this->driver('close', array('str' => $str));
		}
		else
		{
			return NULL;
		}
	}

	public function driver($func, array $args = NULL)
	{
		$class_name = 'Formo_Driver_'.ucfirst($this->_driver);

		$array = array('field' => $this);
		if ($args !== NULL)
		{
			$array = Arr::merge($args, $array);
		}

		return $class_name::$func($array);
	}

	public function error($message = NULL, array $params = array())
	{
		if ($message)
		{
			$this->_errors[$this->alias()] = array($message, $params);

			return $this;
		}
		else
		{
			return $this->_error_to_msg();
		}
	}

	public function errors( array & $array = NULL)
	{
		if ($array === NULL)
		{
			$array = array();
			$is_first_field = TRUE;
		}
		else
		{
			$is_first_field = FALSE;
		}

		$error = $this->error();

		if ( ! empty($this->_fields))
		{
			if ($is_first_field === TRUE AND $error)
			{
				$array[':self'] = $error;
			}

			if ($is_first_field === FALSE)
			{
				$array[$this->alias()] = array();

				if ($error)
				{
					$array[$this->alias()][':self'] = $error;
				}
			}
		}
		elseif ($error)
		{
			$array[$this->alias()] = $error;
		}

		foreach ($this->_fields as $field)
		{
			if ($is_first_field === TRUE)
			{
				$field->errors($array);
			}
			else
			{
				$field->errors($array[$this->alias()]);
			}
		}

		if ( ! empty($this->_fields) AND $is_first_field === FALSE AND empty($array[$this->alias()]))
		{
			unset($array[$this->alias()]);
		}

		return $array;
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

		if ($alias === ':self')
		{
			return $this;
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

	public function flatten( array &$array = NULL)
	{
		if ($array === NULL)
		{
			$array = array();
		}

		foreach ($this->_fields as $field)
		{
			if ($field->driver('is_a_parent'))
			{
				$field->flatten($array);
			}
			else
			{
				$array[] = $field;
			}
		}

		$this->_fields = $array;
	}

	public function get($var, $default = NULL)
	{
		$array_name = $this->_get_var_array($var);

		if ($array_name === '_vars')
		{
			return Arr::get($this->_vars, $var, $default);
		}

		return (isset($this->$array_name))
			? $this->$array_name
			: $default;
	}

	public function html()
	{
		if ($this->get('render') === false)
		{
			return NULL;
		}

		$str = $this->open();

		$opts = $this->driver('get_opts');
		$str.= implode("\n", $opts);

		foreach ($this->_fields as $field)
		{
			if ($field->get('render') === TRUE)
			{
				$str.= $field->render();
			}
		}
		
		$str.= $this->close();

		return $str;
	}

	public function label()
	{
		return $this->_get_label();
	}

	public function load( array $array = NULL)
	{
		if ($array === NULL)
		{
			$post = Request::$current->post();
			$files = $this->_get_files_array();
			$array = Arr::merge($post, $files);
		}

		$this->set('input_array', $array);

		if ( ! $this->sent($array))
		{
			return $this;
		}
		
		if ($this->config('namespaces') === TRUE)
		{
			foreach ($array as $namespace => $values)
			{
				if ($namespace === $this->alias())
				{
					$this->_load($values);
				}
				elseif ($field = $this->find($namespace, false) AND $field->driver('is_a_parent'))
				{
					$field->_load($values);
				}
			}
		}
		else
		{
			$this->_load($array);
		}

		return $this;
	}

	public function name()
	{
		$use_namespaces = $this->config('namespaces');

		return $this->driver('name', array('use_namespaces' => $use_namespaces));
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

			return $this->driver('open', array('str' => $str));
		}
		else
		{
			return NULL;
		}
	}

	public function order($field, $new_order = NULL, $relative_field = NULL)
	{
		if (is_array($field))
		{
			foreach($field as $alias => $values)
			{
				$this->order($alias, $values[0], $values[1]);
			}
		}
		else
		{
			$this->_order($field, $new_order, $relative_field);
		}

		return $this;
	}

	public function orm($method, array $vals = NULL)
	{
		if ($vals === NULL)
		{
			$vals = array();
		}

		$vals = Arr::merge($vals, array('field' => $this));

		$driver = $this->config('orm_driver');

		$class_name = 'Formo_Driver_ORM_'.ucfirst($driver);

		$class_name::$method($vals);

		return $this;
	}

	public function parent(Formo $parent = NULL)
	{
		if ($parent === NULL)
		{
			return $this->_parent;
		}

		$this->_parent = $parent;

		return $this;
	}

	public function remove($alias)
	{
		if (is_array($alias))
		{
			foreach ($alias as $_alias)
			{
				$this->remove($_alias);
			}
		}
		else
		{
			foreach ($this->_fields as $key => $field)
			{
				if ($field->alias() === $alias)
				{
					unset($this->_fields[$key]);
				}
			}
		}

		return $this;
	}

	public function remove_class($class)
	{
		
	}

	public function render()
	{
		if ($this->get('render') === FALSE)
		{
			return NULL;
		}

		$template = $this->driver('get_template');
		$template = $this->config('template_dir').$template;

		$view = View::factory($template)
			->set('field', $this)
			->set('label', $this->label())
			->set('title', $this->title());

		return $view->render();
	}

	public function render_opts()
	{
		if ($template = $this->driver('get_opts_template'))
		{
			$template = $this->config('template_dir').$template;

			$view = View::factory($template)
				->set('field', $this)
				->set('opts', $this->get('opts', array()));

			return $view->render();
		}
	}

	public function rule($alias, $rule, array $params = NULL)
	{
		$this->_add_rule($alias, $rule, $params);

		return $this;
	}

	public function rules($alias = NULL, array $rules = NULL)
	{
		if (func_num_args() === 0)
		{
			return $this->_rules;
		}

		foreach ($rules as $rule)
		{
			$this->_add_rule($alias, $rule[0], Arr::get($rule, 1));
		}

		return $this;
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

		if ($var === 'val')
		{
			// Special case for value
			$this->val($val);
			return $this;
		}

		if ($var === 'driver')
		{
			// Special case for driver
			$this->_set_driver($val);
			return $this;
		}

		if ($var == 'attr')
		{
			$this->attr($val);
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

	public function set_all( array $array)
	{
		foreach ($array as $alias => $values)
		{
			$this->$alias->set($values);
		}

		return $this;
	}
	
	public function sent( array $input_array = NULL)
	{
		if ($input_array === NULL)
		{
			if ($arr = $this->get('input_array'))
			{
				$input_array = $arr;
			}
			else
			{
				$input_array = Request::$current->post();
			}
		}

		foreach ($input_array as $alias => $value)
		{
			if ($alias === $this->alias() OR $this->find($alias))
			{
				return TRUE;
			}
		}

		if ($parent = $this->parent())
		{
			return $this->parent()->sent();
		}

		return FALSE;
	}

	public function subform($alias, array $fields, array $order = NULL, $driver = 'group')
	{
		$subform = Formo::factory(array(
			'alias' => $alias,
			'driver' => $driver,
		));

		foreach ($fields as $field_alias)
		{
			$field = $this->find($field_alias, TRUE);
			$subform->add($field);
			$this->remove($field_alias);
		}

		$this->add($subform);

		if ($order !== NULL)
		{
			$this->order($alias, $order[0], $order[1]);
		}

		return $this;
	}

	public function title()
	{
		return $this->driver('get_title');
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

	public function validate()
	{
		$this->driver('pre_validate');

		if ( ! $this->sent())
		{
			// Return and don't run any callbacks
			return FALSE;
		}

		if ($this->get('render') === FALSE OR $this->get('ignore') === TRUE)
		{
			return;
		}

		$pass_validation = TRUE;

		foreach ($this->_fields as $field)
		{
			if ($field->validate() === FALSE)
			{
				$pass_validation = FALSE;
			}
		}

		if ($pass_validation === TRUE)
		{
			if (Arr::get($this->_errors, $this->alias()))
			{
				$pass_validation = TRUE;
			}
			else
			{
				$validation = $this->validation();
				$this->_add_rules_to_validation($validation);
				$pass_validation = $validation->check();
				$this->_errors = $validation->errors();
			}
		}

		$result = $this->_run_callbacks($pass_validation);

		return ($pass_validation === TRUE AND $result === FALSE)
			? FALSE
			: $pass_validation;
	}

	public function validation( array $array = NULL)
	{
		$values = $this->driver('get_validation_values');
		$validation = new Validation($values);
		$this->_add_rules_to_validation($validation);

		return $validation;
	}

}