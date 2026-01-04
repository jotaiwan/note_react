<?php
// xdebug_stub.php
// 仅供 IDE / 静态分析使用，运行时如果 Xdebug 已安装会被忽略

if (!function_exists('xdebug_break')) {
    /**
     * @return void
     */
    function xdebug_break() {}
}

if (!function_exists('xdebug_get_function_stack')) {
    function xdebug_get_function_stack() { return []; }
}

if (!function_exists('xdebug_var_dump')) {
    function xdebug_var_dump(...$vars) { var_dump(...$vars); }
}
