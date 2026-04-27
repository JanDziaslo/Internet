$(document).ready(function(){
    if($('#workersData').length) {
        getWorkers();
        getWorkersSzukajka();
    }
});

function getWorkers(){
    $.ajax({
        url: "getWorkers.php",
        method: 'POST'
    }).done(function( data ) {
        $('#workersData').html(data);
    })
}

function getWorkersSzukajka(){
    $('#szukajka').on('submit',function(e){
        e.preventDefault();
        $('#workersData').html('<tr>\n' +
            '    <td colspan="10">\n' +
            '        <div class="d-flex justify-content-center w-100">\n' +
            '            <div class="loader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>\n' +
            '        </div>\n' +
            '    </td>\n' +
            '</tr>');
        $.ajax({
            url: "getWorkersSzukajka.php",
            method: 'POST',
            data: {
                search: $('#search').val(),
            }
        }).done(function( data ) {
            $('#workersData').html(data);
        });
    });

}

function dodawnie_prac() {
    $('#szukajka-tabela').hide();

    if ($('#form-container').length === 0) {
        $('#szukajka-tabela').after('<div id="form-container" class="mt-5"></div>');
    }

    $('#form-container').html(
        '<div class="d-flex justify-content-center my-5">' +
        '<div class="loader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>' +
        '</div>'
    ).show();

    $('#form-container').load('dodaj_prac.php .container form', function() {
        obsluzZapisPracownika();
    });
}

function obsluzZapisPracownika() {
    $('#form-container form').on('submit', function(e) {
        e.preventDefault();
        
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Dodawanie...');

        let formData = $(this).serialize();

        $.ajax({
            url: 'dodaj_prac.php',
            method: 'POST',
            data: formData
        }).done(function(response) {

            let nowyFormularz = $('<div>').html(response).find('.container form');

            $('#form-container').html(nowyFormularz);

            obsluzZapisPracownika();

            if ($('<div>').html(response).find('.alert-success').length > 0) {
                // Remove auto redirect
                getWorkers();
            }
        }).fail(function() {
             submitBtn.prop('disabled', false).html(originalText);
        });
    });

    $('#form-container form .btn-danger').on('click', function(e) {
        e.preventDefault();
        pracWroc();
    });
}

function pracWroc() {
    $('#form-container').empty().hide();

    $('#szukajka-tabela').show();
}

function delPrac(id) {
    if (confirm("Czy na pewno chcesz usunąć tego pracownika?")) {

        $.ajax({
            url: "delPrac.php",
            method: 'POST',
            data: {
                action: 'delete',
                idp: id
            }
        }).done(function(response) {
            getWorkers();
        }).fail(function() {
            alert("Nie udało się usunąć pracownika.");
        });
    }
}

function edycja_prac(id) {
    $('#szukajka-tabela').hide();

    if ($('#form-container').length === 0) {
        $('#szukajka-tabela').after('<div id="form-container" class="mt-5"></div>');
    }

    $('#form-container').html(
        '<div class="d-flex justify-content-center my-5">' +
        '<div class="loader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>' +
        '</div>'
    ).show();

    $('#form-container').load('edytuj_prac.php?id=' + id + ' .container form', function() {
        obsluzEdycjePracownika(id);
    });
}

function obsluzEdycjePracownika(id) {
    $('#form-container form').on('submit', function(e) {
        e.preventDefault();
        
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Zapisywanie...');

        let formData = $(this).serialize();

        $.ajax({
            url: 'edytuj_prac.php?id=' + id,
            method: 'POST',
            data: formData
        }).done(function(response) {
            let nowyFormularz = $('<div>').html(response).find('.container form');
            $('#form-container').html(nowyFormularz);

            obsluzEdycjePracownika(id);

            if ($('<div>').html(response).find('.alert-success').length > 0) {
                // Remove auto redirect
                getWorkers();
            }
        }).fail(function() {
            submitBtn.prop('disabled', false).html(originalText);
        });
    });

    $('#form-container form .btn-danger').on('click', function(e) {
        e.preventDefault();
        pracWroc();
    });
}
// Etaty

$(document).ready(function(){
    if($('#etatyData').length) {
        getEtaty();
        getEtatySzukajka();
    }
});

