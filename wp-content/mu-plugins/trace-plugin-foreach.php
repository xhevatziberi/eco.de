<?php
/**
 * Plugin Name: Trace foreach() warning in plugin.php
 * Description: Logs a backtrace when wp-admin/includes/plugin.php:1853 throws the foreach warning.
 */

if ( ! defined('WP_DEBUG') || ! WP_DEBUG ) {
    // Ensure errors are sent to the log
    @ini_set('log_errors', '1');
}

set_error_handler(function($errno, $errstr, $errfile, $errline) {
    // Only care about warnings from plugin.php line 1853 (adjust line if your core file differs by a few lines)
    if ($errno === E_WARNING && strpos($errstr, 'foreach() argument must be of type array|object, null given') !== false
        && strpos($errfile, 'wp-admin/includes/plugin.php') !== false
        && (int)$errline >= 1845 && (int)$errline <= 1865 // small cushion
    ) {
        error_log('=== TRACE foreach null in plugin.php ===');
        // Nice, readable backtrace without args
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($trace as $i => $frame) {
            $func = ($frame['class'] ?? '') . ($frame['type'] ?? '') . ($frame['function'] ?? '');
            $file = $frame['file'] ?? '(no file)';
            $line = $frame['line'] ?? '(no line)';
            error_log(sprintf('#%d %s at %s:%s', $i, $func, $file, $line));
        }
        error_log('=== END TRACE ===');
    }
    // Let normal handling continue
    return false;
});
