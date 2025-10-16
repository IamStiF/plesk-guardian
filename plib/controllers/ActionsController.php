<?php
class ActionsController extends pm_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->_helper->viewRenderer->setNoRender(); // endpoints simples
    }

    private function json($data, $code = 200)
    {
        $this->getResponse()->setHttpResponseCode($code);
        $this->getResponse()->setHeader('Content-Type', 'application/json');
        echo json_encode($data);
    }

    // Stub: forzar HTTPS (placeholder)
    public function forceHttpsAction()
    {
        $path = $this->_getParam('path', '');
        if (!$path || strpos(realpath($path), '/var/www/vhosts') !== 0) {
            return $this->json(['ok' => false, 'error' => 'Ruta inválida'], 400);
        }
        // TODO: Implementar lógica real (configuración web, redirecciones)
        return $this->json(['ok' => true, 'message' => 'Forzar HTTPS (stub)']);
    }

    // Stub: reparar permisos (placeholder)
    public function fixPermissionsAction()
    {
        $path = $this->_getParam('path', '');
        if (!$path || strpos(realpath($path), '/var/www/vhosts') !== 0) {
            return $this->json(['ok' => false, 'error' => 'Ruta inválida'], 400);
        }
        // TODO: Implementar lógica real (find/chmod con extremo cuidado)
        return $this->json(['ok' => true, 'message' => 'Reparar permisos (stub)']);
    }
}
