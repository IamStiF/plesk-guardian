<?php
namespace Guardian;

class LogService
{
    public static function listDomains(): array
    {
        $out = \pm_ApiCli::call('site', ['-l']); // plesk bin site -l
        $lines = preg_split('/\r?\n/', trim($out['stdout']));
        return array_values(array_filter(array_map('trim', $lines)));
    }

    public static function pathsFor(string $domain): array
    {
        $base = "/var/www/vhosts/system/{$domain}/logs";
        return [
            'apache_access' => "{$base}/access_log",
            'apache_error'  => "{$base}/error_log",
            'nginx_access'  => "{$base}/proxy_access_log",
            'php_error'     => "{$base}/php_error.log",
        ];
    }

    public static function tail(string $file, int $lines = 500): array
    {
        if (!is_readable($file)) return [];
        $fp = @fopen($file, 'rb');
        if (!$fp) return [];

        $buffer = '';
        $chunk  = 8192;
        fseek($fp, 0, SEEK_END);
        $pos = ftell($fp);

        $found = 0;
        while ($pos > 0 && $found <= $lines) {
            $read = ($pos - $chunk > 0) ? $chunk : $pos;
            $pos -= $read;
            fseek($fp, $pos, SEEK_SET);
            $buffer = fread($fp, $read) . $buffer;
            $found = substr_count($buffer, "\n");
        }
        fclose($fp);

        $arr = explode("\n", rtrim($buffer, "\n"));
        return array_slice($arr, -$lines);
    }

    public static function countErrorsLastMinutes(string $file, int $minutes = 60): int
    {
        if (!is_readable($file)) return 0;
        $lines = self::tail($file, 5000); // heurística
        $limit = time() - ($minutes * 60);
        $count = 0;

        foreach ($lines as $ln) {
            // cuenta líneas con "error" o con fecha > límite
            if (stripos($ln, 'error') !== false) {
                // Si el formato es “[dd/Mon/yyyy:HH:MM:SS” puedes parsearlo mejor
                $count++;
            }
        }
        return $count;
    }
}
