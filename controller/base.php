<?php

namespace fpcm\modules\nkorg\calendar\controller;

class base extends \fpcm\controller\abstracts\module\controller
implements \fpcm\controller\interfaces\requestFunctions {

    /**
     *
     * @var \fpcm\modules\nkorg\calendar\models\appointment
     */
    protected $appointment;
    
    public function process()
    {
        $this->view->addButton(new \fpcm\view\helper\saveButton('save'));

        $this->view->addJsLangVars([
            $this->addLangVarPrefix('GUI_POLL_REPLY_TXT')
        ]);

        $this->view->addJsFiles([
            \fpcm\module\module::getJsDirByKey($this->getModuleKey(), 'module.js')
        ]);

        $this->view->addTabs('calendar', [
            (new \fpcm\view\helper\tabItem('editor'))
                ->setText($this->addLangVarPrefix('GUI_APPOINTMENT_TAB'))
                ->setModulekey($this->getModuleKey())
                ->setFile( \fpcm\view\view::PATH_MODULE . 'editor')
        ]);
        
        $this->view->assign('appointment', $this->appointment);        
        $this->view->render();
        return true;
    }
    
    protected function onSave()
    {
        $data = $this->request->fromPOST('appointmentdata', [
            \fpcm\model\http\request::FILTER_TRIM,
            \fpcm\model\http\request::FILTER_STRIPTAGS,
            \fpcm\model\http\request::FILTER_STRIPSLASHES
        ]);

        if (!is_array($data) || !count($data)) {
            $this->view->addErrorMessage($this->addLangVarPrefix('MSG_ERROR_INSERTDATA'));
            return false;
        }

        if (empty($data['description']) || empty($data['date']) || empty($data['time'])) {
            $this->view->addErrorMessage($this->addLangVarPrefix('MSG_ERROR_INSERTDATA'));
            return false;
        }

        $data['datetime'] = strtotime($data['date'].' '.$data['time']);

        if (defined('FPCM_MODULE_DEBUG_CALENDAR')) {
            fpcmLogSystem(__METHOD__);
            fpcmLogSystem($data);
            fpcmLogSystem('Test-DT: '.date('Y-m-d H:i', $data['datetime']));
        }

        if ($data['datetime'] === false || date('Y-m-d H:i', $data['datetime']) !== $data['date'].' '.$data['time']) {
            $this->view->addErrorMessage($this->addLangVarPrefix('MSG_ERROR_INSERTDATA_DT'));
            return false;
        }

        $this->appointment
                ->setDescription($data['description'])
                ->setDatetime($data['datetime'])
                ->setPending($data['pending'] ?? 0)
                ->setVisible($data['visible'] ?? 0);

        if (!$this->appointment->getId()) {

            $this->appointment->setCreatetime(time())->setCreateuser($this->session->getUserId());

            if (!$this->appointment->save()) {
                $this->view->addErrorMessage($this->addLangVarPrefix('MSG_ERROR_SAVE'));
                return false;
            }

            $this->redirect('calendar/edit', [
                'id' => $this->appointment->getId()
            ]);

            return true;
        }
        
        if (!$this->appointment->update()) {
            $this->view->addErrorMessage($this->addLangVarPrefix('MSG_ERROR_SAVE'));
            return false;
        }

        $this->view->addNoticeMessage($this->addLangVarPrefix('MSG_SUCCESS_SAVE'));
        return true;
    }

    public function isAccessible(): bool
    {
        return true;
    }
    
    protected function getViewPath() : string
    {
        return 'editor';
    }

}
