<?php
class IndexController extends pm_Controller_Action
{
    public function init()
    {
        parent::init();
    }

    public function indexAction()
    {
        $entries = [];

        // 1️⃣ Obtener todos los dominios registrados en Plesk
        $domains = [];
        $cli = @pm_ApiCli::call('site', ['-l']);
        if (isset($cli['stdout'])) {
            $domains = array_filter(array_map('trim', explode("\n", $cli['stdout'])));
        }

        // 2️⃣ Leer logs de cada dominio (varios ficheros: error/access/proxy...)
        foreach ($domains as $domain) {
            // obtener rutas conocidas para este dominio desde LogService
            $paths = \Guardian\LogService::pathsFor($domain);

            foreach ($paths as $label => $path) {
                if (!is_file($path) || !is_readable($path)) {
                    continue;
                }

                // Leer solo las últimas 500 líneas por fichero
                $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                if (!$lines) continue;

                $lines = array_slice($lines, -500); // limitar cantidad
                foreach ($lines as $line) {
                    $parsed = $this->parseLogLine($line);
                    $parsed['domain'] = $domain;
                    $parsed['log_label'] = $label; // p.ej. apache_error, nginx_access
                    $parsed['log_file'] = basename($path);
                    $entries[] = $parsed;
                }
            }
        }

        // 3️⃣ Ordenar por fecha descendente (más nuevo primero)
        usort($entries, function ($a, $b) {
            return strtotime($b['timestamp']) <=> strtotime($a['timestamp']);
        });

        $this->view->entries = $entries;
    }

    /**
     * 🔍 Parsear una línea de log estilo Apache.
     * Ejemplo:
     * [Fri Oct 10 21:30:21.076138 2025] [ssl:error] [pid 33893:tid 130510416238272]
     * [client 177.93.4.195:0] AH02032: Hostname default-172_26_6_175...
     */
    private function parseLogLine(string $line): array
    {
        $result = [
            'timestamp' => date('Y-m-d H:i:s'),
            'module' => null,
            'level' => null,
            'pid' => null,
            'tid' => null,
            'client' => null,
            'code' => null,
            'message' => trim($line),
        ];

        // Extraer timestamp
        if (preg_match('/^\[([^\]]+)\]/', $line, $m)) {
            $ts = $m[1];
            $dt = DateTime::createFromFormat('D M d H:i:s.u Y', $ts)
                ?: DateTime::createFromFormat('D M d H:i:s Y', $ts);
            if ($dt) {
                $result['timestamp'] = $dt->format('Y-m-d H:i:s');
            }
        }

        // Extraer módulo y nivel (ej: [ssl:error])
        if (preg_match('/\[([a-z0-9_-]+):([a-z]+)\]/i', $line, $m)) {
            $result['module'] = $m[1];
            $result['level'] = strtoupper($m[2]);
        }

        // Extraer pid/tid (ej: [pid 33893:tid 130510416238272])
        if (preg_match('/pid\s+([0-9]+)(?::tid\s*([0-9:]+))?/i', $line, $m)) {
            $result['pid'] = $m[1] ?? null;
            $result['tid'] = $m[2] ?? null;
        }

        // Extraer cliente (ej: [client 177.93.4.195:0])
        if (preg_match('/client\s+([0-9.:]+)/i', $line, $m)) {
            $result['client'] = $m[1] ?? null;
        }

        // Extraer código (ej: AH02032:)
        if (preg_match('/\s([A-Z]{2}[0-9]+):\s*(.*)$/', $line, $m)) {
            $result['code'] = $m[1];
            $result['message'] = trim($m[2]);
        }

        return $result;
    }
}
