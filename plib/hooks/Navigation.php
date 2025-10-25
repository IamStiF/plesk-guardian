<?php

class Modules_PleskGuardian_Navigation extends pm_Hook_Navigation
{
    // En algunas versiones es getItems(), en otras getNavigation().
    // Se definen ambos y se reutiliza el mismo arreglo para ser compatible.
    private function items()
    {
        return [[
            'place'    => self::PLACE_NAVIGATION,                 // menú izquierdo
            'label'    => 'Plesk Guardian',
            //'icon'   => pm_Context::getBaseUrl() . 'img/icon.svg', 
            'link'     => pm_Context::getBaseUrl() . 'index/index', // controlador/acción
            'priority' => 50,
        ]];
    }

    public function getNavigation()
    {
        return $this->items();
    }

    public function getItems()
    {
        return $this->items();
    }
}
