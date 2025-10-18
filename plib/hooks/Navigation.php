<?php
class Navigation implements pm_Hook_Navigation
{
    public static function getNavigation()
    {
        return [[
            'place'    => self::PLACE_NAVIGATION,
            'label'    => 'Plesk Guardian',
            'link'     => pm_Context::getBaseUrl() . '/index/index',
            'priority' => 50,
        ]];
    }
}