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
class AuthTransformer extends SkeletonTransformer
{
	/**
	 * Return actions
	 *
	 * @return string Rendered View
	 */
	protected function actions(Model $model)
	{
		$actions = array();

		if ($this->controller->has_access('view'))
		{
			array_push($actions, array(
				'url' => Uri::create($this->controller->url. '/view/' . $model->id),
				'icon' => 'glyphicon glyphicon-eye-open',
			));
		}

		if ($this->controller->has_access('edit'))
		{
			array_push($actions, array(
				'url' => Uri::create($this->controller->url. '/edit/' . $model->id),
				'icon' => 'glyphicon glyphicon-edit',
			));
		}

		$user = \Auth::get_user_id();

		if ($this->controller->has_access('delete') and (int) $model->id !== $user[1])
		{
			array_push($actions, array(
				'url' => Uri::create($this->controller->url. '/delete/' . $model->id),
				'icon' => 'glyphicon glyphicon-remove text-danger',
			));
		}

		return $this->controller->view('admin/skeleton/list/action')
				->set('actions', $actions, false)
				->render();
	}
}
