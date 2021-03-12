var table;
var mydate = '';
var disposed;
var startdate;

$(document).ready(function()
{
    
    $("#min-date").datepicker({
        "dateFormat": "mm/dd/yy",
        onSelect: function(dateStr) {
            var min = $(this).datepicker('getDate') || new Date(); // Selected date or today if none
            $('#max-date').datepicker('option', {minDate: min});
        },
        maxDate: '0',
    })
    $("#max-date").datepicker({
        "dateFormat": "mm/dd/yy",
        minDate: '+0',
        maxDate: '0',
    })

    table =
    $('table.disposedTable').DataTable({ 
        "dom": 'Blrtip',
        "language": {
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only"></span> ',
            "emptyTable": "No data found!"
        },
        "order": [[ 0, "desc", ]],
        processing: true,
        serverSide: false,
        ajax: {
            url: 'dispose',
        error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'mydate', name: 'mydate'},
            { data: 'date', name:'date'},
            { data: 'category', name:'category'},
            { data: 'item', name:'item'},
            { data: 'serial', name:'serial'},
            { data: 'status', name:'status'}
        ],
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
            }
        ],
        buttons: {
            dom: {
                button: {
                    className: 'btn btn-primary' //Primary class for all buttons
                }
            },
            buttons: [
                {
                    extend: 'print',
                    className: 'btn btn-primary',
                    titleAttr: 'Print',
                    enabled: true,
                    autoPrint: false,
                    text: '<span class="icon text-white-50"><i class="fa fa-print" style="color:white"></i></span><span> PRINT</span>',
                    customize: function (doc) {
                        var d = new Date();
                        var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
                        var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
                        var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
                        $(doc.document.body)
                            .prepend('<img style="position:absolute; top:10; left:20;width:100;margin-botton:50px" src="'+window.location.origin+'/idsi.png">')
                            .prepend('<div style="position:absolute; bottom:80; left:15;font-family: arial; font-weight: bold;">Prepared By: '+$('#userlog').val()+'</div>')
                            .prepend('<div style="position:absolute; bottom:50; left:15;font-family: arial; font-weight: bold;">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                            .prepend('<div style="position:absolute; bottom:80; right:15;font-family: arial; font-weight: bold;">Received By: _____________________</div>')
                            .prepend('<div style="position:absolute; bottom:50; right:15;font-family: arial; font-weight: bold;">Received Date: _____________________</div>')
                            .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK INVENTORY MONITORING</div>')
                            .prepend('<img style="position:absolute; top:400; left:300;font-size:20px;margin-botton:50px" src="'+window.location.origin+'/idsiwatermark.png">')
                        $(doc.document.body).find('table')            			
                                .removeClass('dataTable')
                        .css('font-size','12px') 
                                .css('margin-top','85px')
                        .css('margin-bottom','60px')
                        $(doc.document.body).find('th').each(function(index){
                            $(this).css('font-size','14px');
                            $(this).css('color','black');
                            $(this).css('background-color','F0F0F0');
                        });
                    },
                    title:'',
                    exportOptions: {
                        rows: function (idx) {
                            var dt = new $.fn.dataTable.Api('#disposedTable' );
                            var selected = dt.rows( { selected: true } ).indexes().toArray();
                        
                            if( selected.length === 0 || $.inArray(idx, selected) !== -1)
                            return true;
                            return false;
                        },
                        columns: [ 1, 2, 3, 4, 5 ]
                    },
                    init: function(node) {$(node).removeClass('dt-button')},
                }
            ]
        }
    });
    table.buttons().container().appendTo('.printBtn');
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
$('#max-date').on("change", function () { 
    if (!$('#min-date').val()) {
        $(this).val('');
        alert('select start Date first!');
        return false;
    }
});
$('#min-date').on("change", function () { 
    if (!$('#min-date').val()) {
        $(this).val('');
        alert('select start Date first!');
        return false;
    }
});

$(document).on("click", "#goBtn", function() {
    if (!$('#min-date').val() || !$('#max-date').val()) {
        alert('select Date first!');
        return false;
    }
    console.log($('#min-date').val());
    startdate = $('#min-date').val()
    var rowcount = table.data().count();
    for(var i=0;i<rowcount;i++){
        if (table.rows( i ).data()[0].mydate.replace(new RegExp('/', 'g'),"") == startdate)
        {
            console.log(i);
        }else{
            console.log(table.rows( i ).data()[0].mydate.replace(new RegExp('/', 'g'),""));
        }
    }
    var filteredData = table
        .rows()
        .indexes()
        .filter( function ( value, index ) {
        return (table.rows(value).data()[0].mydate == startdate); 
        });
        console.log(table.row(1).data());
        console.log(filteredData);

    table.rows( filteredData ).remove().draw();
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