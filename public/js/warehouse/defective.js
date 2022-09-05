var table;
var sub = 0;
$(document).ready(function() {
    table =
        $('table.defectiveTable').DataTable({ 
            "dom": 'lrtip',
            "language": {
                "emptyTable": "No defective unit found!"
            },
            processing: true,
            serverSide: false,
            ajax: {
                url: 'return-table',
                error: function(data, error, errorThrown) {
                    if(data.status == 401) {
                        window.location.href = '/login';
                    }
                }
            },
            columns: [
                {data: 'date',name: 'date'},
                {data: 'branch', name: 'branch', "width": "14%"},
                {data: 'category',name: 'category'},
                {data: 'item',name: 'item'},
                {data: 'serial',name: 'serial'},
                {data: 'status',name: 'status'}
            ],
        });

    $('.filter-input').keyup(function() { 
        table.column($(this).data('column'))
            .search($(this).val())
            .draw();
    });
});
$(document).on("click", "#defectiveTable tr", function() {
    var trdata = table.row(this).data();
    $('#branch_id').val(trdata.branchid);
    $('#date').val(trdata.date);
    $('#description').val(trdata.item.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#status').val(trdata.status);
    $('#myid').val(trdata.id);
    $('#serial').val(trdata.serial);
    if (trdata.status == 'For receiving') {
        $('#submit_Btn').val('Received');
        $('#submit_Btn').show();
        $('#unrepair_Btn').hide();
    } else if (trdata.status == 'For repair' && $('#level').val() == 'Repair') {
        $('#submit_Btn').val('Repaired');
        $('#submit_Btn').show();
    } else if (trdata.status == 'Repaired' && $('#level').val() != 'Repair') {
        $('#submit_Btn').val('Add to stock');
        $('#submit_Btn').show();
    } else {
        $('#submit_Btn').hide();
        $('#unrepair_Btn').hide();
    }
    $('#returnModal').modal({
        backdrop: 'static',
        keyboard: false
    });
});

$(document).on('click', '#submit_Btn', function() {
    if (sub > 0) {
        return false;
    }
    $('#returnModal').modal('hide');
    $('#loading').show();
    var branch = $('#branch_id').val();
    var id = $('#myid').val();
    if ($('#submit_Btn').val() == 'Received') {
        sub++;
        $.ajax({
            url: 'return-update',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'PUT',
            data: {
                id: id,
                branch: branch,
                status: 'Received'
            },
            success: function(data) {
                //location.reload();
                table.draw();
                $('#loading').hide();
            },
            error: function(data) {
                alert(data.responseText);
            }
        });
    }
    if ($('#submit_Btn').val() == 'Repaired') {
        sub++;
        $.ajax({
            url: 'return-update',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'PUT',
            data: {
                id: id,
                branch: branch,
                status: 'Repaired'
            },
            success: function(data) {
                table.draw();
                $('#loading').hide();
            },
            error: function(data) {
                alert(data.responseText);
            }
        });
    }
    if ($('#submit_Btn').val() == 'Add to stock') {
        sub++;
        $.ajax({
            url: 'return-update',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'PUT',
            data: {
                id: id,
                branch: branch,
                status: 'warehouse'
            },
            success: function() {
                location.reload();
            },
            error: function(data) {
                alert(data.responseText);
            }
        });
    }
});
$(document).on('click', '.close', function() {
    if ($('#level').val() != 'Repair') {
        window.location.href = 'return';
    }else{
        window.location.href = '/';
    }
});
$(document).on('click', '#unrepair_Btn', function() {
    if (sub > 0) {
        return false;
    }
    $('#returnModal').modal('hide');
    $('#loading').show();
    var branch = $('#branch_id').val();
    var id = $('#myid').val();
    sub++;
    $.ajax({
        url: 'return-update',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            id: id,
            branch: branch,
            status: 'Unrepairable approval'
        },
        success: function() {
            location.reload();
        },
        error: function(data) {
            alert(data.responseText);
        }
    });
});