function getEtaty(){
    $.ajax({
        url: "getEtaty.php",
        method: 'POST'
    }).done(function( data ) {
        $('#etatyData').html(data);
    })
}

function getEtatySzukajka(){
    $('#szukajka-etat').on('submit',function(e){
        e.preventDefault();
        $('#etatyData').html('<tr>\n' +
            '    <td colspan="4">\n' +
            '        <div class="d-flex justify-content-center w-100">\n' +
            '            <div class="loader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>\n' +
            '        </div>\n' +
            '    </td>\n' +
            '</tr>');
        $.ajax({
            url: "getEtatySzukajka.php",
            method: 'POST',
            data: {
                search: $('#search-etat').val(),
            }
        }).done(function( data ) {
            $('#etatyData').html(data);
        });
    });
}

function dodawanie_etat() {
    $('#szukajka-tabela-etat').hide();

    if ($('#form-container-etat').length === 0) {
        $('#szukajka-tabela-etat').after('<div id="form-container-etat" class="mt-5"></div>');
    }

    $('#form-container-etat').html(
        '<div class="d-flex justify-content-center my-5">' +
        '<div class="loader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>' +
        '</div>'
    ).show();

    $('#form-container-etat').load('dodaj_etat.php .container form', function() {
        obsluzZapisEtatu();
    });
}

function obsluzZapisEtatu() {
    $('#form-container-etat form').on('submit', function(e) {
        e.preventDefault();
        
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Dodawanie...');

        let formData = $(this).serialize();

        $.ajax({
            url: 'dodaj_etat.php',
            method: 'POST',
            data: formData
        }).done(function(response) {
            let nowyFormularz = $('<div>').html(response).find('.container form');
            $('#form-container-etat').html(nowyFormularz);
            obsluzZapisEtatu();

            if ($('<div>').html(response).find('.alert-success').length > 0) {
                // Remove auto redirect
                getEtaty();
            }
        }).fail(function() {
            submitBtn.prop('disabled', false).html(originalText);
        });
    });

    $('#form-container-etat form .btn-danger').on('click', function(e) {
        if($(this).attr('href')) {
            e.preventDefault();
            etatWroc();
        }
    });
}

function etatWroc() {
    $('#form-container-etat').empty().hide();
    $('#szukajka-tabela-etat').show();
}

function delEtat(id) {
    if (confirm("Czy na pewno chcesz usunąć ten etat?")) {
        $.ajax({
            url: "delEtat.php",
            method: 'POST',
            data: {
                action: 'delete',
                idp: id
            }
        }).done(function(response) {
            getEtaty();
        }).fail(function() {
            alert("Nie udało się usunąć etatu.");
        });
    }
}

function edycja_etat(id) {
    $('#szukajka-tabela-etat').hide();

    if ($('#form-container-etat').length === 0) {
        $('#szukajka-tabela-etat').after('<div id="form-container-etat" class="mt-5"></div>');
    }

    $('#form-container-etat').html(
        '<div class="d-flex justify-content-center my-5">' +
        '<div class="loader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>' +
        '</div>'
    ).show();

    $('#form-container-etat').load('edytuj_etat.php?nazwa=' + encodeURIComponent(id) + ' .container form', function() {
        obsluzEdycjeEtatu(id);
    });
}

function obsluzEdycjeEtatu(id) {
    $('#form-container-etat form').on('submit', function(e) {
        e.preventDefault();
        
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Zapisywanie...');

        let formData = $(this).serialize();

        $.ajax({
            url: 'edytuj_etat.php?nazwa=' + encodeURIComponent(id),
            method: 'POST',
            data: formData
        }).done(function(response) {
            let nowyFormularz = $('<div>').html(response).find('.container form');
            $('#form-container-etat').html(nowyFormularz);
            obsluzEdycjeEtatu(id);

            if ($('<div>').html(response).find('.alert-success').length > 0) {
                // Remove auto redirect
                getEtaty();
            }
        }).fail(function() {
             submitBtn.prop('disabled', false).html(originalText);
        });
    });

    $('#form-container-etat form .btn-danger').on('click', function(e) {
        if($(this).attr('href')) {
            e.preventDefault();
            etatWroc();
        }
    });
}

// zespoly

$(document).ready(function(){
    if($('#zespolyData').length) {
        getZespoly();
        getZespolySzukajka();
    }
});

