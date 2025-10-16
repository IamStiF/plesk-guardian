<?php
namespace Guardian;

class Commander
{
    public static function wpVersion(string $path): ?string
    {
        $path = rtrim($path, '/');
        if (!is_dir($path)) {
            return null;
        }
        $owner = trim(shell_exec("stat -c '%U' " . escapeshellarg($path)));
        if ($owner === '') {
            $owner = 'www-data';
        }
        $cmd = "sudo -u " . escapeshellarg($owner) . " wp core version --path=" . escapeshellarg($path) . " --skip-plugins --skip-themes 2>/dev/null";
        $out = shell_exec($cmd);
        return $out ? trim($out) : null;
    }
}
