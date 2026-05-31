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
        var cursorOffset;

        switch (tag) {
            case 'url':
                if (selected) {
                    replacement = '[url=' + selected + ']' + selected + '[/url]';
                    cursorOffset = 6 + selected.length + selected.length; // po drugim selected
                } else {
                    replacement = '[url=]URL[/url]';
                    cursorOffset = 6; // po [url=]
                }
                break;
            case 'email':
                if (selected) {
                    replacement = '[email]' + selected + '[/email]';
                    cursorOffset = 7 + selected.length;
                } else {
                    replacement = '[email]email@example.com[/email]';
                    cursorOffset = 7; // po [email]
                }
                break;
            case 'img':
                if (selected) {
                    replacement = '[img]' + selected + '[/img]';
                    cursorOffset = 5 + selected.length;
                } else {
                    replacement = '[img]https://example.com/image.jpg[/img]';
                    cursorOffset = 5; // po [img]
                }
                break;
            case 'color':
                if (selected) {
                    replacement = '[color=' + selected + ']' + selected + '[/color]';
                    cursorOffset = 8 + selected.length + selected.length; // po drugim selected
                } else {
                    replacement = '[color=red]tekst[/color]';
                    cursorOffset = 11; // po [color=red] (7+3+1)
                }
                break;
            case 'size':
                if (selected) {
                    replacement = '[size=' + selected + ']' + selected + '[/size]';
                    cursorOffset = 7 + selected.length + selected.length; // po drugim selected
                } else {
                    replacement = '[size=large]tekst[/size]';
                    cursorOffset = 12; // po [size=large] (6+5+1)
                }
                break;
            case 'list':
                replacement = '[list]\n[*]element 1[/*]\n[*]element 2[/*]\n[/list]';
                cursorOffset = 10; // po [list]\n[*]
                break;
            case 'center':
                replacement = '[center]' + selected + '[/center]';
                cursorOffset = selected ? 8 + selected.length : 8;
                break;
            case 'quote':
                replacement = '[quote]' + selected + '[/quote]';
                cursorOffset = selected ? 7 + selected.length : 7;
                break;
            case 'code':
                replacement = '[code]' + selected + '[/code]';
                cursorOffset = selected ? 6 + selected.length : 6;
                break;
            default:
                replacement = '[' + tag + ']' + selected + '[/' + tag + ']';
                cursorOffset = selected ? (2 + tag.length + selected.length) : (2 + tag.length);
        }

        textarea.val(textarea.val().substring(0, start) + replacement + textarea.val().substring(end));
        textarea[0].selectionStart = textarea[0].selectionEnd = start + cursorOffset;
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


// Nawigacja do wskazanej strony przez Ajax
window.navigateTo = function(url) {
    // Pokaż spinner ładowania
    $('#main-content').html(
        '<div class="container mt-4"><div class="text-center py-5"><div class="spinner-border" role="status"><span class="visually-hidden">Ładowanie...</span></div><p class="mt-2 text-muted">Ładowanie...</p></div></div>'
    );

    $.ajax({
        url: url + (url.indexOf('?') === -1 ? '?' : '&') + 'ajax=1',
        method: 'GET',
        dataType: 'html'
    })
    .done(function(html) {
        // Wyodrębnij #main-content z odpowiedzi
        var $tmp = $('<div>').html(html);
        var $newContent = $tmp.find('#main-content');

        if ($newContent.length === 0) {
            // Fallback: normalne przejście
            window.location.href = url;
            return;
        }

        var contentHtml = $newContent.html();

        // Wyodrębnij skrypty (bez CDN bootstrap/jquery)
        var $content = $('<div>').html(contentHtml);
        var scriptsToRun = [];

        $content.find('script').each(function() {
            var src = $(this).attr('src') || '';
            var code = $(this).text();

            // Pomiń skrypty CDN (bootstrap, jquery)
            if (src && (src.indexOf('bootstrap') !== -1 || src.indexOf('jquery') !== -1)) {
                return;
            }

            if (code && code.trim()) {
                scriptsToRun.push(code);
            }
        });

        // Usuń wszystkie skrypty z contentu
        $content.find('script').remove();

        // Wstaw zawartość
        $('#main-content').html($content.html());

        // Zaktualizuj URL
        history.pushState({ url: url }, '', url);

        // Zaktualizuj aktywny link w nawigacji
        updateActiveNav(url);

        // Wykonaj skrypty w kolejności
        scriptsToRun.forEach(function(code) {
            try {
                var scriptEl = document.createElement('script');
                scriptEl.text = code;
                document.body.appendChild(scriptEl);
            } catch(e) {
                console.error('Błąd wykonania skryptu:', e);
            }
        });
    })
    .fail(function() {
        // Fallback przy błędzie
        window.location.href = url;
    });
};

// Aktualizacja aktywnego linku w nawigacji
function updateActiveNav(url) {
    $('#navbarNav .nav-link').removeClass('active').removeAttr('aria-current');
    $('#navbarNav .nav-link[href="' + url + '"]').addClass('active').attr('aria-current', 'page');
}

// Obsługa kliknięć w linki nawigacyjne
$(document).on('click', '#navbarNav .nav-link', function(e) {
    var href = $(this).attr('href');
    if (!href || href === '#' || href.indexOf('http') === 0 || href.indexOf('mailto:') === 0) {
        return;
    }

    // Nie przechwytuj linku wylogowania
    if (href.indexOf('logout.php') !== -1) {
        return;
    }

    e.preventDefault();
    navigateTo(href);
});

// Obsługa przycisków Wstecz/Dalej w przeglądarce
$(window).on('popstate', function(e) {
    if (e.originalEvent.state && e.originalEvent.state.url) {
        var url = e.originalEvent.state.url;

        $.ajax({
            url: url + (url.indexOf('?') === -1 ? '?' : '&') + 'ajax=1',
            method: 'GET',
            dataType: 'html'
        })
        .done(function(html) {
            var $tmp = $('<div>').html(html);
            var $newContent = $tmp.find('#main-content');

            if ($newContent.length === 0) {
                window.location.href = url;
                return;
            }

            var contentHtml = $newContent.html();
            var $content = $('<div>').html(contentHtml);
            var scriptsToRun = [];

            $content.find('script').each(function() {
                var src = $(this).attr('src') || '';
                var code = $(this).text();
                if (src && (src.indexOf('bootstrap') !== -1 || src.indexOf('jquery') !== -1)) return;
                if (code && code.trim()) scriptsToRun.push(code);
            });

            $content.find('script').remove();
            $('#main-content').html($content.html());
            updateActiveNav(url);

            scriptsToRun.forEach(function(code) {
                try {
                    var scriptEl = document.createElement('script');
                    scriptEl.text = code;
                    document.body.appendChild(scriptEl);
                } catch(e) {}
            });
        })
        .fail(function() {
            window.location.href = url;
        });
    }
});
