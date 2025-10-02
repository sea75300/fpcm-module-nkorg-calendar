<?php

namespace fpcm\modules\nkorg\calendar\models;

class dashContainer extends \fpcm\model\abstracts\dashcontainer {

    use \fpcm\module\tools;

    /**
     * Container chart
     * @var \fpcm\components\charts\chart
     */
    private $chart;

    /**
     * Poll for chart
     * @var poll
     */
    private $poll = false;

    private int $start;

    private int $stop;

    public function getContent() : string
    {
        $this->start = mktime(0, 0, 0);
        $this->stop = mktime(23, 59, 59);

        $search = new \fpcm\modules\nkorg\calendar\models\search();
        $search->start = mktime(0,0,0);
        $search->limit = $this->getObject()->getOption('dashboard_items');
        $search->offset = 0;
        $search->order = 'datetime ASC';

        $appointments = (new \fpcm\modules\nkorg\calendar\models\appointments)->getAppointments($search);

        if (!count($appointments)) {
            return $this->language->translate('GLOBAL_NOTFOUND2');
        }
        
        $html = ['<div class="list-group me-2">'];
        /* @var $appointment \fpcm\modules\nkorg\calendar\models\appointment */
        foreach ($appointments as $appointment) {
            
            $dt = $appointment->getDatetime();

            $html[] = sprintf(
                '<a class="list-group-item list-group-item-action %s" href="%s"><span class="d-flex"><span class="me-1">%s</span><span class="text-light-emphasis">%s</span><span class="ms-auto">%s %s</span></span><span class="me-3">%s</span></a>',
                $this->isCurrent($dt) . (!$appointment->getVisible() ? ' list-group-item-danger' : ''),
                $appointment->getEditLink(),
                (string) (new \fpcm\view\helper\icon('edit')),
                (new \fpcm\view\helper\dateText($appointment->getDatetime(), $appointment->getPending() ? 'M / Y' : 'd.m.Y')),
                (new \fpcm\view\helper\boolToText('status'.$appointment->getId()))->setValue($appointment->getPending())->setText($this->addLangVarPrefix('GUI_APPOINTMENT_PENDING')),
                (new \fpcm\view\helper\boolToText('visible'.$appointment->getId()))->setValue($appointment->getVisible())->setText($this->addLangVarPrefix('GUI_APPOINTMENT_VISIBLE')),
                (new \fpcm\view\helper\escape($appointment->getDescription())),
            );            
        }

        $html[] = '</div>';
        
        return implode(PHP_EOL, $html);
    }

    public function getHeadline() : string
    {
        return $this->language->translate(
            $this->addLangVarPrefix('HEADLINE_DASHBOARD'),
            [
                (string) (new \fpcm\view\helper\icon('calendar-day'))
            ],
            true
        );
    }

    public function getName() : string
    {
        return 'nkorg_calendar_recentpoll';
    }

    public function getPosition()
    {
        return self::DASHBOARD_POS_MAX;
    }

    private function isCurrent(int $dt) : string
    {
        return $dt >= $this->start && $dt <= $this->stop ? 'list-group-item-warning' : '';
    }

}
