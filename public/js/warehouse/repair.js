var table;
var sub = 0;
var itemid;
var dataid;
$(document).ready(function() {
    table =
        $('table.defectiveTable').DataTable({ 
            "dom": 'lrtip',
            "language": {
                "emptyTable": "No defective unit found!"
            },
            processing: false,
            serverSide: false,
            autoWidth: false,
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
                    "data": null,"width": "19%",
                    "targets": [7]
                }
            ],
            columns: [
                {data: 'updated_at',name: 'date',"width": "15%",
                    render: function(data, type, row) {
                        if (type === 'display' || type === 'filter') {
                            var date = new Date(data);
                            var formattedDateTime = date.toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' });
                            formattedDateTime = formattedDateTime.replace(' at', '');
                            return '<span class="formatted-date">' + formattedDateTime + '</span>';
                        }
                        return data; // Return the original data for sorting and other purposes
                    }
                },
                {data: 'branch',name: 'branch',"width": "10%"},
                {data: 'category',name: 'category',"width": "10%"},
                {data: 'item',name: 'item',"width": "16%"},
                {data: 'serial',name: 'serial',"width": "10%"},
                {data: 'status',name: 'status',"width": "15%"},
                {data: 'remarks',name: 'remarks',"width": "15%"}
            ],
        });

    $('.filter-input').keyup(function() { 
        table.column($(this).data('column'))
            .search($(this).val())
            .draw();
    });
});

$(document).on("click", "#defectiveTable tr td:not(:nth-child(7))", function () {
    var data = table.row(this).data();
    if (data.item.includes("2NR Printer")) {
        itemid = data.itemid;
        dataid = data.id;
        $('#updateModal').modal({backdrop: 'static', keyboard: false});
    }
});


$(document).on('change', '#desc1', function() {
    if (itemid == $(this).val()) {
        $('#sub_Btn').prop('disabled', true);
    }
    else{
        $('#sub_Btn').prop('disabled', false);
    }
});

$(document).on('click', '.sub_Btn', function() {
    $('#updateModal').modal('hide');
    $('#loading').show();
    setTimeout(() => {
        $.ajax({
            url: 'update-printer',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'PUT',
            data: {
                id: dataid,
                itemid: $('#desc1').val()
            },
            success: function(data) {
                location.reload();
                $('#loading').hide();
            },
            error: function(data) {
                alert(data.responseText);
            }
        });
    }, 500);
});

$(document).on('click', '.cancel', function () {
    location.reload(); 
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