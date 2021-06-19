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
            "columnDefs": [
                {   
                    "render": function ( data, type, row, meta ) {
                        if (data.status == "For receiving") {
                            return '<button class="btn-primary recBtn" return_id="'+data.id+'" stat="Received">Received</button>';
                        }else if (data.status == "For repair") {
                            return '<button class="btn-primary recBtn" return_id="'+data.id+'" stat="Repaired">Repaired</button>&nbsp; <button class="btn-primary recBtn" return_id="'+data.id+'" stat="Unrepairabled">Unrepairabled</button>';
                        }else if (data.status == "Conversion") {
                            return '<button class="btn-primary recBtn" return_id="'+data.id+'" stat="Repaired">Repaired</button>&nbsp; <button class="btn-primary recBtn" return_id="'+data.id+'" stat="Unrepairabled">Unrepairabled</button>';
                        }
                    },
                    "defaultContent": '',
                    "data": null,"width": "18%",
                    "targets": [6]
                }
            ],
            columns: [{
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'branch',
                    name: 'branch'
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'item',
                    name: 'item'
                },
                {
                    data: 'serial',
                    name: 'serial'
                },
                {
                    data: 'status',
                    name: 'status',
                    "width": "14%"
                }
            ],
        });

    $('.filter-input').keyup(function() { 
        table.column($(this).data('column'))
            .search($(this).val())
            .draw();
    });
});

$(document).on('click', '.recBtn', function() {
    var returnid = $(this).attr('return_id');
    if ($(this).attr('stat') == "Received") {
        $.ajax({
            url: 'return-update',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'PUT',
            data: {
                id: returnid,
                status: 'Received'
            },
            success: function(data) {
                
                $('#loading').hide();
            },
            error: function(data) {
                alert(data.responseText);
            }
        });
        table
            .row($(this).parents('tr'))
            .remove().draw( false );
    }else if($(this).attr('stat') == "Repaired"){
        $.ajax({
            url: 'return-update',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'PUT',
            data: {
                id: returnid,
                status: 'Repaired'
            },
            success: function(data) {
                $('#loading').hide();
            },
            error: function(data) {
                alert(data.responseText);
            }
        });
        table
            .row($(this).parents('tr'))
            .remove().draw( false );
    }else if($(this).attr('stat') == "Unrepairabled"){
        $.ajax({
            url: 'return-update',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'PUT',
            data: {
                id: returnid,
                status: 'Unrepairable approval'
            },
            success: function(data) {
                $('#loading').hide();
            },
            error: function(data) {
                alert(data.responseText);
            }
        });
        table
            .row($(this).parents('tr'))
            .remove().draw( false );
    }

    
});
/*$(document).on("click", "#defectiveTable tr", function() {
    var trdata = table.row(this).data();
    $('#branch_id').val(trdata.branchid);
    $('#date').val(trdata.date);
    $('#description').val(trdata.item.replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
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
});*/

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