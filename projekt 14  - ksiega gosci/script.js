$(function () {
    $('#toggle-form-btn').on('click', function () {
        var $form = $('#form-container');
        var $btn = $(this);

        if ($form.hasClass('d-none')) {
            $form.removeClass('d-none');
            $btn.text('Ukryj formularz');
        } else {
            $form.addClass('d-none');
            $btn.html('<i class="me-1">+</i> Dodaj wpis');
        }
    });

    $('.bbcode-btn').on('click', function () {
        var tag = $(this).data('tag');
        var textarea = $('#content');
        var start = textarea[0].selectionStart;
        var end = textarea[0].selectionEnd;
        var selected = textarea.val().substring(start, end);
        var replacement;

        switch (tag) {
            case 'url':
                if (selected) {
                    replacement = '[url=' + selected + ']' + selected + '[/url]';
                } else {
                    replacement = '[url=]URL[/url]';
                }
                break;
            case 'email':
                if (selected) {
                    replacement = '[email]' + selected + '[/email]';
                } else {
                    replacement = '[email]email@example.com[/email]';
                }
                break;
            case 'img':
                if (selected) {
                    replacement = '[img]' + selected + '[/img]';
                } else {
                    replacement = '[img]https://example.com/image.jpg[/img]';
                }
                break;
            case 'color':
                if (selected) {
                    replacement = '[color=' + selected + ']' + selected + '[/color]';
                } else {
                    replacement = '[color=red]tekst[/color]';
                }
                break;
            case 'size':
                if (selected) {
                    replacement = '[size=' + selected + ']' + selected + '[/size]';
                } else {
                    replacement = '[size=large]tekst[/size]';
                }
                break;
            case 'list':
                replacement = '[list]\n[*]element 1[/*]\n[*]element 2[/*]\n[/list]';
                break;
            case 'center':
                replacement = '[center]' + selected + '[/center]';
                break;
            case 'quote':
                replacement = '[quote]' + selected + '[/quote]';
                break;
            case 'code':
                replacement = '[code]' + selected + '[/code]';
                break;
            default:
                replacement = '[' + tag + ']' + selected + '[/' + tag + ']';
        }

        textarea.val(textarea.val().substring(0, start) + replacement + textarea.val().substring(end));
        textarea[0].selectionStart = textarea[0].selectionEnd = start + replacement.length;
        textarea.focus().trigger('input');
    });

    var charLimit = parseInt($('#content').attr('maxlength'));
    $('#content').on('input', function () {
        var len = $(this).val().length;
        var $count = $('#char-count');
        $count.text(len);
        if (len >= charLimit) {
            $count.addClass('text-danger');
        } else {
            $count.removeClass('text-danger');
        }
    });

    $('#entry-form').on('submit', function (e) {
        e.preventDefault();

        var form = $(this);
        var submitBtn = form.find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Dodawanie...');

        $.ajax({
            url: form.attr('action'),
            method: 'POST',
            data: form.serialize(),
            dataType: 'json',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .done(function (response) {
                if (response.success) {
                    $('#alert-container').html(
                        '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                        response.message +
                        '<button type="button" class="btn btn-close" data-bs-dismiss="alert"></button></div>'
                    );
                    form[0].reset();
                    $('#char-count').text(0);
                } else {
                    var errors = response.errors ? response.errors.join('<br>') : 'Wystąpił błąd.';
                    $('#alert-container').html(
                        '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                        errors +
                        '<button type="button" class="btn btn-close" data-bs-dismiss="alert"></button></div>'
                    );
                }
            })
            .fail(function () {
                $('#alert-container').html(
                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                    'Wystąpił błąd podczas dodawania wpisu.' +
                    '<button type="button" class="btn btn-close" data-bs-dismiss="alert"></button></div>'
                );
            })
            .always(function () {
                submitBtn.prop('disabled', false).text('Dodaj wpis');
            });
    });
});
