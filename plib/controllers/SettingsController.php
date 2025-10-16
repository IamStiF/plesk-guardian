<?php
class SettingsController extends pm_Controller_Action
{
    public function init()
    {
        parent::init();
        $this->view->pageTitle = 'Plesk Guardian — Ajustes';
    }

    public function indexAction()
    {
        $form = new pm_Form_Simple();
        $form->addElement('text', 'vhostsPath', [
            'label' => 'Ruta base de vhosts',
            'value' => pm_Settings::get('vhostsPath', '/var/www/vhosts'),
            'required' => true,
        ]);
        $form->addControlButtons([
            'cancelLink' => pm_Context::getModulesListUrl(),
        ]);

        if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
            $values = $form->getValues();
            pm_Settings::set('vhostsPath', rtrim($values['vhostsPath'], '/'));
            $this->_status->addMessage('info', 'Ajustes guardados.');
            $this->_helper->redirector('index', 'index'); // volver al dashboard
            return;
        }

        $this->view->form = $form;
    }
}
