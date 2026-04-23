$(document).ready(function(){

    getWorkers();
    getWorkersSzukajka();
    //delPrac();

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
        $('#szukajka-tabela').after('<div id="form-container"></div>');
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
                setTimeout(function() {
                    pracWroc();
                    getWorkers();
                }, 1500);
            }
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
        $('#szukajka-tabela').after('<div id="form-container"></div>');
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
                setTimeout(function() {
                    pracWroc();
                    getWorkers();
                }, 1500);
            }
        });
    });

    $('#form-container form .btn-danger').on('click', function(e) {
        e.preventDefault();
        pracWroc();
    });
}