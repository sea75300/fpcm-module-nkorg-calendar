<?php

namespace fpcm\modules\nkorg\calendar\events;

final class dashboardContainersLoad extends \fpcm\module\event {

    public function run()
    {
        $this->data[] = '\fpcm\modules\nkorg\calendar\models\dashContainer';
        return $this->data;
    }

    public function init() : bool
    {
        return true;
    }

}
