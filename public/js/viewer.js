var table;
var filteredData;
$(document).ready(function()
{
    table =
    $('table.activityTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
                "emptyTable": "No data found!",
                "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>'
            },
        "pageLength": 10,
        "order": [ 0, 'desc' ],
        
        "columnDefs": [
            {
                "targets": [ 0 ],
                "visible": false
            }
        ],
        processing: true,
        serverSide: true,
        ajax: {
            url: 'activity',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        /*"fnRowCallback": function(nRow, aData) {
            if ($('#userlevel').val() == "Viewer IDSI") {
                if (aData.client == "MERCURY DRUG") {
                    $(nRow).hide();
                }
            }else if ($('#userlevel').val() == "Viewer PLSI"){
                if (aData.activity.toLowerCase().indexOf('mercury drug') == -1) {
                    $(nRow).hide();
                }
            }
        },*/
        columnDefs: [
            {
                "targets": [ 0 ],
                "visible": false
            },
            {
                "targets": [1],
                "render": $.fn.dataTable.render.moment('YYYY-MM-DD HH:mm:ss', 'MMM. DD, YYYY, h:mm A')
            },
        ],
        columns: [
            { data: 'id', name:'id',"width": "14%"},
            { data: 'created_at', name:'created_at',"width": "14%"},
            { data: 'fullname', name:'fullname',"width": "14%"},
            { data: 'branch', name:'branch',"width": "14%"},
            { data: 'activity', render: function ( data, type, row ) {
                return data.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&');
            }}
        ]
    });
    $('.tbsearch').show();

    $('.filter-input').keyup(function() { 
        table.column( $(this).data('column'))
            .search( $(this).val())
            .draw();
    });
});