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
            end = $form.find('input[name="end"]').val();

        start = moment(start, 'DD/MM/YYYY HH:mm');
        end = moment(end, 'DD/MM/YYYY HH:mm');

        if (HOUR && (moment.utc(end.diff(start)).format('HH:mm') === '00:00')) {
            return;
        }

        $form.find('div[rel="error-message"]').hide();

        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: $form.serialize(),
            success: function (response) {
                if (typeof response.id === 'undefined') {
                    return showError('I don\'t know whats happend', $form);
                }

                var action = $form.find('input[name="action"]').val();

                if (action === 'factAdd') {
                    var $original = $factsTable.find('tbody > tr:first').clone();
                    $factsTable.find('tbody').prepend($original);
                } else {
                    var $original = $tr.prev();
                }

                $.get(BASE_WWW + '/fact-tr/' + response.id, function (response) {
                    $original.replaceWith(response).find('[data-toggle="tooltip"]').tooltip();
                });

                if (action === 'factAdd') {
                    $tr = $original;
                }

                $tr.addClass('success');

                setTimeout(function () {
                    $tr.removeClass('success');
                }, 1000);
            },
            error: function(response) {
                return showErrors(response.responseJSON, $form);
            }
        });
    };

    var loadTimes = function () {
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
        $form.find('div[rel="error-message"]')
            .show().removeClass('hidden')
            .html(message);
    };

    var showErrors = function (errors, $form) {
        console.log(errors);
        var errorContainer = $form.find('div[rel="error-message"]');
        $.each(errors, function (index, value) {
            errorContainer.append('<div>'+value+'</div>');

        });
        errorContainer.show().removeClass('hidden');
    };
    var $editOpen,
        $editForm = $('#facts-form-edit'),
        $headerTimer = $('#header-timer'),
        $factsTable = $('.facts-table'),
        MASK_FORMAT = ('00/00/0000' + (((typeof HOUR !== 'undefined') && HOUR) ? ' 00:00' : '')),
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

    var $wait = $('.submit-wait');

    if ($wait.length) {
        if ($wait.is('form') !== true) {
            $wait = $wait.closest('form');
        }

        $wait.on('submit', function () {
            var message = $wait.data('message');

            $('body').append('<div class="waiting-layer"></div>')
                .append('<div class="waiting-layer-message">' + message + '</div>');

            return true;
        });
    }

    $('body').on('submit', '.facts-form', function (e) {
        e.preventDefault();

        if (e.keyCode === 13) {
            return;
        }

        saveFact($(this));
    });

    $('body').tooltip({
        selector: '[data-toggle="tooltip"]'
    });

    $factsTable.floatThead({
        useAbsolutePositioning: false
    });

    $factsTable.on('click', '[data-action="edit"]', function (e) {
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

        setValue($clone, $original, 'start', MASK_FORMAT);
        setValue($clone, $original, 'end', MASK_FORMAT);

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

    $addForm.find('[data-action="play"]').on('click', function (e) {
        e.preventDefault();

        var $activity = $addForm.find('select[name="activity"]');

        if (!$activity.val()) {
            return showError('Please, select a project', $addForm);
        }

        $addForm.find('[rel="error-message"]').hide();

        var $start = $addForm.find('input[name="start"]'),
            $end = $addForm.find('input[name="end"]'),
            end = moment($end.val(), 'DD/MM/YYYY HH:mm');

        $(this).hide();

        $addForm.find('[data-action="refresh"]').removeClass('hidden').show();

        setFact();

        $start.val(moment().format('DD/MM/YYYY HH:mm')).trigger('change');
        $end.val(moment().format('DD/MM/YYYY HH:mm')).trigger('change');

        timeCounter = setInterval(function () {
            $end.val(moment().format('DD/MM/YYYY HH:mm')).trigger('change');
        }, 60000);
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

        saveFact($addForm);
        refreshFact();
    });

    $('.facts-form').on('keyup change', 'input:not(readonly)', function (e) {
        e.preventDefault();

        if (e.keyCode === 13) {
            return;
        }

        var $this = $(this),
            $form = $this.closest('form'),
            action = $form.find('input[name="action"]').val();

        if ((action !== 'factAdd') && !HOUR) {
            return;
        }

        var start = $form.find('input[name="start"]').val(),
            end = $form.find('input[name="end"]').val(),
            $time = $form.find('input[name="time"]');

        start = moment(start, 'DD/MM/YYYY HH:mm');
        end = moment(end, 'DD/MM/YYYY HH:mm');

        var diff = moment.utc(end.diff(start)).format('HH:mm');

        $time.val(diff);

        if ($fact) {
            $headerTimer.find('h1').text(diff);
            $headerTimer.find('h2').text($addForm.find('select[name="activity"] option:selected').text());
        }
    });

    $('.bootstrap-switch').bootstrapSwitch();

    loadTimes();
})(jQuery);

$(window).bind('beforeunload', function() {
    if ($fact) {
        setFact();
    }
});
