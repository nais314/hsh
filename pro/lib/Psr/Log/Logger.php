<?php

namespace Psr\Log;

/**
 * Describes a logger instance
 *
 * The message MUST be a string or object implementing __toString().
 *
 * The message MAY contain placeholders in the form: {foo} where foo
 * will be replaced by the context data in key "foo".
 *
 * The context array can contain arbitrary data, the only assumption that
 * can be made by implementors is that if an Exception instance is given
 * to produce a stack trace, it MUST be in a key named "exception".
 *
 * See https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
 * for the full interface specification.
 */

include 'LoggerInterface.php';
include 'LogLevel.php'; 
 
class Logger implements LoggerInterface
{
	
	/** quick and dirty fun */
	protected function echoit($message) {
		echo $message;
	}
	
    /**
     * System is unusable.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function emergency($message, array $context = array(), $level = Psr\Log\LogLevel::EMERGENCY)
    {
    //echoit($message);
    $log = "[" . date("Y-m-d H:i:s") . " " . vsprintf("%-10s",   $level) . "] " . $message . "\n";
		file_put_contents('pro/log/log_'.date("Y-m-d").'.txt', $log, FILE_APPEND); 
    }
    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function alert($message, array $context = array(), $level = Psr\Log\LogLevel::ALERT)
    {
    //echoit($message);
    $log = "[" . date("Y-m-d H:i:s") . " " . vsprintf("%-10s",   $level) . "] " . $message . "\n";
		file_put_contents('pro/log/log_'.date("Y-m-d").'.txt', $log, FILE_APPEND); 
    }
    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function critical($message, array $context = array(), $level = Psr\Log\LogLevel::CRITICAL)
    {
    //echoit($message);
    $log = "[" . date("Y-m-d H:i:s") . " " . vsprintf("%-10s",   $level) . "] " . $message . "\n";
		file_put_contents('pro/log/log_'.date("Y-m-d").'.txt', $log, FILE_APPEND); 
    }
    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function error($message, array $context = array(), $level = Psr\Log\LogLevel::ERROR)
    {
    //echoit($message);
    $log = "[" . date("Y-m-d H:i:s") . " " . vsprintf("%-10s",   $level) . "] " . $message . "\n";
		file_put_contents('pro/log/log_'.date("Y-m-d").'.txt', $log, FILE_APPEND); 
    }
    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function warning($message, array $context = array(), $level = Psr\Log\LogLevel::WARNING)
    {
    //echoit($message);
    $log = "[" . date("Y-m-d H:i:s") . " " . vsprintf("%-10s",   $level) . "] " . $message . "\n";
		file_put_contents('pro/log/log_'.date("Y-m-d").'.txt', $log, FILE_APPEND); 
    }
    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function notice($message, array $context = array(), $level = Psr\Log\LogLevel::NOTICE)
    {
    //echoit($message);
    $log = "[" . date("Y-m-d H:i:s") . " " . vsprintf("%-10s",   $level) . "] " . $message . "\n";
		file_put_contents('pro/log/log_'.date("Y-m-d").'.txt', $log, FILE_APPEND);  
    }
    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function info($message, array $context = array(), $level = Psr\Log\LogLevel::INFO)
    {
    //echoit($message);
    $log = "[" . date("Y-m-d H:i:s") . " " . vsprintf("%-10s",   $level) . "] " . $message . "\n";
		file_put_contents('pro/log/log_'.date("Y-m-d").'.txt', $log, FILE_APPEND); 
    }
    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array $context
     * @return null
     */
    public function debug($message, array $context = array(), $level = LogLevel::DEBUG)
    {
    //echoit($message);
    $log = "[" . date("Y-m-d H:i:s") . " " . vsprintf("%-10s",   $level) . "] " . $message . "\n";
		file_put_contents('pro/log/log_'.date("Y-m-d").'.txt', $log, FILE_APPEND);  
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
		//echo ($level . $message . '\n');
		$log = "[" . date("Y-m-d H:i:s") . " " . vsprintf("%-10s",   $level) . "] " . $message . "\n";
		file_put_contents('pro/log/log_'.date("Y-m-d").'.txt', $log, FILE_APPEND);
    }
}

