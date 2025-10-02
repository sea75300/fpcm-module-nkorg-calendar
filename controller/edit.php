<?php

namespace fpcm\modules\nkorg\calendar\controller;

final class edit extends base {

    public function request()
    {
        $id = $this->request->getID();
        if (!$id) {
            $this->view = new \fpcm\view\error($this->addLangVarPrefix('MSG_ERROR_NOTFOUND'));
            return false;
        }

        $this->appointment = new \fpcm\modules\nkorg\calendar\models\appointment($id);
        if (!$this->appointment->exists()) {
            $this->view = new \fpcm\view\error($this->addLangVarPrefix('MSG_ERROR_NOTFOUND'));
        }

        return true;
    }

    public function process()
    {
        $this->view->addButtons([
            (new \fpcm\view\helper\linkButton('backList'))->setUrl(\fpcm\classes\tools::getControllerLink('calendar/overview')) ->setText($this->addLangVarPrefix('GUI_GOtO_OVERVIEW'))->setIcon('arrow-circle-left'),
        ]);

        $this->view->setFormAction('calendar/edit&id='.$this->appointment->getId());

        parent::process();
    }

}
