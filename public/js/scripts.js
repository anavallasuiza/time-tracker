var setFact = function () {
    localStorage.setItem('fact', $fact = JSON.stringify({
        activity: $addForm.find('select[name="activity"]').val(),
        tag: $addForm.find('select[name="tag"]').val(),
        start: $addForm.find('input[name="start"]').val(),
        end: $addForm.find('input[name="end"]').val(),
        description: $addForm.find('input[name="description"]').val()
    }));
};

var getFact = function () {
    var fact = localStorage.getItem('fact');

    if (fact) {
        return JSON.parse(fact);
    }
};

var $addForm = $('#facts-form-add'),
    $fact = getFact();

(function($){
    'use strict';

    var setValue = function ($after, $before, field, mask) {
        var $input = $after.find('input[name="' + field + '"]');

        $input.val($before.find('.column-' + field).text());

        if (mask) {
            $input.mask(mask);
        }

        return $input;
    };

    var setOption = function ($after, $before, field) {
        var value = $before.find('.column-' + field).text().toLowerCase().replace(/\W/g, '');

        return $after.find('select[name="' + field + '"] option').filter(function () {
            return ($(this).text().toLowerCase().replace(/\W/g, '') === value);
        }).prop('selected', true);
    };

    var refreshFact = function () {
        var time = moment().format('DD/MM/YYYY HH:mm');

        $addForm.find('select').val('');
        $addForm.find('input[name="time"]').val('');
        $addForm.find('input[name="description"]').val('');

        $addForm.find('[data-action="refresh"]').hide();
        $addForm.find('[data-action="play"]').show();
        $addForm.find('input[name="start"]').val(time);
        $addForm.find('input[name="end"]').val(time).trigger('change');

        localStorage.setItem('fact', $fact = null);

        clearInterval(timeCounter);
    };

    var saveFact = function ($form) {
        var $tr = $form.closest('tr'),
            start = $form.find('input[name="start"]').val(),
            end = $form.find('input[name="end"]').val(),
            action = $form.find('input[name="action"]').val();

        start = moment(start, 'DD/MM/YYYY HH:mm');
        end = moment(end, 'DD/MM/YYYY HH:mm');

        if (moment.utc(end.diff(start)).format('HH:mm') === '00:00') {
            return;
        }

        if (action === 'edit') {
            var $original = $tr.prev();
        } else {
            var $original = $tr.clone();

            $original.find('> td').html('');

            $tr.after($original);
        }

        $tr.find('div[rel="error-message"]').addClass('hidden');

        $.ajax({
            type: 'POST',
            data: $form.serialize(),
            success: function (response) {
                if (typeof response.id === 'undefined') {
                    return showError('I don\'t know whats happend', $form);
                }

                $.get(BASE_WWW + '/fact-tr/' + response.id, function (response) {
                    $original.replaceWith(response).find('[data-toggle="tooltip"]').tooltip();
                });

                $tr.addClass('success');

                setTimeout(function () {
                    $tr.removeClass('success');
                }, 1000);
            },
            error: function(response) {
                return showError(response.responseText, $form);
            }
        });
    };

    var resetTimes = function () {
        $addForm.find('input[name="start"], input[name="end"]').mask('00/00/0000 00:00');

        if (!$fact) {
            return;
        }

        $.each($fact, function(field, value) {
            $addForm.find('[name="' + field + '"]').val(value);
        });

        $addForm.find('[data-action="play"]').trigger('click');
    };

    var showError = function (message, $form) {
        var $tr = $form.closest('tr');

        $tr.addClass('danger')
            .find('div[rel="error-message"]')
            .removeClass('hidden')
            .html(message);

        setTimeout(function () {
            $tr.removeClass('danger');
        }, 1000);
    };

    var $editOpen,
        $editForm = $('#facts-form-edit'),
        timeCounter;

    $('.input-daterange').datepicker({
        format: 'dd/mm/yyyy',
        autoclose: true,
        todayBtn: true,
        weekStart: 1
    });

    $('.filter').on('change', function () {
        $(this).closest('form').submit();
    });

    $('.facts-table').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    $('.facts-table').floatThead({
        useAbsolutePositioning: false
    });

    $('.facts-table').on('click', '[data-action="edit"]', function (e) {
        e.preventDefault();

        var $original = $(this).closest('tr'),
            $clone = $editForm.clone(),
            id = $original.data('id');

        if ($editOpen) {
            var $previous = $editOpen.prev();

            $previous.removeClass('hover');

            if ($previous.data('id') === id) {
                $editOpen.remove();
                return;
            }

            $editOpen.remove();
        }

        $clone.find('input[name="id"]').val(id);

        setValue($clone, $original, 'start', '00/00/0000 00:00');
        setValue($clone, $original, 'end', '00/00/0000 00:00');

        $clone.find('input[name="time"]').val($.trim($original.find('.column-time div:first').text()));

        setOption($clone, $original, 'activity');
        setOption($clone, $original, 'tag');

        $clone.find('input[name="description"]').val($original.find('.column-activity [data-original-title]').data('original-title'));

        if ($original.data('remote')) {
            $clone.find('div[rel="remote"]').removeClass('hidden');
        }

        var className = $original.attr('class'),
            colspan = $original.find('> td').length,
            $td = $('<td colspan="' + colspan + '">');

        $editOpen = $('<tr class="' + className + ' hover">');
        $td.append($clone.removeClass('hidden'));
        $original.addClass('hover').after($editOpen.append($td));
    });

    $('.facts-table').on('submit', 'form', function (e) {
        e.preventDefault();

        if (e.keyCode === 13) {
            return;
        }

        saveFact($(this));
    });

    $addForm.find('[data-action="play"]').on('click', function (e) {
        e.preventDefault();

        var $start = $addForm.find('input[name="start"]'),
            $end = $addForm.find('input[name="end"]'),
            end = moment($end.val(), 'DD/MM/YYYY HH:mm');

        $(this).hide();
        $addForm.find('[data-action="refresh"]').removeClass('hidden').show();

        setFact();

        timeCounter = setInterval(function () {
            $end.val(moment().format('DD/MM/YYYY HH:mm')).trigger('change');
        }, 1000);
    });

    $addForm.find('[data-action="refresh"]').on('click', function (e) {
        e.preventDefault();
        refreshFact();
    });

    $addForm.find('button[type="submit"]').on('click', function (e) {
        e.preventDefault();

        if (!$addForm[0].checkValidity()) {
            return showError('Please check all form fields', $addForm);
        }

        var loaded = $fact ? true : false;
console.log($fact);
console.log(loaded);
        saveFact($addForm);

        refreshFact();
console.log(loaded);
        if (loaded) {
            $addForm.find('[data-action="play"]').trigger('click');
        }
    });

    $('.facts-table').on('keyup change', 'input:not(readonly)', function (e) {
        e.preventDefault();

        if (e.keyCode === 13) {
            return;
        }

        var $this = $(this),
            $clone = $this.closest('form'),
            start = $clone.find('input[name="start"]').val(),
            end = $clone.find('input[name="end"]').val(),
            $time = $clone.find('input[name="time"]');

        start = moment(start, 'DD/MM/YYYY HH:mm');
        end = moment(end, 'DD/MM/YYYY HH:mm');

        $time.val(moment.utc(end.diff(start)).format('HH:mm'));
    });

    resetTimes();
})(jQuery);

$(window).bind('beforeunload', function() {
    if ($fact) {
        setFact();
    }
});
