<?php
class Modules_PleskGuardian_GuardianHelper
{
    public static function getWPInstances()
    {
        $cmd = new pm_ApiCli('wp-toolkit', ['--list']);
        $result = $cmd->run();
        return $result['stdout'] ?? 'No WordPress instances found';
    }
}
