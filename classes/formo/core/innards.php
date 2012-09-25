<?php defined('SYSPATH') or die('No direct script access.');

abstract class Formo_Core_Innards {

	const NOTSET = '_NOTSET';
	const OPTS = 3;

	protected $_alias;
	protected $_attr = array
	(
		'class' => null,
	);
	protected $_config = array();
	protected $_construct_aliases = array
	(
		'alias' => 0,
		'driver' => 1,
		'val' => 2,
	);
	protected $_driver;
	protected $_editable = true;
	protected $_render = true;
	protected $_errors = array();
	protected $_fields = array();
	protected $_filters = array();
	protected $_label = self::NOTSET;
	protected $_opts = array();
	protected $_parent;
	protected $_rules = array();
	protected $_callbacks = array();
	protected $_single_tags = array
	(
		'br',
		'hr',
		'input',
	);
	protected $_vals = array
	(
		'original' => self::NOTSET,
		'new' => self::NOTSET,
	);
	protected $_vars = array();
	protected $_validation;

	public function config($param, $default = NULL)
	{
		$val = Arr::path($this->_config, $param, self::NOTSET);

		if ($val !== self::NOTSET)
		{
			return $val;
		}

		$parent = $this->parent();
		if ($parent)
		{
			$val = $parent->config($param, self::NOTSET);
			if ($val !== self::NOTSET)
			{
				return $val;
			}
		}

		return $default;
	}

	protected function _add_rule($alias, $rule, array $params = NULL)
	{
		if ($alias != ':self' AND $alias != $this->alias())
		{
			$field = $this->find($alias);
			return $field->rule($alias, $rule, $params);
		}

		$this->_rules[] = array($rule, $params);
	}

	protected function _add_rules_to_validation( Validation $validation)
	{
		$validation->rules($this->alias(), $this->_rules);
	}

	protected function _attr_to_str()
	{
		$str = NULL;

		$arr1 = $this->driver('get_attr');
		$arr2 = $this->get('attr', array());

		$attr = \Arr::merge($arr1, $arr2);

		foreach ($attr as $key => $value)
		{
			$str.= ' '.$key.'="'.HTML::entities($value).'"';
		}

		return $str;
	}

	protected function _get_files_array()
	{
		$files = $_FILES;
		$vars = array('name', 'type', 'tmp_name', 'error', 'size');

		$array = array();
		foreach ($files as $parent_alias => $vals)
		{
			foreach ($vars as $var_name)
			{
				if (is_array($vals[$var_name]))
				{
					foreach ($vals[$var_name] as $key => $val)
					{
						$array[$parent_alias][$key][$var_name] = $val;
					}
				}
				else
				{
					$array[$parent_alias][$var_name] = $vals[$var_name];
				}
			}
		}

		return $array;
	}

	protected function _get_latest_val()
	{
		return ($this->_vals['new'] !== self::NOTSET)
			? $this->_vals['new']
			: $this->_vals['original'];
	}

	protected function _get_label()
	{
		$label_str = $this->driver('get_label');
		$return_str = NULL;

		if ($label_str == NULL)
		{
			return NULL;
		}

		if ($file = $this->config('label_message_file'))
		{
			$parent = $this->parent();

			$prefix = ($parent = $this->parent())
				? $parent->alias()
				: NULL;

			$full_alias = $prefix
				? $prefix.'.'.$label_str
				: $label_str;

			if ($label = Kohana::message($file, $full_alias))
			{
				$return_str = (is_array($label))
					? $full_alias
					: $label;
			}
			elseif($label = Kohana::message($file, $label_str))
			{
				$return_str = $label;
			}
			elseif ($prefix AND ($label = Kohana::message($file, $prefix.'.default')))
			{
				if ($label === ':alias')
				{
					$return_str = $this->alias();
				}
				elseif ($label === ':alias_spaces')
				{
					$return_str = str_replace('_', ' ', $this->alias());
				}
			}
			else
			{
				$return_str = $label_str;
			}
		}
		else
		{
			$return_str = $label_str;
		}

		return ($this->config('translate') === TRUE)
			? __($return_str, NULL)
			: $return_str;
	}

