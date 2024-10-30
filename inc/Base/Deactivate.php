<?php
/**
 * @package ContentHubPlugin
 */

namespace CHPS_Includes\Base;

class Deactivate
{
    public static function deactivate()
    {
        flush_rewrite_rules();
    }
}
