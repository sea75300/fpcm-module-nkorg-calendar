if (fpcm === undefined) {
    var fpcm = {};
}

fpcm.calendar = {

    init: function () {

        if (fpcm.dataview !== undefined) {
            fpcm.dataview.render('nkorgcalendar');
        }

    }

};
