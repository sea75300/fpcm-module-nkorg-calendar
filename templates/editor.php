<?php /* @var $theView \fpcm\view\viewVars */ ?>
<?php /* @var $appointment fpcm\modules\nkorg\calendar\models\appointment */ ?>
<div class="row row-cols-2 pt-3">
    
    <div class="col">
        <?php $theView
                ->dateTimeInput("appointmentdata[date]")
                ->setText('MODULE_NKORGCALENDAR_GUI_APPOINTMENT_DATETIME')
                ->setValue(date('Y-m-d', $appointment->getDatetime())); ?>
        
    </div>
    
    <div class="col">
        <?php $theView
                ->dateTimeInput("appointmentdata[time]")
                ->setText('')
                ->setValue(date('H:i', $appointment->getDatetime()))
                ->setClass('ml-1')
                ->setNativeTime(); ?>
    </div>

</div>

<div class="row">
    <?php $theView
            ->textInput("appointmentdata[description]")
            ->setText('Beschreibung')
            ->setSize(255)
            ->setValue($appointment->getDescription())
            ->setWrapper(false)
            ->setDisplaySizesDefault(); ?>
</div>

<div class="row">
    <?php $theView
            ->boolSelect("appointmentdata[pending]")
            ->setValue(1)
            ->setSelected($appointment->getPending())
            ->setText('MODULE_NKORGCALENDAR_GUI_APPOINTMENT_PENDING'); ?>
</div>

<div class="row">
    <?php $theView
            ->boolSelect("appointmentdata[visible]")
            ->setValue(1)
            ->setSelected($appointment->getVisible())
            ->setText('MODULE_NKORGCALENDAR_GUI_APPOINTMENT_VISIBLE'); ?>
</div>
