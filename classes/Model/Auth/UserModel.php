<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Indigo\Base\Model\Auth;

use Auth\Model\Auth_User;
use Auth\Model\Auth_Group;

/**
 * User model
 *
 * @author Márk Sági-Kazár <mark.sagikazar@gmail.com>
 */
class UserModel extends Auth_User
{
	use \Indigo\Base\Model\SkeletonTrait;

	protected static $_fieldsets = array(
		'basic' => 'Basic details'
	);

	protected static $_properties = array(
		'id' => array(
			'label' => 'Id',
			'list' => array(
				'type' => 'text'
			),
		),
		'username'       => array(
			'label'      => 'Username',
			'default'    => 0,
			'null'       => false,
			'list'       => true,
			'validation' => array('required', 'maxLength' => array(255)),
		),
		'email'          => array(
			'label'      => 'Email',
			'default'    => 0,
			'null'       => false,
			'list'       => true,
			'validation' => array('required', 'email'),
		),
		'group_id'       => array(
			'label'      => 'Group',
			'default'    => 0,
			'null'       => false,
			'list'       => true,
			'form'       => array('type' => 'select'),
			'validation' => array('required', 'numeric'),
		),
		'password'       => array(
			'label'      => 'Password',
			'default'    => 0,
			'null'       => false,
			'form'       => array('type' => 'password'),
			'validation' => array('minLength' => array(8), 'matchField' => array('confirm'))
		),
		'confirm'        => array(
			'virtual'    => true,
			'view'       => true,
			'label'      => 'Confirm password',
			'default'    => 0,
			'form'       => array('type' => 'password'),
		),
		'last_login'     => array(
			'form'       => array('type' => false),
		),
		'previous_login' => array(
			'form'       => array('type' => false),
		),
		'login_hash'     => array(
			'form'       => array('type' => false),
		),
		'user_id'        => array(
			'default'    => 0,
			'null'       => false,
			'form'       => array('type' => false),
		),
		'created_at'     => array(
			'default'    => 0,
			'null'       => false,
			'form'       => array('type' => false),
		),
		'updated_at'     => array(
			'default'    => 0,
			'null'       => false,
			'form'       => array('type' => false),
		),
	);

	public static function _init()
	{
		$groups = Auth_Group::query()->get();
		$groups = \Arr::pluck($groups, 'name', 'id');

		static::$_properties['group_id']['form']['options'] = $groups;
		static::$_properties['group_id']['validation']['value'] = array_keys($groups);
	}
}