function getZespoly(){
    $.ajax({
        url: "getZespoly.php",
        method: 'POST'
    }).done(function( data ) {
        $('#zespolyData').html(data);
    })
}

function getZespolySzukajka(){
    $('#szukajka-zesp').on('submit',function(e){
        e.preventDefault();
        $('#zespolyData').html('<tr>\n' +
            '    <td colspan="4">\n' +
            '        <div class="d-flex justify-content-center w-100">\n' +
            '            <div class="loader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>\n' +
            '        </div>\n' +
            '    </td>\n' +
            '</tr>');
        $.ajax({
            url: "getZespolySzukajka.php",
            method: 'POST',
            data: {
                search: $('#search-zesp').val(),
            }
        }).done(function( data ) {
            $('#zespolyData').html(data);
        });
    });
}

function dodawanie_zesp() {
    $('#szukajka-tabela-zesp').hide();

    if ($('#form-container-zesp').length === 0) {
        $('#szukajka-tabela-zesp').after('<div id="form-container-zesp" class="mt-5"></div>');
    }

    $('#form-container-zesp').html(
        '<div class="d-flex justify-content-center my-5">' +
        '<div class="loader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>' +
        '</div>'
    ).show();

    $('#form-container-zesp').load('dodaj_zesp.php .container form', function() {
        obsluzZapisZespolu();
    });
}

function obsluzZapisZespolu() {
    $('#form-container-zesp form').on('submit', function(e) {
        e.preventDefault();
        
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Dodawanie...');

        let formData = $(this).serialize();

        $.ajax({
            url: 'dodaj_zesp.php',
            method: 'POST',
            data: formData
        }).done(function(response) {
            let nowyFormularz = $('<div>').html(response).find('.container form');
            $('#form-container-zesp').html(nowyFormularz);
            obsluzZapisZespolu();

            if ($('<div>').html(response).find('.alert-success').length > 0) {
                // Remove auto redirect
                getZespoly();
            }
        }).fail(function() {
             submitBtn.prop('disabled', false).html(originalText);
        });
    });

    $('#form-container-zesp form .btn-danger').on('click', function(e) {
        if($(this).attr('href')) {
            e.preventDefault();
            zespWroc();
        }
    });
}

function zespWroc() {
    $('#form-container-zesp').empty().hide();
    $('#szukajka-tabela-zesp').show();
}

function delZesp(id) {
    if (confirm("Czy na pewno chcesz usunąć ten zespół?")) {
        $.ajax({
            url: "delZesp.php",
            method: 'POST',
            data: {
                action: 'delete',
                idp: id
            }
        }).done(function(response) {
            getZespoly();
        }).fail(function() {
            alert("Nie udało się usunąć zespołu.");
        });
    }
}

function edycja_zesp(id) {
    $('#szukajka-tabela-zesp').hide();

    if ($('#form-container-zesp').length === 0) {
        $('#szukajka-tabela-zesp').after('<div id="form-container-zesp" class="mt-5"></div>');
    }

    $('#form-container-zesp').html(
        '<div class="d-flex justify-content-center my-5">' +
        '<div class="loader"><div></div><div></div><div></div><div></div><div></div><div></div><div></div><div></div></div>' +
        '</div>'
    ).show();

    $('#form-container-zesp').load('edytuj_zesp.php?id=' + id + ' .container form', function() {
        obsluzEdycjeZespolu(id);
    });
}

function obsluzEdycjeZespolu(id) {
    $('#form-container-zesp form').on('submit', function(e) {
        e.preventDefault();
        
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Zapisywanie...');

        let formData = $(this).serialize();

        $.ajax({
            url: 'edytuj_zesp.php?id=' + id,
            method: 'POST',
            data: formData
        }).done(function(response) {
            let nowyFormularz = $('<div>').html(response).find('.container form');
            $('#form-container-zesp').html(nowyFormularz);
            obsluzEdycjeZespolu(id);

            if ($('<div>').html(response).find('.alert-success').length > 0) {
                // Remove auto redirect
                getZespoly();
            }
        }).fail(function() {
            submitBtn.prop('disabled', false).html(originalText);
        });
    });

    $('#form-container-zesp form .btn-danger').on('click', function(e) {
        if($(this).attr('href')) {
            e.preventDefault();
            zespWroc();
        }
    });
}
