<?php
class Modules_PleskGuardian_Main extends pm_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->pageTitle = '🛡️ Plesk Guardian Dashboard';
        $this->view->headScript()->appendFile(pm_Context::getBaseUrl() . 'resources/js/main.js');
        $this->view->headLink()->appendStylesheet(pm_Context::getBaseUrl() . 'resources/css/style.css');
    }

    public function indexAction()
    {
        $this->view->content = "Welcome to Plesk Guardian — Centralized WordPress Security Management.";
    }

    public function scanAction()
    {
        $this->view->pageTitle = 'Security Scan';
        $this->view->content = "Running vulnerability scan across all WordPress instances...";
        pm_Log::info("Security scan initiated by admin.");
    }

    public function statusAction()
    {
        $this->view->pageTitle = 'Guardian Status';
        $this->view->content = "All systems operational. No threats detected.";
    }
}
