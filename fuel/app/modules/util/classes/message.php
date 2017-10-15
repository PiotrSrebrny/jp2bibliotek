<?php

namespace Util;

class Message
{
	protected static $instance;
	
	private $success = array();
	private $danger = array();
	
	static private function get_instance()
	{
		if (static::$instance == NULL)
			static::$instance = new Message();
		
		return static::$instance;
	}
	
	static public function add_success($msg)
	{
		$instance = static::get_instance();
		
		array_push($instance->success, $msg);
		
		\Session::set('message_success', $instance->success);
	}
	
	static public function get_success()
	{
		$success = \Session::get('message_success');
		
		\Session::delete('message_success');
		
		return $success;
	}
	
	static public function add_danger($msg)
	{
		$instance = static::get_instance();
	
		array_push($instance->success, $msg);
	
		\Session::set('message_danger', $instance->success);
	}
	
	
	static public function get_danger()
	{
		$danger = \Session::get('message_danger');
		
		\Session::delete('message_danger');
		
		return $danger;
	}
}
