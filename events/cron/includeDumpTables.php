<?php

namespace fpcm\modules\nkorg\calendar\events\cron;

final class includeDumpTables extends \fpcm\module\event {

    public function run() : \fpcm\module\eventResult
    {
        /* @var $db \fpcm\classes\database */
        $db = \fpcm\classes\loader::getObject('\fpcm\classes\database');

        $this->data[] = $db->getTablePrefixed($this->getObject()->getFullPrefix('appointments'));
        
        return (new \fpcm\module\eventResult())->setData($this->data);
    }

    public function init(): bool
    {
        return false;
    }

}
