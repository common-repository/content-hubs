<?php
/**
 * @package ContentHubPlugin
 */

namespace CHPS_Includes;

final class Init
{
    /**
     * Store all the classes inside an array
     * @return array Full list of classes
     */
    public static function get_services()
    {
        $classes = [
            Base\ContentHubController::class,
            Base\PostController::class,
            Base\Enqueue::class,
            Base\BlockController::class
        ];

        // only add Frontend class when not in backend
        if(!is_admin()) {
            array_push($classes, Pages\Frontend::class);
        }

        // PRO
        if(file_exists(dirname(__FILE__) . '/Pro/ProController__premium_only.php')) {
            // this is the PRO version
            array_unshift($classes, Pro\ProController__premium_only::class); // add class at first position, so that filters can be registered
        }

        return $classes; // NOTE: return [] will only work with php7
    }

    /**
     * Loop through the classes, initialize them and call method register() if it exists
     */
    public static function register_services()
    {
        foreach (self::get_services() as $class) {
            $service = self::instantiate($class);

            if (method_exists($service, 'register')) {
                $service->register();
            }
        }
    }

    /**
     * Initialize the class
     * @param $class class      class from the services array
     * @return class instance   new instance of the class
     */
    private static function instantiate($class)
    {
        $service = new $class();

        return $service;
    }
}
