<?php

namespace fpcm\modules\nkorg\calendar\models;

class appointment extends \fpcm\model\abstracts\dataset {

    use \fpcm\module\tools;

    protected $id = 0;  
    protected $description = '';
    protected $datetime = 0;
    protected $pending = 0;
    protected $visible = 0;
    protected $createtime = 0;
    protected $createuser = 0;

    public function __construct($id = null)
    {
        $this->table = $this->getObject()->getFullPrefix('appointments');
        return parent::__construct($id);
    }

    protected function getEventModule(): string
    {
        return '';
    }

    public function getEditLink()
    {
        return \fpcm\classes\tools::getControllerLink('calendar/edit', [
            'id' => $this->getId()
        ]);
    }

    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    public function setDatetime($datetime) {
        $this->datetime = (int) $datetime;
        return $this;
    }

    public function setPending($pending) {
        $this->pending = (int) $pending;
        return $this;
    }

    public function setVisible($visible) {
        $this->visible = (int) $visible;
        return $this;
    }

    public function setCreatetime($createtime) {
        $this->createtime = (int) $createtime;
        return $this;
    }

    public function setCreateuser($createuser) {
        $this->createuser = (int) $createuser;
        return $this;
    }
    
    public function getDescription() {
        return $this->description;
    }

    public function getDatetime() {
        return $this->datetime;
    }

    public function getPending() {
        return $this->pending;
    }

    public function getVisible() {
        return $this->visible;
    }

    public function getCreatetime() {
        return $this->createtime;
    }

    public function getCreateuser() {
        return $this->createuser;
    }
    
    public function save()
    {
        if (!$this->dbcon->insert($this->table, $this->getPreparedSaveParams())) {
            return false;
        }

        (new \fpcm\classes\cache())->cleanup(appointments::CACHE_NAME);
        $this->id = $this->dbcon->getLastInsertId();
        return $this->id;
    }

    public function update()
    {
        $params = $this->getPreparedSaveParams();        
        $params[] = $this->getId();
        
        if (!$this->dbcon->update($this->table, array_slice(array_keys($params), 0, -1), array_values($params), 'id = ?')) {
            (new \fpcm\classes\cache())->cleanup(appointments::CACHE_NAME);
            return false;
        }

        (new \fpcm\classes\cache())->cleanup(appointments::CACHE_NAME);
        return true;
    }


}
