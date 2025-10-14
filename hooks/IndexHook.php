<?php
class Modules_PleskGuardian_IndexHook extends pm_Hook_Action
{
    public function onActivate()
    {
        pm_Log::info('Plesk Guardian activated.');
    }

    public function onDeactivate()
    {
        pm_Log::info('Plesk Guardian deactivated.');
    }
}
