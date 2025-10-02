<?php

namespace fpcm\modules\nkorg\calendar\controller;

final class overview extends \fpcm\controller\abstracts\module\controller {

    use \fpcm\controller\traits\common\dataView;

    const ROW_STYLE_CURRENT = 'bg-warning-subtle';

    private int $start;

    private int $stop;


    /**
     *
     * @var \fpcm\components\dataView\dataView
     */
    protected $dataView;

    public function process()
    {
        $this->start = mktime(0, 0, 0);
        $this->stop = mktime(23, 59, 59);

        $this->delete();

        $this->view->addButtons([
            (new \fpcm\view\helper\linkButton('appointmentAdd'))->setText($this->addLangVarPrefix('GUI_APPOINTMENT_ADD'))->setIcon('calendar-plus')->setUrl(\fpcm\classes\tools::getControllerLink('calendar/add')),
            (new \fpcm\view\helper\deleteButton('appointmentDelete'))->setIcon('calendar-minus'),
        ]);

        $this->view->addJsVars([
            'polls' => [],
        ]);

        $this->view->addJsFiles([
            \fpcm\module\module::getJsDirByKey($this->getModuleKey(), 'module.js')
        ]);

        $this->items = (new \fpcm\modules\nkorg\calendar\models\appointments)->getAppointments();
        $this->initDataView();

        $this->view->addTabs('calendar', [
            (new \fpcm\view\helper\tabItem('main'))
                ->setText($this->addLangVarPrefix('HEADLINE'))
                ->setFile( \fpcm\view\view::PATH_COMPONENTS . 'dataview__inline.php' )
        ]);

        $this->view->setFormAction('calendar/overview');
        $this->view->render();
        return true;
    }

    private function delete() : bool {

        if (!$this->buttonClicked('appointmentDelete')) {
            return true;
        }

        $id = $this->request->fromPOST('id', [
            \fpcm\model\http\request::FILTER_CASTINT
        ]);

        if (!$id) {
            return false;
        }

        $appointment = new \fpcm\modules\nkorg\calendar\models\appointment($id);
        if (!$appointment->exists()) {
            $this->view->addErrorMessage($this->addLangVarPrefix('MSG_ERROR_NOTFOUND'));
            return false;
        }

        if (!$appointment->delete()) {
            $this->view->addErrorMessage($this->addLangVarPrefix('MSG_ERROR_DELETE'));
            return false;
        }

        return true;
    }

    protected function getDataViewCols(): array
    {
        return [
            (new \fpcm\components\dataView\column('select', ''))->setSize(1)->setAlign('center'),
            (new \fpcm\components\dataView\column('button', ''))->setSize(1)->setAlign('center'),
            (new \fpcm\components\dataView\column('name', $this->addLangVarPrefix('GUI_APPOINTMENT_DESCRIPTION')))->setSize(4),
            (new \fpcm\components\dataView\column('time', $this->addLangVarPrefix('GUI_APPOINTMENT_DATETIME')))->setSize(4)->setAlign('center'),
            (new \fpcm\components\dataView\column('status', $this->addLangVarPrefix('GUI_APPOINTMENT_PENDING')))->setSize(1)->setAlign('center'),
            (new \fpcm\components\dataView\column('visible', $this->addLangVarPrefix('GUI_APPOINTMENT_VISIBLE')))->setSize(1)->setAlign('center'),
        ];

    }

    /**
     *
     * @param \fpcm\modules\nkorg\calendar\models\appointment $appointment
     * @return \fpcm\components\dataView\row
     */
    protected function initDataViewRow($appointment)
    {
        $dt = $appointment->getDatetime();

        return new \fpcm\components\dataView\row([
            new \fpcm\components\dataView\rowCol('select', (new \fpcm\view\helper\radiobutton('id', 'chbx' . $appointment->getId()))->setValue($appointment->getId()), '', \fpcm\components\dataView\rowCol::COLTYPE_ELEMENT),
            new \fpcm\components\dataView\rowCol('button', (new \fpcm\view\helper\editButton('edit'.$appointment->getId()))->setUrlbyObject($appointment) ),
            new \fpcm\components\dataView\rowCol('name', $appointment->getDescription() ),
            new \fpcm\components\dataView\rowCol('time', new \fpcm\view\helper\dateText($dt, 'd.m.Y H:i') ),
            new \fpcm\components\dataView\rowCol('status', (new \fpcm\view\helper\boolToText('status'.$appointment->getId()))->setValue($appointment->getPending()) ),
            new \fpcm\components\dataView\rowCol('visible', (new \fpcm\view\helper\boolToText('visible'.$appointment->getId()))->setValue($appointment->getVisible()) )
        ], $this->isCurrent($dt));
    }

    protected function getDataViewName() {
        return 'nkorgcalendar';
    }

    protected function getViewPath() : string
    {
        return 'dataview';
    }

    public function isAccessible(): bool
    {
        return true;
    }

    private function isCurrent(int $dt) : string
    {
        return $dt >= $this->start && $dt <= $this->stop ? self::ROW_STYLE_CURRENT : '';
    }

}
