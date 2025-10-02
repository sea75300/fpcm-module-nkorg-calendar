<?php

namespace fpcm\modules\nkorg\calendar\events\cron;

final class includeDumpTables extends \fpcm\module\event {

    public function run()
    {
        /* @var $db \fpcm\classes\database */
        $db = \fpcm\classes\loader::getObject('\fpcm\classes\database');

        $this->data[] = $db->getTablePrefixed($this->getObject()->getFullPrefix('appointments'));
        return $this->data;
    }

    public function init(): bool
    {
        return false;
    }

}