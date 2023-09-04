var table;
var send = 1;
var retno;
var rowcount;
$(document).ready(function()
{
    $.ajax({
        type:'get',
        url:'gen',
        async: false,
        success:function(result)
        {
            retno = result;
        }
    });
    table =
    $('table.repairedTable').DataTable({ 
        "dom": 'Blrtip',
        processing: true,
        serverSide: false,
        "language": {
            "emptyTable": "No item/s for repaired found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
        },
        "pageLength": 25,
        ajax: {
            url: 'repairedget',
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        "order": [[ 1, "asc" ],[ 2, "asc" ]],
        columns: [
            { data: 'updated_at', name:'updated_at'},
            { data: 'category', name: 'category'},
            { data: 'item', name:'item'},
            { data: 'serial', name:'serial'}
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
                    titleAttr: 'Submit and print preview',
                    enabled: true,
                    autoPrint: true,
                    text: '<span class="icon text-white-50"><i class="fa fa-print" style="color:white"></i></span><span> SUBMIT AND PRINT</span>',
                    customize: function (doc) {
                        var d = new Date();
                        var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
                        var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
                        var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
                        /*$(doc.document.body)
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
                        .css('margin-bottom','120px')
                        $(doc.document.body).find('th').each(function(index){
                            $(this).css('font-size','14px');
                            $(this).css('color','black');
                            $(this).css('background-color','F0F0F0');
                        });*/
                        $(doc.document.body)
                        .prepend('<img style="position:absolute; top:10; left:20;width:100;margin-botton:50px" src="'+window.location.origin+'/idsi.png">')
                        //.prepend('<div style="position:absolute; top:10; right:0;">My Title</div>')
                        .prepend('<div style="position:absolute; top:90; width:100%;left:40%;font-size:28px;font-weight: bold"><b></b>REPAIRED ITEMS DELIVERY RECEIPT<b></b></div>')
                        //.prepend('<div style="position:absolute; top:90;margin: auto;font-size:16px;color: #0d1a80; font-family: arial; font-weight: bold;">Delivery receipt of defective units from '+$('#branchname').val()+'</div>')
                        .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK MONITORING SYSTEM</div>')
                        .prepend('<img style="position:absolute; top:400; left:300;font-size:20px;margin-botton:50px" src="'+window.location.origin+'/idsiwatermark.png">')
                        .prepend('<div style="position:absolute; top:140;font-size:24px"><b>Prepared by:</b> '+$('#userlog').val()+'</div>')
                        .prepend('<div style="position:absolute; top:170;font-size:24px"><b>Area.:</b> '+$('#areaname').val()+'</div>')
                        .prepend('<div style="position:absolute; top:140;left:70%;font-size:24px"><b>Date prepared:</b> '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                        //.prepend('<div style="position:absolute; top:200;font-size:24px"><label for="textbranch"><b>Branch address:&nbsp;&nbsp;</b></label><textarea id="textbranch" style="vertical-align: top;resize: none;background: transparent;border:0 none" rows="3" cols="80" readonly>'+$('#addr').val()+'</textarea></div>')
                        .prepend('<div style="position:absolute; top:230;font-size:24px"><b>Reference #: </b> '+retno+'</div>')
                            //  .prepend('<div style="position:absolute; bottom:20; left:100;">Pagina '+page.toString()+' of '+pages.toString()+'</div>');
                    //jsDate.toString()
                        $(doc.document.body)
                            //.append('<div style="position:absolute; bottom:80; left:15;font-family: arial; font-weight: bold;">Prepared By: '+$("#userlog").val()+'</div>')
                            //.append('<div style="position:absolute; bottom:50; left:15;font-family: arial; font-weight: bold;">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                            .append('<div style="position:absolute; bottom:80; right:15;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                            .append('<div style="position:absolute; bottom:50; right:15;font-family: arial; font-weight: bold;font-size:24px">Received Date: _____________________</div>')
                        $(doc.document.body).find('table')            			
                            .removeClass('dataTable')
                            .css('font-size','24px') 
                            .css('margin-top','310px')
                            .css('margin-bottom','310px')
                        $(doc.document.body).find('th').each(function(index){
                            $(this).css('font-size','26px');
                            $(this).css('color','black');
                            $(this).css('background-color','F0F0F0');
                        });   
                    },
                    title:'',
                    exportOptions: {
                        rows: function (idx) {
                            var dt = new $.fn.dataTable.Api('#defectiveTable' );
                            var selected = dt.rows( { selected: true } ).indexes().toArray();
                        
                            if( selected.length === 0 || $.inArray(idx, selected) !== -1)
                            return true;
                            return false;
                        }
                    },
                    init: function(node) {$(node).removeClass('dt-button')},
                }
            ]
        }
    });
    table.buttons().container().appendTo('.printBtn');
    $('#loading').show();
    setTimeout(function() { 
        rowcount = table.data().count();
        if (rowcount) {
            $('#test').show();
        }
        $('#loading').hide();
    }, 2000);
    
});
$(document).on('click', '#printrecBtn', function(){
    window.location.href = 'repaired-list';
});
$(document).on('click', '.printBtn', function(){
    $('#loading').show();
    setTimeout(() => {
        $.ajax({
            url: 'repairedupdate',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'PUT',
            data: {
                retno: retno,
                send: send
            },
            success:function(data)
            {
                location.reload();
            },
            error: function (data) {
                alert(data.responseText);
                return false;
            }
        });
    }, 300);
    
});