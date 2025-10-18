<?php
class IndexController extends pm_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->pageTitle = 'Plesk Guardian — Dashboard';
    }

    public function indexAction()
    {
        $vhostsPath = pm_Settings::get('vhostsPath', '/var/www/vhosts');
        $fetchVersion = (bool)$this->_getParam('fetchVersion', false);

        $sites = \Guardian\Scanner::findWpInstallations($vhostsPath);

        if ($fetchVersion) {
            foreach ($sites as &$s) {
                $s['version'] = \Guardian\Commander::wpVersion($s['path']);
            }
        }

        $this->view->vhostsPath = $vhostsPath;
        $this->view->sites = $sites;
        $this->view->fetchVersion = $fetchVersion;
    }
}
