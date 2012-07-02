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
	protected $_opts = array();
	protected $_parent;
	protected $_rules = array();
	protected $_single_tags = array
	(
		'br',
		'hr',
		'input',
	);
	protected $_vals = array
	(
		'original' => null,
		'new' => null,
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

	protected function _append( Formo $field)
	{
		$this->_fields += array($field);
	}

	protected function _attr_to_str()
	{
		$str = NULL;
		
		$arr1 = array('id' => $this->_make_id());
		$arr2 = $this->driver('get_attr', array('field' => $this));
		$arr3 = $this->get('attr', array());

		$attr = \Arr::merge($arr1, $arr2, $arr3);

		foreach ($attr as $key => $value)
		{
			$str.= ' '.$key.'="'.HTML::entities($value).'"';
		}

		return $str;
	}

	protected function _get_latest_val()
	{
		return (isset($this->_vals['new']))
			? $this->_vals['new']
			: $this->_vals['original'];
	}

	protected function _get_label()
	{
		$label = $this->driver('get_label', array('field' => $this));

		if ($label == NULL)
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
				? $prefix.'.'.$this->alias()
				: $this->alias();

			if ($label = Kohana::message($file, $full_alias))
			{
				return $label;
			}
			elseif($label = Kohana::message($file, $this->alias()))
			{
				return $label;
			}
			elseif ($prefix AND ($label = Kohana::message($file, $prefix.'.default')))
			{
				if ($label === ':alias')
				{
					return $this->alias();
				}
				elseif ($label === ':alias_spaces')
				{
					return str_replace('_', ' ', $this->alias());
				}
			}

			return $full_alias;
		}
		else
		{
			return $label;
		}
	}

	protected function _get_val()
	{
		$val = (isset($this->_vals['new']))
			? $this->_vals['new']
			: $this->_vals['original'];

		return $this->driver('get_val', array('val' => $val, 'field' => $this));
	}

	protected function _get_var_array($var)
	{
		if (in_array($var, array('driver', 'attr', 'alias', 'opts', 'render', 'editable', 'config', 'rules')))
		{
			return '_'.$var;
		}

		return '_vars';
	}

	protected function _error_to_msg()
	{
		$file = $this->config('validation_message_file');
		$translate = $this->config('translate', FALSE);

		if ($set = Arr::get($this->_errors, $this->alias()))
		{
			$field = $this->alias();
			list($error, $params) = $set;

			// Start the translation values list
			$values = array(
				':field' => $this->alias(),
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

			$message = strtr($message, $values);

			return $message;
		}

		return FALSE;
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

	protected function _set_val($val, $force_new = FALSE)
	{
		if ( ! isset($this->_vals['original']) AND $force_new !== TRUE)
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
		elseif (strpos($_array['driver'], '|') !== false)
		{
			$parts = explode('|', $_array['driver']);
			$_array['driver'] = $parts[0];
			if ( ! array_key_exists('attr', $_array))
			{
				$_array['attr'] = array();
			}
			$_array['attr']['type'] = $parts[1];
		}

		return $_array;
	}
}