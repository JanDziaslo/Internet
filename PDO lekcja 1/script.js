$(document).ready(function(){

    getWorkers();
    getWorkersSzukajka();

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
    $('#szukajka-tabela').replaceWith('<div class="alert alert-info">taa... zapowiada sie ciekawa przygoda</div>');
}