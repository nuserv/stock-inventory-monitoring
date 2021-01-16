var sunit;
$(document).ready(function()
{
    sunit = $('table.sUnitTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "emptyTable": " "
        },
        processing: true,
        serverSide: true,
        ajax: 'sUnit',
        
        columns: [
            { data: 'date', name:'date'},
            { data: 'client', name:'client'},
            { data: 'category', name:'category'},
            { data: 'description', name:'description'},
            { data: 'serial', name:'serial'},
            { data: 'serviceby', name:'serviceby'}
        ]
    });
});

$(document).on('click', '#out_Btn', function(){
    $('#service-unitModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '.in-close', function(){
    $('#service-unitModal').modal('toggle');
    $('#loading').show();
    window.location.href = 'service-unit';
});

$(document).on("click", "#sUnitTable tr", function () {
    var trdata = sunit.row(this).data();
    $('#outOptionModal').modal({backdrop: 'static', keyboard: false});
    console.log(trdata);
});

$(document).on('click', '.service-unit', function(){
    $('#outOptionModal .out-close').click();
    $('.def').show();
    $('.gud').show();
    $('#inOptionModal').modal({backdrop: 'static', keyboard: false});
});

$(document).on('click', '.replacement', function () {
    
    $('#inOptionModal').modal({backdrop: 'static', keyboard: false});
});