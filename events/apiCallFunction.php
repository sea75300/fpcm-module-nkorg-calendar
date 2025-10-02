<?php

namespace fpcm\modules\nkorg\calendar\events;

final class apiCallFunction extends \fpcm\module\event {

    private int $start;

    private int $stop;

    public function run()
    {
        $fn = $this->data['name'];
        if (!method_exists($this, $fn)) {
            trigger_error('Function '.$fn.' does not exists!');
            return false;
        }

        call_user_func([$this, $fn],$this->data['args']);
        return true;
    }

    public function init()
    {
        return true;
    }

    final protected function display()
    {
        $this->start = mktime(0, 0, 0);
        $this->stop = mktime(23, 59, 59);

        $cache = new \fpcm\classes\cache();

        if (!$cache->isExpired(\fpcm\modules\nkorg\calendar\models\appointments::CACHE_NAME)) {
            print $cache->read(\fpcm\modules\nkorg\calendar\models\appointments::CACHE_NAME);
            return true;
        }


        $search = new \fpcm\modules\nkorg\calendar\models\search();
        $search->start = mktime(0,0,0);
        $search->stop = mktime(23,59,59) + $this->getObject()->getOption('frontend_days') * FPCM_DATE_SECONDS;
        $search->visible = 1;

        $appointments = (new \fpcm\modules\nkorg\calendar\models\appointments)->getAppointments($search);
        if (!count($appointments)) {
            print '<p>'.\fpcm\classes\loader::getObject('\fpcm\classes\language')->translate($this->addLangVarPrefix('MSG_ERROR_NOTFOUND_PUB')).'</p>';
            return true;
        }

        $html = ['<ul>'];
        /* @var $appointment \fpcm\modules\nkorg\calendar\models\appointment */
        foreach ($appointments as $appointment) {

            $dt = $appointment->getDatetime();

            $html[] = sprintf(
                '<li class="%s"><strong>%s</strong> %s</li>',
                $this->isCurrent($dt),
                (new \fpcm\view\helper\dateText($appointment->getDatetime(), $appointment->getPending() ? 'M / Y' : 'd.m.Y')),
                (new \fpcm\view\helper\escape($appointment->getDescription()))
            );
        }

        $html[] = '</ul>';

        $res = implode(PHP_EOL, $html);

        $cache->write(\fpcm\modules\nkorg\calendar\models\appointments::CACHE_NAME, $res);

        print $res;
        return true;
    }

    private function isCurrent(int $dt) : string
    {
        return $dt >= $this->start && $dt <= $this->stop ? 'fpcm-calendar-current-item' : '';
    }

}
