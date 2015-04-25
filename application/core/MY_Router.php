<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2006, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * 已扩展的 Router 类库
 *
 * 实现 Module 的 URL 可访问
 *
 * @package		CodeIgniter
 * @subpackage	Libraries
 * @author		Hex
 * @category	HMVC
 * @link		http://codeigniter.org.cn/forums/thread-1319-1-2.html
 */

class MY_Router extends CI_Router
{
	/**
	 * Constructor
	 *
	 * Runs the route mapping function.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------
	/**
	 * Validates the supplied segments.  Attempts to determine the path to
	 * the controller.
	 *
	 * @access	private
	 * @param	array
	 * @return	array
	 */
	function _validate_request($segments)
	{
		$c = count($segments);
		if ($segments[0] === 'module')
		{
			return $this->_validate_module_request($segments);
		}
		
		// Loop through our segments and return as soon as a controller
		
		// is found or when such a directory doesn't exist
		while ($c-- > 0)
		{
			$test = $this->directory
			.ucfirst($this->translate_uri_dashes === TRUE ? str_replace('-', '_', $segments[0]) : $segments[0]);
		
			if ( ! file_exists(APPPATH.'controllers/'.$test.'.php') && is_dir(APPPATH.'controllers/'.$this->directory.$segments[0]))
			{
				$this->set_directory(array_shift($segments), TRUE);
				continue;
			}
		
			return $segments;
		}
		
		// This means that all segments were actually directories
		return $segments;
	}

	// --------------------------------------------------------------------

	/**
	 * Module 的访问直接路由到特殊的 Module_proxy 控制器
	 *
	 * @access	private
	 * @param	array
	 * @return	array
	 */
	function _validate_module_request($segments)
	{
		$segments = array_slice($segments, 1);

		$this->set_directory('../third_party');
		$this->set_class('module_proxy');
		$this->set_method('index');

		return $segments;
	}

	// --------------------------------------------------------------------

	/**
	 * Set the Route
	 *
	 * This function takes an array of URI segments as
	 * input, and sets the current class/method
	 *
	 * @access	private
	 * @param	array
	 * @param	bool
	 * @return	void
	 */
	function _set_request($segments = array())
	{
		// 如果是访问 Module，则转到 _validate_module_request 方法处理
		if (count($segments) > 0 && $segments[0] === 'module')
		{
			$segments = $this->_validate_module_request($segments);
			$this->uri->rsegments = $segments;

			return;
		}

		// If we don't have any segments left - try the default controller;
		// WARNING: Directories get shifted out of the segments array!
		if (empty($segments))
		{
			$this->_set_default_controller();
			return;
		}

		if ($this->translate_uri_dashes === TRUE)
		{
			$segments[0] = str_replace('-', '_', $segments[0]);
			if (isset($segments[1]))
			{
				$segments[1] = str_replace('-', '_', $segments[1]);
			}
		}

		$this->set_class($segments[0]);
		if (isset($segments[1]))
		{
			$this->set_method($segments[1]);
		}
		else
		{
			$segments[1] = 'index';
		}

		array_unshift($segments, NULL);
		unset($segments[0]);
		$this->uri->rsegments = $segments;
	}

	// --------------------------------------------------------------------

}

/* End of file MY_Router.php */
/* Location: ./application/core/MY_Router.php */
