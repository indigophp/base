<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\Fractal\Transformer;

use League\Fractal\TransformerAbstract;
use Fuel\Common\Arr;
use Orm\Model;
use Uri;

/**
 * Skeleton Model Transformer
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class SkeletonTransformer extends TransformerAbstract
{
	protected $controller;

	protected $actions = true;

	public function __construct($controller, $actions = true)
	{
		$this->controller = $controller;
		$this->actions = $actions;
	}

	public function transform(Model $model)
	{
		$properties = $model->properties();

		$data = $model->to_array(false, false, true);
		$data = Arr::subset($data, array_keys($properties));
		$data = Arr::flattenAssoc($data, '.');

		// Check for options and set value
		foreach ($properties as $key => $value)
		{
			if ( ! empty($data) and $options = Arr::get($value, 'list.options', false))
			{
				$data[$key] = $options[$data[$key]];
			}
		}

		if ($this->actions)
		{
			$actions = array();

			if ($this->controller->has_access('view'))
			{
				array_push($actions, array(
					'url' => Uri::create($this->controller->_url. '/view/' . $model->id),
					'icon' => 'glyphicon glyphicon-eye-open',
				));
			}

			if ($this->controller->has_access('edit'))
			{
				array_push($actions, array(
					'url' => Uri::create($this->controller->_url. '/edit/' . $model->id),
					'icon' => 'glyphicon glyphicon-edit',
				));
			}

			if ($this->controller->has_access('delete'))
			{
				array_push($actions, array(
					'url' => Uri::create($this->controller->_url. '/delete/' . $model->id),
					'icon' => 'glyphicon glyphicon-remove text-danger',
				));
			}

			$data['action'] = $this->controller->view('admin/skeleton/list/action')
				->set('actions', $actions, false)
				->render();
		}


		return array_values($data);
	}

}
