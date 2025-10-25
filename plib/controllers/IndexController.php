<?php
class IndexController extends pm_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->pageTitle = 'Plesk Guardian — Consola Centralizada de Logs';
    }

    public function indexAction()
    {
        $entries = [];

        $domains = [];
        $cli = @pm_ApiCli::call('site', ['-l']);
        if (isset($cli['stdout'])) {
            $domains = array_values(array_filter(array_map('trim', explode("\n", $cli['stdout']))));
        }

        foreach ($domains as $domain) {
            $domain = trim($domain);
            if ($domain === '') continue;

            // Ruta estándar de logs en Plesk
            $logsDir = "/var/www/vhosts/system/{$domain}/logs";
            $errorLog = "{$logsDir}/error_log";

            // Verificar si el archivo error_log existe y es legible
            if (is_file($errorLog) && is_readable($errorLog)) {

                // Intentar leer las últimas 50 líneas del error_log sin usar shell
                $lines = file($errorLog, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                if ($lines) {
                    // Tomar solo las últimas 50 líneas
                    $linesArr = array_slice($lines, -50);

                    foreach ($linesArr as $line) {
                        if ($line === '') continue;

                        // Extraer datos como la fecha, el nivel de severidad y el mensaje
                        preg_match('/(\d{4}-\d{2}-\d{2}[\sT]\d{2}:\d{2}:\d{2}).*(INFO|WARN|ERROR|NOTICE|DEBUG)?/i', $line, $m);
                        $timestamp = $m[1] ?? date('Y-m-d H:i:s');
                        $level = strtoupper($m[2] ?? 'INFO');

                        // Agregar el log a la lista de entradas
                        $entries[] = [
                            'domain'   => $domain,
                            'datetime' => $timestamp,
                            'severity' => $level,
                            'event'    => 'error_log',
                            'message'  => $line,
                        ];
                    }
                }
            }
        }

        usort($entries, function ($a, $b) {
            return strcmp($b['datetime'], $a['datetime']);
        });

        $this->view->entries = $entries;
    }
}
