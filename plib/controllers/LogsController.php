<?php
class LogsController extends pm_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->pageTitle = 'Plesk Guardian — Consola Centralizada de Logs';
    }

    public function indexAction()
    {
        $domains = [];
        $cli = @pm_ApiCli::call('site', ['-l']);
        if (isset($cli['stdout'])) {
            $domains = array_values(array_filter(array_map('trim', explode("\n", $cli['stdout']))));
        }

        $entries = [];
        foreach ($domains as $domain) {
            $domain = trim($domain);
            if ($domain === '') continue;

            $info = @pm_ApiCli::call('site', ['--info', $domain]);
            $stdout = $info['stdout'] ?? '';
            $docRoot = '';

            foreach (explode("\n", $stdout) as $line) {
                if (stripos($line, 'Document root') !== false) {
                    $docRoot = trim(explode(':', $line, 2)[1] ?? '');
                    break;
                }
            }

            if ($docRoot === '') continue;

            $logsDir = dirname($docRoot) . '/logs';
            if (!is_dir($logsDir)) continue;

            foreach (glob("$logsDir/*.log") as $file) {
                $lines = @shell_exec("tail -n 30 " . escapeshellarg($file));
                if (!$lines) continue;

                $fileBase = basename($file);
                foreach (explode("\n", trim($lines)) as $line) {
                    if ($line === '') continue;

                    // Detectar fecha y nivel (básico)
                    preg_match('/(\d{4}-\d{2}-\d{2}[\sT]\d{2}:\d{2}:\d{2}).*(INFO|WARN|ERROR|NOTICE|DEBUG)?/i', $line, $m);
                    $timestamp = $m[1] ?? '';
                    $level = strtoupper($m[2] ?? 'INFO');

                    $entries[] = [
                        'domain'   => $domain,
                        'datetime' => $timestamp,
                        'severity' => $level,
                        'event'    => $fileBase,
                        'actor'    => 'System',
                        'message'  => $line,
                    ];
                }
            }
        }

        // Ordenar por fecha descendente
        usort($entries, fn($a, $b) => strcmp($b['datetime'], $a['datetime']));

        $this->view->entries = $entries;
    }
}
