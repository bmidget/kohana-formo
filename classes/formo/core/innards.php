<?php defined('SYSPATH') or die('No direct script access.');

abstract class Formo_Core_Innards {

	const OPTS = 4;
	protected $_alias;
	protected $_attr = array
	(
		'class' => null,
	);
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

	protected function _add_rule($alias, $rule, array $params = NULL)
	{
		if ($alias != ':self' AND $alias != $this->alias())
		{
			$field = $this->find($alias);
			return $field->rule($alias, $rule, $params);
		}

		$rules = Arr::get($this->_rules, $alias, array());
		$rules[] = array($rule, $params);
		$this->_rules[$alias] = $rules;
	}

	protected function _add_rules_to_validation( Validation $validation)
	{
		$rules = $this->_rules;

		foreach ($this->_fields as $field)
		{
			$rules += $field->rules();
		}

		foreach ($rules as $alias => $rules)
		{
			$validation->rules($alias, $rules);
		}
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

	protected function _get_val()
	{
		$val = (isset($this->_vals['new']))
			? $this->_vals['new']
			: $this->_vals['original'];

		return $this->driver('get_val', array('val' => $val, 'field' => $this));
	}

	protected function _get_validation_values()
	{
		$values = $this->val();
		if ( ! is_array($values))
		{
			$values = array($this->alias => $this->val());
		}

		return $values;
	}

	protected function _get_var_array($var)
	{
		if (in_array($var, array('driver', 'attr', 'alias', 'opts', 'render', 'editable')))
		{
			return '_'.$var;
		}

		return '_vars';
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

		foreach ($this->_construct_aliases as $key => $key_alias)
		{
			if (isset($array[$key_alias]))
			{
				$_array[$key] = $array[$key_alias];
				unset($_array[$key_alias]);
			}
		}

		if (empty($_array['driver']))
		{
			$_array['driver'] = 'input';
		}

		return $_array;
	}
}