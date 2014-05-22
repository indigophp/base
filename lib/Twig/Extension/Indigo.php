<?php

/*
 * This file is part of the Indigo Base package.
 *
 * (c) Indigo Development Team
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Twig\Extension;

use Fuel\Fieldset\Element;
use Fuel\Fieldset\Fieldset;
use Twig_Extension;

class Indigo extends Twig_Extension
{

	/**
	 * Gets the name of the extension.
	 *
	 * @return  string
	 */
	public function getName()
	{
		return 'indigo';
	}

	/**
	 * {@inheritdocs}
	 */
	public function getFunctions()
	{
		return array(
			'gravatar'             => new \Twig_Function_Function('Gravatar::forge'),
			'menu'                 => new \Twig_Function_Function('Menu::render_menu'),
			'admin_menu'           => new \Twig_Function_Function('Menu_Admin::render_menu'),
			'request'              => new \Twig_Function_Function('Request::active'),
			'admin_url'            => new \Twig_Function_Function('Uri::admin'),
			'default_img'          => new \Twig_Function_Method($this, 'getDefaultImage'),
			'auth_get_screen_name' => new \Twig_Function_Function('Auth::get_screen_name'),
			'auth_get_meta'        => new \Twig_Function_Function('Auth::get_profile_fields'),
			'date'                 => new \Twig_Function_Function('Date::forge'),
			'time_elapsed'         => new \Twig_Function_Method($this, 'time_elapsed'),
			'getFormElementType'   => new \Twig_Function_Method($this, 'getFormElementType'),
		);
	}

	/**
	 * {@inheritdocs}
	 */
	public function getFilters()
	{
		return array(
			'md5'                 => new \Twig_Filter_Function('md5'),
			'html'                => new \Twig_Filter_Function('html_entity_decode'),
			'pluralize'           => new \Twig_Filter_Function('Inflector::pluralize'),
			'bytes'               => new \Twig_Filter_Function('Num::format_bytes'),
			'qty'                 => new \Twig_Filter_Function('Num::quantity'),
			'bool'                => new \Twig_Filter_Method  ($this, 'bool'),
			'attr'                => new \Twig_Filter_Function('array_to_attr'),
			'date_format'         => new \Twig_Filter_Method  ($this, 'dateFormat'),
			'truncate_html'       => new \Twig_Filter_Method  ($this, 'printTruncated'),
			'eval'                => new \Twig_Filter_Method  ($this, 'evaluate', array(
				'needs_environment' => true,
				'needs_context'     => true,
				'is_safe'           => array(
					'evaluate' => true
				)
			))
		);
	}

	/**
	 * {@inheritdocs}
	 */
	public function getTests()
	{
		return array(
			'bool'     => new \Twig_Test_Function('is_bool'),
			'fieldset' => new \Twig_Test_Method($this, 'isFieldset')
		);
	}

	/**
	 * Check whether given object is instance of Fieldset
	 *
	 * @param  mixed  $fieldset
	 * @return boolean
	 */
	public function isFieldset($fieldset)
	{
		return $fieldset instanceof Fieldset;
	}

	/*
	No general interface for now
	 */
	public function getFormElementType($element)
	{
		return strtolower(\Inflector::denamespace(get_class($element)));
	}

	/**
	 * This function will evaluate $string through the $environment, and return its results.
	 *
	 * @param array $context
	 * @param string $string
	 */
	public function evaluate( \Twig_Environment $environment, $context, $string ) {
		$loader = $environment->getLoader( );

		$parsed = $this->parseString( $environment, $context, $string );

		$environment->setLoader( $loader );
		return $parsed;
	}

	/**
	 * Sets the parser for the environment to Twig_Loader_String, and parsed the string $string.
	 *
	 * @param \Twig_Environment $environment
	 * @param array $context
	 * @param string $string
	 * @return string
	 */
	protected function parseString( \Twig_Environment $environment, $context, $string ) {
		$environment->setLoader( new \Twig_Loader_String( ) );
		return $environment->render( $string, $context );
	}

	// base_url() ~ 'assets/theme/img/icons/' ~ (model.group_id == 6 ? 'admin' : model.group_id == 1 ? 'user_cancel' : 'user') ~ '.png' | url_encode
	public function getDefaultImage(\Auth\Model\Auth_User $model)
	{
		return urlencode(\Uri::create('assets/theme/img/icons/' . ($model->group_id == 6 ? 'admin' : ($model->group_id == 1 ? 'banned' : 'user') ) . '.png'));
	}

	public function dateFormat($timestamp, $pattern_key = 'local', $timezone = null)
	{
		if (is_numeric($timestamp))
		{
			$date = \Date::forge($timestamp);
		}
		else
		{
			$date = \Date::create_from_string($timestamp);
		}

		return $date->format($pattern_key, $timezone);
	}

	/**
	 * Return time elapsed from timestamp
	 *
	 * @param  int $timestamp
	 * @return int
	 */
	public function time_elapsed($timestamp)
	{
		if (empty($timestamp)) {
			return null;
		}

		$time = new \DateTime();
		$time->setTimestamp($timestamp);
		$diff = $time->diff(new \DateTime());

		$elapsed = '';

		if ($diff->days > 0)
		{
			$elapsed .= str_replace('%d', $diff->days, ngettext("%d day", "%d days", $diff->days)) . ', ';
		}

		$elapsed .= $diff->format('%H:%I:%S');

		return $elapsed;
	}

	/**
	 * Check if value is bool and return string representation
	 *
	 * @param  mixed $value
	 * @return mixed
	 */
	public function bool($value)
	{
		return is_bool($value) ? ($value ? 'true' : 'false') : $value;
	}

	/**
	 * Truncate function with closing HTML tags from http://stackoverflow.com/a/1193598/518076
	 * @param  number  $maxLength The character length. Does not include the HTML tags.
	 * @param  string  $html      The HTML to be stripped
	 * @param  boolean $isUtf8    False if not UTF-8, but an ASCII compatible single-byte encoding is used
	 * @return string             The HTML truncated
	 */
	function printTruncated($html, $maxLength, $isUtf8=true)
	{
		$return = '';
		$printedLength = 0;
		$position = 0;
		$tags = array();

		// For UTF-8, we need to count multibyte sequences as one character.
		$re = $isUtf8
			? '{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;|[\x80-\xFF][\x80-\xBF]*}'
			: '{</?([a-z]+)[^>]*>|&#?[a-zA-Z0-9]+;}';

		while ($printedLength < $maxLength && preg_match($re, $html, $match, PREG_OFFSET_CAPTURE, $position))
		{
			list($tag, $tagPosition) = $match[0];


			// Print text leading up to the tag.
			$str = substr($html, $position, $tagPosition - $position);
			if ($printedLength + strlen($str) > $maxLength)
			{
				$return .= (substr($str, 0, $maxLength - $printedLength));
				$printedLength = $maxLength;
				break;
			}

			$return .= ($str);
			$printedLength += strlen($str);
			if ($printedLength >= $maxLength) break;

			if ($tag[0] == '&' || ord($tag) >= 0x80)
			{
				// Pass the entity or UTF-8 multibyte sequence through unchanged.
				$return .= ($tag);
				$printedLength++;
			}
			else
			{
				// Handle the tag.
				$tagName = $match[1][0];
				if ($tag[1] == '/')
				{
					// This is a closing tag.

					$openingTag = array_pop($tags);
					assert($openingTag == $tagName); // check that tags are properly nested.

					$return .= ($tag);
				}
				else if ($tag[strlen($tag) - 2] == '/')
				{
					// Self-closing tag.
					$return .= ($tag);
				}
				else
				{
					// Opening tag.
					$return .= ($tag);
					$tags[] = $tagName;
				}
			}

			// Continue after the tag.
			$position = $tagPosition + strlen($tag);
		}

		// Print any remaining text.
		if ($printedLength < $maxLength && $position < strlen($html))
		{
			$return .= '...';
			$return .= (substr($html, $position, $maxLength - $printedLength));
		}

		// Close any open tags.
		while (!empty($tags))
		{
			$return .= '</'.array_pop($tags).'>';
		}
		return $return;
	}

}