<?php
// plib/hooks/Navigation.php
class Modules_PleskGuardian_Navigation extends pm_Hook_Navigation
{
    public function getNavigation()
    {
        return [[
            // Coloca el link en el menú de la izquierda (sección Extensions)
            'place'      => self::PLACE_NAVIGATION,
            'label'      => 'Plesk Guardian',
            'controller' => 'index',
            'action'     => 'index',
            'priority'   => 50,
        ]];
    }
}
