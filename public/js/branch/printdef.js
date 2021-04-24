var table;
$(document).ready(function()
{
    table = $('table.defectiveTable').DataTable({ 
        "dom": 'Brtip',
        processing: true,
        serverSide: false,
        destroy: true,
        searching: false,
        "language": {
            "emptyTable": "No item/s for return found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
        },
        "pageLength": 25,
        columnDefs: [{
            orderable: false,
            className: 'select-checkbox',      
            targets: 0
        }],
        ajax: {
            url: '/printtable',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
                alert(data.responseText);
            }
        },
        columns: [
            { data: null, defaultContent: ''},
            { data: 'date', name:'date'},
            { data: 'category', name:'category'},
            { data: 'item', name:'item'},
            { data: 'serial', name:'serial'},
            { data: 'status', name:'status'}
        ],
        
        select: {
            style: 'multi',
            selector: 'td:first-child'
        },
        buttons: {
            buttons: [
                {
                    extend: 'print',
                    className: 'btn btn-primary btn-icon-split',
                    titleAttr: 'PRINT',
                    enabled: false,
                    autoPrint: false,
                    text: '<span class="icon text-white-50"><i class="fa fa-print" style="color:white"></i></span><span> Print Preview</span>',
                    customize: function (doc) {
                        var d = new Date();
                        var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
                        var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
                        var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
                        
                        $(doc.document.body)
                            .prepend('<img style="position:absolute; top:10; left:20;width:100;margin-botton:50px" src="'+window.location.origin+'/idsi.png">')
                            //.prepend('<div style="position:absolute; top:10; right:0;">My Title</div>')
                            .prepend('<div style="position:absolute; top:90; width:100%;left:40%;font-size:20px;font-weight: bold"><b></b>DEFECTIVE ITEMS DELIVERY RECEIPT<b></b></div>')
                            //.prepend('<div style="position:absolute; top:90;margin: auto;font-size:16px;color: #0d1a80; font-family: arial; font-weight: bold;">Delivery receipt of defective units from '+$('#branchname').val()+'</div>')
                            .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK INVENTORY MONITORING</div>')
                            .prepend('<img style="position:absolute; top:400; left:300;font-size:20px;margin-botton:50px" src="'+window.location.origin+'/idsiwatermark.png">')
                            .prepend('<div style="position:absolute; top:140;"><b>Prepared by:</b> '+$('#name').val()+'</div>')
                            .prepend('<div style="position:absolute; top:170;"><b>Area.:</b> '+$('#area').val()+'</div>')
                            .prepend('<div style="position:absolute; top:140;left:70%"><b>Date prepared:</b> '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                            .prepend('<div style="position:absolute; top:200;"><label for="textbranch"><b>Branch address:&nbsp;&nbsp;</b></label><textarea id="textbranch" style="vertical-align: top;resize: none;background: transparent;border:0 none" rows="2" cols="90" readonly>'+$('#branchaddress').val()+'</textarea></div>')
                                //  .prepend('<div style="position:absolute; bottom:20; left:100;">Pagina '+page.toString()+' of '+pages.toString()+'</div>');
                        //jsDate.toString()
                        $(doc.document.body)
                            //.append('<div style="position:absolute; bottom:80; left:15;font-family: arial; font-weight: bold;">Prepared By: '+$("#userlog").val()+'</div>')
                            //.append('<div style="position:absolute; bottom:50; left:15;font-family: arial; font-weight: bold;">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                            .append('<div style="position:absolute; bottom:80; right:15;font-family: arial; font-weight: bold;">Received By: _____________________</div>')
                            .append('<div style="position:absolute; bottom:50; right:15;font-family: arial; font-weight: bold;">Received Date: _____________________</div>')
                        $(doc.document.body).find('table')            			
                                    .removeClass('dataTable')
                            .css('font-size','14px') 
                                    .css('margin-top','100px')
                            .css('margin-bottom','120px')
                            $(doc.document.body).find('th').each(function(index){
                                $(this).css('font-size','14px');
                                $(this).css('color','black');
                                $(this).css('background-color','F0F0F0');
                            });                
                    },
                    title:'',
                    exportOptions: {
                        rows: function ( idx, data, node ) {
                            var dt = new $.fn.dataTable.Api('#defectiveTable' );
                            var selected = dt.rows( { selected: true } ).indexes().toArray();
                        
                            if( selected.length === 0 || $.inArray(idx, selected) !== -1)
                            return true;

                            return false;
                        }
                    },
                    init: function(api, node, config) {$(node).removeClass('dt-button')}    
                }
            ]
        }
    });
    table.on( 'select deselect', function () {
        var selectedRows = table.rows( { selected: true } ).count();
 
        table.button( 0 ).enable( selectedRows > 0 );
    });
    table.buttons().container().appendTo('div.panel-heading');
});
$('table.defectiveTable').DataTable().on('select', function () {
    var rowselected = table.rows( { selected: true } ).data();
    if(rowselected.length > 0){
        $('#printBtn').prop('disabled', false);
    }
});
$('table.defectiveTable').DataTable().on('deselect', function () {
    var rowselected = table.rows( { selected: true } ).data();
    if(rowselected.length == 0){
        $('#printBtn').prop('disabled', true);
    }
});
$(document).on('click', '#printBtn', function(){
    $('#loading').show();
    var rowcount = table.data().count();
    var rows = table.rows( '.selected' ).data();
    var id = new Array();
    for(var i=0;i<rows.length;i++){
        id.push(rows[i].id);
    }
    var ids = new Array();
    for(var i=0;i<rowcount;i++){
        if ($.inArray(table.rows( i ).data()[0].id, id) == -1)
        {
            ids.push(i);
        }
    }
    table.rows( ids ).remove().draw();
    $('#loading').hide();
    window.print();
});

