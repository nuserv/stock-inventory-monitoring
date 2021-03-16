var r = 1;
var y = 1;
var interval = null;
var requestno;
$(document).ready(function()
{
    var d = new Date();
    var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
    var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
    var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
    $('#date').val(months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm);
    $('#sdate').val(months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm);

    var table =
    $('table.resolvedTable').DataTable({ 
        "dom": 'lrtip',
        "pageLength": 50,
        "language": {
            "emptyTable": "No data found!",
            "info": "\"Showing _START_ to _END_ of _TOTAL_ Stock Request\"",
        },
        "columnDefs": [
        {
            "targets": [ 0 ],
            "visible": false
        }],
        processing: true,
        serverSide: true,
        ajax: 'res',
        columns: [
            { data: 'id', name:'id'},
            { data: 'created_at', name:'date', "width": "14%" },
            { data: 'reqBy', name:'reqBy', "width": "14%"},
            { data: 'branch', name:'branch',"width": "14%"},
            { data: 'type', name:'type', "width": "14%"},
            { data: 'stat', name:'stat', "width": "14%"},
            { data: 'resolved_name', name:'resolved_name', "width": "14%"}
        ]
    });
    $('#resolvedTable tbody').on('click', 'tr', function () {
        var trdata = table.row(this).data();
        var trsched = new Date(trdata.sched);
        requestno = trdata.request_no;
        $('#requestModal').modal('show');
        $('.notes').hide();
        $('#head').text('STOCK REQUEST NO. '+trdata.request_no);
        $('#sched').val(months[trsched.getMonth()]+' '+trsched.getDate()+', ' +trsched.getFullYear());
        $('#date').val(trdata.created_at);
        $('#status').val(trdata.status);
        $('#branch').val(trdata.branch);
        $('#name').val(trdata.reqBy);
        $('#area').val(trdata.area);
        $('#reqbranch').val(trdata.branch_id);
        $('#requesttypes').val(trdata.type);
        $('#resolvedby').val(trdata.resolved_name);


        if (trdata.type == "STOCK") {
            $('.ticketno').hide();
            $('#clientrows').hide();
        }else{
            $('.ticketno').show();
            $('#clientrows').show();
            $('#clients').val(trdata.client);
            $('#customers').val(trdata.customer);
            $('#tickets').val(trdata.ticket);
        }
        if (trdata.stat == 'RESOLVED') {
            $('.notes').show();
            $('#remarks').val(trdata.remarks);
            $('table.intransitDetails').show();
            $('.sched').show();
            $('table.intransitDetails').dataTable().fnDestroy();
            $('table.intransitDetails').DataTable({ 
                "dom": 'lrtp',
                "language": {
                    "emptyTable": " "
                },
                processing: true,
                serverSide: true,
                ajax: "/intransit/"+trdata.request_no,
                columns: [
                    { data: 'items_id', name:'items_id'},
                    { data: 'item_name', name:'item_name'},
                    { data: 'quantity', name:'quantity'},
                    { data: 'serial', name:'serial'}
                ]
            });
        }
    });
});
$(document).on("click", ".intransitDetails tr", function() {
    if ($('#status').val() == 'UNRESOLVED' && $('#level').val() == 'Manager') {
        $('#remarksModal').modal('show');
        $('#requestModal').modal('hide');
    }
});
$(document).on('click', '.close', function(){
    window.location.href = 'resolved';
});
$(document).on('click', '#remarks_btn', function(){
    $.ajax({
        url: 'resolved',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            requestno: requestno,
            remarks: $('#remarkstext').val()
        },
        success: function (data) {
            window.location.href = 'request';
        },
        error: function (data) {
            alert(data.responseText);
            return false;
        }
    });
});
