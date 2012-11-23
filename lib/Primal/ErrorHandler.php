<?php 

namespace Primal;

class ErrorHandler {
	
	public function __construct($callback = null) {
		ini_set('display_errors', 'off');
		ini_set('html_errors', 'off');
		
		if ($callback === null) {
			$callback = function ($data) {
				header('Content-type: application/json', 500);
				echo json_encode($data);
			};
		}
		
		$caught = false;
		
		set_exception_handler(function ($ex) use ($caught, $callback) {
			$caught = true;
			$callback(array(
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

		set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) use ($caught, $callback) {
			$caught = true;
			$callback(array(
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
		
		
		register_shutdown_function(function ()  use ($caught, $callback){
			if (($error = error_get_last()) && !$caught) {
				$callback(array(
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
	
	public static function Init($callback = null) {
		return new static($callback);
	}
}
