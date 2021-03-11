var table;
$(document).ready(function()
{
    
    table =
    $('table.disposedTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "emptyTable": " "
        },
        "order": [[ 0, "desc", ]],
        processing: true,
        serverSide: true,
        ajax: {
            url: 'dispose',
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
        ]
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
$(document).on("click", ".approveBtn", function() {
    var returnid = $(this).attr('return_id');
    console.log(returnid);
    $.ajax({
        url: 'return-update',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            id: returnid,
            status: 'approved'
        },
        success: function(data) {
            location.reload();
        },
        error: function(data) {
            alert(data.responseText);
        }
    });
});
$(document).on("click", ".disposeBtn", function() {
    var returnid = $(this).attr('return_id');
    console.log(returnid);
    $.ajax({
        url: 'return-update',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            id: returnid,
            status: 'dispose'
        },
        success: function(data) {
            location.reload();
        },
        error: function(data) {
            alert(data.responseText);
        }
    });
});
$(document).on("click", ".returnBtn", function() {
    var returnid = $(this).attr('return_id');
    console.log(returnid);
    $.ajax({
        url: 'return-update',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            id: returnid,
            status: 'return'
        },
        success: function(data) {
            location.reload();
        },
        error: function(data) {
            alert(data.responseText);
        }
    });
});