<?php
/**
 * @package ContentHubPlugin
 */

namespace CHPS_Includes\Base;

class Activate
{
    public static function activate()
    {
        flush_rewrite_rules();
    }
}
