(function($){
    'use strict';

    function updateUrl (paramName, paramValue) {
        var url =  window.location.href;
        var pattern = new RegExp('('+paramName+'=).*?(&|$)');
        var newUrl = url.replace(pattern,'$1' + paramValue + '$2');

        if (url.indexOf(paramName) == -1) {
            newUrl = newUrl + (newUrl.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue;
        }

        return newUrl;
    }

    $('[data-toggle="tooltip"]').tooltip();

    $('.datepicker-month').datepicker({
        'autoclose': true,
        'minViewMode': 'months'
    }).on('changeDate', function (e) {
        var date = new Date(e.date)
        window.location = updateUrl('date', date.getFullYear() + '-' + (date.getMonth() + 1));
    });

    $('.input-daterange').datepicker({
        'format': 'dd/mm/yyyy',
        'autoclose': true,
        'todayBtn': true
    });

    $('.filter').on('change', function () {
        $(this).closest('form').submit();
    });

    $('.facts-table').floatThead({
        useAbsolutePositioning: false
    });
})(jQuery);
