ErrorHandler
========

Simple to use class for catching all uncaught errors for processing.  By default the class outputs the error as JSON, but that behavior can be overridden with a custom callback.  Designed to help with error reporting when building APIs, but useful in generic application settings as well.  Handles Exceptions, Runtime Errors and Fatal Errors

##Usage

    //defaults to outputting errors as application/json
    \Primal\ErrorHandler::Init();
    
    //override with a custom callback to write to a log file
    \Primal\ErrorHandler::Init(function ($error) {
        error_log(json_encode($error), 0, '/path/to/error/log');
    });