var table;
var sub = 0;
$(document).ready(function()
{
    table =
    $('table.defectiveTable').DataTable({ 
        "dom": 'lrtip',
        processing: true,
        serverSide: false,
        destroy: true,
        searching: false,
        "language": {
            "emptyTable": "No item/s for return",
            "info": "\"Showing _START_ to _END_ of _TOTAL_ Defectives\" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",
            select: {
                rows: {
                    _: "You have selected %d Defective Units",
                    0: "Select Defective Units to Return",
                    1: "Only 1 Defective Unit Selected"
                }
            }
        },
        "pageLength": 2,
        
        ajax: {
            url: 'return-table',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'date', name:'date'},
            { data: 'category', name:'category'},
            { data: 'item', name:'item'},
            { data: 'serial', name:'serial'},
            { data: 'status', name:'status'}
        ],
        select: {
            style: 'multi',
            
        }
    });

    $('#search-ic').on("click", function () { 
        for ( var i=0 ; i<=5 ; i++ ) {
            
            $('.fl-'+i).val('').change();
            table
            .columns(i).search( '' )
            .draw();
        }
        $('.tbsearch').toggle();
        
    });

    $('.filter-input').keyup(function() { 
        table.column( $(this).data('column'))
            .search( $(this).val())
            .draw();
    });


});

$('table.defectiveTable').DataTable().on('select', function () {
    var rowselected = table.rows( { selected: true } ).data();
    console.log(rowselected);
    /*if(rowselected.length > 0){
        $('#rec_Btn').prop('disabled', false);
    }*/
});

$('table.defectiveTable').DataTable().on('deselect', function () {
    var rowselected = table.rows( { selected: true } ).data();
    console.log(rowselected);
    /*if(rowselected.length > 0){
        $('#rec_Btn').prop('disabled', false);
    }*/
});


/*$(document).on("click", "#defectiveTable tr", function () {
    
    var trdata = table.row(this).data();
    clearInterval(interval);
    $('#branch_id').val(trdata.branchid);
    $('#date').val(trdata.date);
    $('#description').val(convert(trdata.item));
    $('#status').val(trdata.status);
    $('#myid').val(trdata.id);
    $('#serial').val(trdata.serial);
    $('#return_id').val(trdata.itemid);
    if (trdata.status == 'For return') {
        $('#submit_Btn').show();
    }else{
        $('#submit_Btn').hide();
    }

    $('#returnModal').modal({backdrop: 'static', keyboard: false});

    function convert (string) {
        return string.replace(/&#(?:x([\da-f]+)|(\d+));/ig, function (_, hex, dec) {
            return String.fromCharCode(dec || +('0x' + hex))
        })
    }
});*/

$(document).on('click', '#submit_Btn', function(){
    if (sub > 0) {
        return false;
    }
    var branch = $('#branch_id').val();
    var id = $('#myid').val();
    var status = 'For receiving';
    var itemid = $('#return_id').val();
    sub++;
    $.ajax({
        url: 'return-update',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            id: id,
            branch: branch,
            status: status,
            itemid: itemid
        },
        success:function()
        {
            interval = setInterval(function(){
                table.draw();
            }, 30000);
            table.draw();
            $('#returnModal .close').click();
        },
        error: function (data) {
            alert(data.responseText);
        }
    });
});

$(document).on('click', '.cancel', function(){
    window.location.href = 'return';
});

$(document).on('click', '.close', function(){
    table.draw();
    interval = setInterval(function(){
        table.draw();
    }, 30000);
});