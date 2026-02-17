<?php

namespace DoctorlyDashboard\Includes;

if (! defined('ABSPATH')) {
    exit;
}

class Autoloader
{
    public static function register(): void
    {
        spl_autoload_register([self::class, 'autoload']);
    }

    private static function autoload(string $class): void
    {
        if (strpos($class, 'DoctorlyDashboard\\') !== 0) {
            return;
        }

        $relative = strtolower(str_replace(['DoctorlyDashboard\\', '\\'], ['', '/'], $class));
        $file = DOCTORLY_DASHBOARD_PATH . $relative . '.php';

        if (file_exists($file)) {
            require_once $file;
        }
    }
}