	protected function _get_val()
	{
		$val = ($this->_vals['new'] !== self::NOTSET)
			? $this->_vals['new']
			: $this->_vals['original'];

		if ($val === self::NOTSET)
		{
			$val = NULL;
		}

		if ($val)
		{
			foreach ($this->_filters as $filter)
			{
				if (  ! is_array($filter))
				{
					// Very simple filters, take one argument
					$val = $filter($val);
				}
				else
				{
					// Support filters as defined in Kohana ORM
					$func = array_shift($filter);
					$params = Arr::merge(array($val), $filter);
					$val = call_user_func_array($func, $params);
				}
			}
		}

		$val =  $this->driver('get_val', array('val' => $val));

		return $val;
	}

	protected function _get_var_name($var)
	{
		$var_name = '_'.$var;

		if (property_exists($this, $var_name))
		{
			return $var_name;
		}
		else
		{
			return '_vars';
		}
	}

	protected function _error_to_msg( array $errors_array = NULL)
	{
		$file = $this->config('validation_message_file');
		$translate = $this->config('translate', FALSE);
		$errors = ($errors_array !== NULL)
			? $errors_array
			: $this->_errors;

		if ($set = Arr::get($errors, $this->alias()))
		{
			$field = $this->alias();
			list($error, $params) = $set;

			// Start the translation values list
			$values = array(
				':field' => $this->label(),
				':value' => $this->val(),
			);

			if ($message = Kohana::message($file, "{$field}.{$error}"))
			{
				// Found a message for this field and error
			}
			elseif ($message = Kohana::message($file, "{$field}.default"))
			{
				// Found a default message for this field
			}
			elseif ($message = Kohana::message($file, $error))
			{
				// Found a default message for this error
			}
			else
			{
				// No message exists, display the path expected
				$message = "{$file}.{$field}.{$error}";
			}

			if ($params)
			{
				foreach ($params as $key => $value)
				{
					if (is_array($value))
					{
						// All values must be strings
						$value = implode(', ', Arr::flatten($value));
					}
					elseif (is_object($value))
					{
						// Objects cannot be used in message files
						continue;
					}

					if ($field = $this->parent(TRUE)->find($value, TRUE))
					{
						// Use a field's label if we're referencing a field
						$value = $field->label();
					}

					// Add each parameter as a numbered value, starting from 1
					$values[':param'.($key + 1)] = $value;
				}
			}

			$message = strtr($message, $values);

			return ($translate === TRUE)
				? __($message)
				: $message;
		}

		return FALSE;
	}

	protected function _load( array $array)
	{
		foreach ($this->_fields as $field)
		{
			$value = Arr::get($array, $field->alias(), Formo::NOTSET);

			if ($value !== Formo::NOTSET)
			{
				$field->driver('load', array('val' => $value));
			}
			elseif ($field->driver('can_be_empty') === TRUE)
			{
				$field->val(null);
			}
		}
	}

	protected function _make_id()
	{
		if ($id = $this->attr('id'))
		{
			// Use the id if it's already set
			return $id;
		}

		$id = $this->alias();

		return $id;
	}

	protected function _merge($name, array $array)
	{
		$var_array = $this->_get_var_name($name);

		if ( ! is_array($this->$var_array))
		{
			throw new Kohana_Exception('Formo :param is not an array', array(':param' => '$'.$var_array));
		}

		$this->$var_array = Arr::merge($this->$var_array, $array);
	}

