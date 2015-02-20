<?php 
/**
 *	This file is part of the UnikaCMF project
 *	some function are steal from kohana Html	
 *
 *	@license MIT
 *	@author Fajar Khairil <fajar.khairil@gmail.com>
 */

namespace Unika\Helper;


class Html 
{ 

	/**
	 * @var  array  preferred order of attributes
	 */
	protected $attribute_order = array
	(
		'action',
		'method',
		'type',
		'id',
		'name',
		'value',
		'href',
		'src',
		'width',
		'height',
		'cols',
		'rows',
		'size',
		'maxlength',
		'rel',
		'media',
		'accept-charset',
		'accept',
		'tabindex',
		'accesskey',
		'alt',
		'title',
		'class',
		'style',
		'selected',
		'checked',
		'readonly',
		'disabled',
	);

	/**
	 * Creates an email (mailto:) anchor. Note that the title is not escaped,
	 * to allow HTML elements within links (images, etc).
	 *
	 *     echo HTML::mailto($address);
	 *
	 * @param   string  $email      email address to send to
	 * @param   string  $title      link text
	 * @param   array   $attributes HTML anchor attributes
	 * @return  string
	 * @uses    HTML::attributes
	 */
	public function mailto($email, $title = NULL, array $attributes = NULL)
	{
		if ($title === NULL)
		{
			// Use the email address as the title
			$title = $email;
		}

		return '<a href="&#109;&#097;&#105;&#108;&#116;&#111;&#058;'.$email.'"'.$this->attributes($attributes).'>'.$title.'</a>';
	}

	/**
	 * Creates a style sheet link element.
	 *
	 *     echo HTML::style('media/css/screen.css');
	 *
	 * @param   string  $file       file name
	 * @param   array   $attributes default attributes
	 * @param   mixed   $protocol   protocol to pass to URL::base()
	 * @param   boolean $index      include the index page
	 * @return  string
	 * @uses    URL::base
	 * @uses    HTML::attributes
	 */
	public function style($file, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		// Set the stylesheet link
		$attributes['href'] = $file;

		// Set the stylesheet rel
		$attributes['rel'] = empty($attributes['rel']) ? 'stylesheet' : $attributes['rel'];

		// Set the stylesheet type
		$attributes['type'] = 'text/css';

		return '<link'.$this->attributes($attributes).' />';
	}

	/**
	 * Creates a script link.
	 *
	 *     echo HTML::script('media/js/jquery.min.js');
	 *
	 * @param   string  $file       file name
	 * @param   array   $attributes default attributes
	 * @param   mixed   $protocol   protocol to pass to URL::base()
	 * @param   boolean $index      include the index page
	 * @return  string
	 * @uses    URL::base
	 * @uses    HTML::attributes
	 */
	public function script($file, array $attributes = NULL, $protocol = NULL, $index = FALSE)
	{
		// Set the script link
		$attributes['src'] = $file;

		// Set the script type
		$attributes['type'] = 'text/javascript';

		return '<script'.$this->attributes($attributes).'></script>';
	}



	public function obfuscate($string)
	{
	    $safe = '';
	    foreach (str_split($string) as $letter)
	    {
	        switch (rand(1, 3))
	        {
	            // HTML entity code
	            case 1:
	                $safe .= '&#'.ord($letter).';';
	            break;
	 
	            // Hex character code
	            case 2:
	                $safe .= '&#x'.dechex(ord($letter)).';';
	            break;
	 
	            // Raw (no) encoding
	            case 3:
	                $safe .= $letter;
	        }
	    }
	 
	    return $safe;
	}

	/**
	 * Compiles an array of HTML attributes into an attribute string.
	 * Attributes will be sorted using HTML::$attribute_order for consistency.
	 *
	 *     echo '<div'.HTML::attributes($attrs).'>'.$content.'</div>';
	 *
	 * @param   array   $attributes attribute list
	 * @return  string
	 */
	public function attributes(array $attributes = NULL)
	{
		if (empty($attributes))
			return '';

		$sorted = array();
		foreach ($this->attribute_order as $key)
		{
			if (isset($attributes[$key]))
			{
				// Add the attribute to the sorted list
				$sorted[$key] = $attributes[$key];
			}
		}

		// Combine the sorted attributes
		$attributes = $sorted + $attributes;

		$compiled = '';
		foreach ($attributes as $key => $value)
		{
			if ($value === NULL)
			{
				// Skip attributes that have NULL values
				continue;
			}

			if (is_int($key))
			{
				// Assume non-associative keys are mirrored attributes
				$key = $value;
			}

			// Add the attribute key
			$compiled .= ' '.$key;

			if ($value)
			{
				// Add the attribute value
				$compiled .= '="'.htmlspecialchars($value).'"';
			}
		}

		return $compiled;
	}
}