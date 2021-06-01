var table;
var mydate = '';
var disposed;
var startdate;
var tables;
var desc=" ";
search = '0';
$(document).ready(function()
{
    $("#min-date").datepicker({
        template: "modal",
        "dateFormat": "mm/dd/yy",
        onSelect: function(dateStr) {
            var min = $(this).datepicker('getDate') || new Date(); // Selected date or today if none
            $('#max-date').datepicker('option', {minDate: min});
        },
        maxDate: '0',
        beforeShow: function() {
            setTimeout(function(){
                $('.ui-datepicker').css('z-index', 99999999999999);
            }, 0);
        }
    })
    $("#max-date").datepicker({
        "dateFormat": "mm/dd/yy",
        minDate: '+0',
        maxDate: '0',
        beforeShow: function() {
            setTimeout(function(){
                $('.ui-datepicker').css('z-index', 99999999999999);
            }, 0);
        }
    })

    table =
    $('table.disposedTable').DataTable({ 
        "dom": 'lrtip',
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
            { data: 'date', name:'date'},
            { data: 'category', name:'category'},
            { data: 'item', name:'item'},
            { data: 'serial', name:'serial'}
        ]
    });

    tables =
    $('table.disposedsTable').DataTable({ 
        "dom": 'Brtip',
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
                            .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK MONITORING SYSTEM</div>')
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
                            var dt = new $.fn.dataTable.Api('#disposedsTable' );
                            var selected = dt.rows( { selected: true } ).indexes().toArray();
                        
                            if( selected.length === 0 || $.inArray(idx, selected) !== -1)
                            return true;
                            return false;
                        },
                        columns: [ 1, 2, 3, 4 ]
                    },
                    init: function(node) {$(node).removeClass('dt-button')},
                }
            ]
        }
    });
    tables.buttons().container().appendTo('.printBtn');
    $('.printBtn').hide();
    desc+='<option selected disabled>select item</option>';
    setTimeout(function () {
        var data = table.data();
    /*if(data.length > 0){
        for(var i=0;i<data.length;i++){
            if (data[i].item == "For return") {
                $('#returnBtn').prop('disabled', false);
                return false;
            }
        }  
    }*/
    var itemcode = $.map(data, function(value, index) {
        return [value];
    });
    itemcode.forEach(value => {
        desc+='<option value="'+value.item+'">'+value.item+'</option>';
    });
    $("#item").find('option').remove().end().append(desc);
    }, 1000)
    

});
$('#generate').on("click", function () { 
    $('#disposedModal').modal({
        backdrop: 'static',
        keyboard: false
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
$(document).on("keyup", "#searchall", function () {
    table.search(this.value).draw();
});
$(document).on('click', '.cancel', function(){
    location.reload();
});
$(document).on("click", "#byDate", function() {
    $('#datediv').show();
    $('#itemdiv').hide();
});
$(document).on("click", "#byItem", function() {
    $('#datediv').hide();
    $('#itemdiv').show();
});
$(document).on('change', "#item", function() {
    search = '1';
});
$(document).on("click", ".goBtn", function() {
    if ($('#byDate').is(':checked')) {
        if (!$('#min-date').val() || !$('#max-date').val()) {
            alert('select Date first!');
            return false;
        }
        tables.ajax.reload();
        $('#disposedModal').modal('hide');
        $('#loading').show();
        setTimeout(function () {
            var filteredData = tables
            .rows()
            .indexes()
            .filter( function ( value, index ) {
            return (tables.rows(value).data()[0].mydate.replace(new RegExp('/', 'g'),"") < $('#min-date').val().replace(new RegExp('/', 'g'),"")); 
            });
    
            tables.rows( filteredData ).remove().draw();
            filteredData = tables
                .rows()
                .indexes()
                .filter( function ( value, index ) {
                return (tables.rows(value).data()[0].mydate.replace(new RegExp('/', 'g'),"") > $('#max-date').val().replace(new RegExp('/', 'g'),"")); 
                });
            tables.rows( filteredData ).remove().draw();
            setTimeout(function () {
                $('.buttons-print').click();
                setTimeout(function () {
                    location.reload();
                }, 1000)
            }, 1000)
            $('#loading').hide();
        }, 1000)
    }else if ($('#byItem').is(':checked')) {
        if (search == '0') {
            alert('select item first!');
            return false;
        }
        $('#disposedModal').modal('hide');
        $('#loading').show();
        tables
        .columns( 3 )
        .search( $('#item').val() )
        .draw();
        setTimeout(function () {    
            $('.buttons-print').click();
            setTimeout(function () {
                location.reload();
            }, 1000)
            $('#loading').hide();
        }, 1000)
    }

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