	protected function _order($field_alias, $new_order, $relative_field = NULL)
	{
		if (is_array($field_alias))
		{
			foreach ($field_alias as $_field => $_value)
			{
				$args = (array) $_value;
				array_unshift($args, $_field);
				$args = array_pad($args, 3, NULL);

				$method = new ReflectionMethod($this, 'order');
				$method->invokeArgs($this, $args);
			}

			return $this;
		}

		$fields = $this->_fields;
		$field_obj = NULL;
		$field_key = NULL;
		$new_key = (ctype_digit($new_order) OR is_int($new_order))
			? $new_order
			: FALSE;

		foreach ($this->_fields as $key => $field)
		{
			if ($field->alias() === $field_alias)
			{
				$field_obj = $field;
				$field_key = $key;
				break;
			}
		}

		if ($field_obj === NULL)
		{
			return;
		}

		$i = 0;
		foreach ($this->_fields as $field)
		{
			if ($field === $field_obj)
			{
				continue;
			}

			if ($relative_field AND $field->alias() === $relative_field)
			{
				$new_key = ($new_order === 'after')
					? $i + 1
					: $i;
			}

			$i++;
		}

		if ( $field_key === NULL OR $new_key === FALSE)
		{
			return;
		}

		unset($this->_fields[$field_key]);
		array_splice($this->_fields, $new_key, 0, array($field_obj));
	}

	protected function _run_callbacks($type = NULL)
	{
		$keys = array('fail' => FALSE, 'pass' => TRUE);
		$return = NULL;

		foreach ($keys as $key => $value)
		{
			if ($type === NULL AND $this->validate() !== $value)
			{
				continue;
			}

			if ($type === NULL OR $value === $type)
			{
				$callbacks = Arr::get($this->_callbacks, $key, array());
				foreach ($callbacks as $callback)
				{
					$result = $callback($this);

					if ($value === TRUE AND $result === FALSE)
					{
						$return = FALSE;
					}
				}
			}
		}

		return $return;
	}

	protected function _set_driver($driver)
	{
		if (strpos($driver, '|') !== FALSE)
		{
			$vals = explode('|', $driver);

			list($this->_driver, $type) = explode('|', $driver);
			$this->attr('type', $type);
		}
		else
		{
			$this->_driver = $driver;
		}
	}

	protected function _set_id( array & $array)
	{
		if ($this->config('auto_id') === TRUE AND empty($array['attr']['id']))
		{
			if (empty($array['attr']))
			{
				$array['attr'] = array();
			}

			Arr::set_path($array, 'attr.id', $array['alias']);
		}
	}

	protected function _set_val($val, $force_new = FALSE)
	{
		if ($this->_vals['original'] === self::NOTSET AND $force_new !== TRUE)
		{
			$this->_vals['original'] = $val;
		}
		else
		{
			$this->_vals['new'] = $val;
		}
	}

	protected function _resolve_construct_aliases($array)
	{
		$_array = $array;

		if (isset($_array[Formo::OPTS]))
		{
			$_array = Arr::merge($_array, $_array[Formo::OPTS]);
			unset($_array[Formo::OPTS]);
		}

		foreach ($this->_construct_aliases as $key => $key_alias)
		{
			if (array_key_exists($key_alias, $array))
			{
				$_array[$key] = $array[$key_alias];
				unset($_array[$key_alias]);
			}
		}

		if (empty($_array['driver']))
		{
			$_array['driver'] = 'input';
		}

		$this->set('driver', $_array['driver']);

		if ($parent = Arr::get($_array, 'parent'))
		{
			$this->set('parent', $_array['parent']);
			unset($_array['parent']);
		}

		if ($this->driver('is_a_parent'))
		{
			// Merge config files
			$config = (array) Kohana::$config->load('formo');
			$other_config = $this->get('config', array());
			$merged = Arr::merge($config, $other_config);
			$this->set('config', $config);
		}

		if (empty($_array['alias']))
		{
			throw new Kohana_Exception('Formo.Every formo field must have an alias');
		}

		$this->_set_id($_array);

		return $_array;
	}
}