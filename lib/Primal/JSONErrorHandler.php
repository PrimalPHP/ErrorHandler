<?php 

namespace Primal;

class JSONErrorHandler {
	
	private static $initialized = false;
	
	function __construct() {
		if (static::$initialized) return;
		
		static::$initialized = true;
		
		ini_set('html_errors', 'off');
		
		$caught = false;
		
		set_exception_handler(function ($ex) use ($caught) {
			$caught = true;
			header('Content-type: application/json', 500);
			echo json_encode(array(
				'crash'=>array(
					'type'=>'Unhandled '.get_class($ex),
					'level'=>$ex->getCode(),
					'message'=>$ex->getMessage(),
					'file'=>$ex->getFile(),
					'line'=>$ex->getLine(),
					'trace'=>$ex->getTrace()
				)
			));
			exit;
		});

		set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) use ($caught) {
			$caught = true;
			header('Content-type: application/json', 500);
			echo json_encode(array(
				'crash'=>array(
					'type'=>'RuntimeError',
					'level'=>$errno,
					'message'=>$errstr,
					'file'=>$errfile,
					'line'=>$errline,
					'context'=>$errcontext,
					'trace'=>debug_backtrace()
				)
			));
			exit;
		}, error_reporting());
		
		
		register_shutdown_function(function ()  use ($caught){
			if (($error = error_get_last()) && !$caught) {
				header('Content-type: application/json', 500);
				echo json_encode(array(
					'crash'=>array(
						'type'=>'Fatal Error',
						'level'=>$error['type'],
						'message'=>$error['message'],
						'file'=>$error['file'],
						'line'=>$error['line'],
						'trace'=>debug_backtrace()
					)
				));
				exit;
			}
		});
	}
	
	public static function Init() {
		return new static();
	}
}
