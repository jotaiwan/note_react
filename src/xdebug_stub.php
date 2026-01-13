<?php
// xdebug_stub.php
// For IDE/static analysis only; ignored at runtime if Xdebug is installed

if (!function_exists('xdebug_break')) {
    /**
     * @return void
     */
    function xdebug_break() {}
}

if (!function_exists('xdebug_get_function_stack')) {
    function xdebug_get_function_stack()
    {
        return [];
    }
}

if (!function_exists('xdebug_var_dump')) {
    function xdebug_var_dump(...$vars)
    {
        var_dump(...$vars);
    }
}
