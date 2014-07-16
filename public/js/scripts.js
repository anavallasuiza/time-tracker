(function($){
    'use strict';

    $('[data-toggle="tooltip"]').tooltip();

    $('.input-daterange').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: true,
        weekStart: 1
    });

    $('.filter').on('change', function () {
        $(this).closest('form').submit();
    });

    $('.facts-table').floatThead({
        useAbsolutePositioning: false
    });
})(jQuery);
