<?php
namespace Guardian;

class Scanner
{
    public static function findWpInstallations(string $root): array
    {
        $root = rtrim($root, '/');
        $results = [];
        if (!is_dir($root)) {
            return $results;
        }

        try {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            $seen = [];
            foreach ($iterator as $file) {
                if (strtolower($file->getFilename()) === 'wp-config.php') {
                    $path = $file->getPath();
                    if (!isset($seen[$path])) {
                        $results[] = ['path' => $path];
                        $seen[$path] = true;
                    }
                }
            }
        } catch (\Exception $e) {
            \pm_Log::warn('Scanner error: ' . $e->getMessage());
        }

        usort($results, fn($a, $b) => strcmp($a['path'], $b['path']));
        return $results;
    }
}
