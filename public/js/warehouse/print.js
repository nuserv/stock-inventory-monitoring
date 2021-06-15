var table;
$(document).ready(function()
{
    var reqno = $('#reqno').val();

    table =
        $('table.itemDetails').DataTable({ 
            "dom": 'Bftr',
            "language": {
                "emptyTable": "Delivery date not found!",
                "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only"></span> '
            },
            'iDisplayLength': 100,
            processing: true,
            serverSide: true,
            async: false,
            ajax: { 
                url: "/getprint/"+reqno,
                dataType: 'json',
                data: {
                    schedule: $("#datesched").val(),
                },
                error: function(data) {
                    if(data.status == 401) {
                        window.location.href = '/login';
                    }
                }
            },
            columns: [
                { data: 'item', name:'item'},
                { data: 'quantity', name:'quantity'},
                { data: 'serial', name:'serial'}
            ],
            buttons: {
                buttons: [
                    {
                        extend: "print",
                        className: 'btn btn-primary btn-icon-split buttonsToHide',
                        titleAttr: 'Submit and print preview',
                        enabled: true,
                        autoPrint: true,
                        text: '<span class="icon text-white-50"><i class="fa fa-print" style="color:white"></i></span><span> PRINT</span>',
                        customize: function (doc) {
                            var d = new Date();
                            var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
                            var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
                            var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
                            $(doc.document.body)
                                .prepend('<img style="position:absolute; top:10; left:20;width:100;margin-botton:50px" src="'+window.location.origin+'/idsi.png">')
                                //.prepend('<div style="position:absolute; top:10; right:0;">My Title</div>')
                                .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK MONITORING SYSTEM</center></div>')
                                .prepend('<div style="position:absolute; top:90; width:100%;left:40%;font-size:28px;font-weight: bold"><b></b>STOCK REQUEST DELIVERY RECEIPT<b></b></div>')
                                //.prepend('<div style="position:absolute; top:90;margin: auto;font-size:16px;color: #0d1a80; font-family: arial; font-weight: bold;">Delivery Receipt For '+$("#branch").val()+'</div>')
                                .prepend('<div style="position:absolute; top:140;font-size:24px"><b>Requested by:</b> '+$('#name').val()+'</div>')
                                .prepend('<div style="position:absolute; top:140;left:70%;font-size:24px"><b>Stock request no.:</b> '+$('#reqno').val()+'</div>')
                                .prepend('<div style="position:absolute; top:170;font-size:24px"><b>Area.:</b> '+$('#area').val()+'</div>')
                                .prepend('<div style="position:absolute; top:170;left:70%;font-size:24px"><b>Request type:</b> '+$('#requesttyp').val()+'</div>')
                                .prepend('<div style="position:absolute; top:200;font-size:24px"><label for="textbranch"><b>Branch address:&nbsp;&nbsp;</b></label><textarea id="textbranch" style="vertical-align: top;resize: none;background: transparent;border:0 none" rows="3" cols="50" readonly>'+$('#branchaddress').val()+'</textarea></div>')
                                .prepend('<div style="position:absolute; top:200;left:70%;font-size:24px"><b>Date Schedule:</b> '+$('#datesched').val()+'</div>')
                                .prepend('<img style="position:absolute; top:400; left:300;font-size:20px;margin-botton:50px" src="'+window.location.origin+'/idsiwatermark.png">')
                            //  .prepend('<div style="position:absolute; bottom:20; left:100;">Pagina '+page.toString()+' of '+pages.toString()+'</div>');
                            //jsDate.toString()
                            $(doc.document.body)
                                .append('<div style="position:absolute; bottom:80; left:15;font-family: arial; font-weight: bold;font-size:24px">Prepared By: '+$("#userlog").val()+'</div>')
                                .append('<div style="position:absolute; bottom:50; left:15;font-family: arial; font-weight: bold;font-size:24px">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                .append('<div style="position:absolute; bottom:80; right:15;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                                .append('<div style="position:absolute; bottom:50; right:15;font-family: arial; font-weight: bold;font-size:24px">Received Date: _____________________</div>')
                                
                            $(doc.document.body).find('table')            			
                                    .removeClass('dataTable')
                            .css('font-size','24px') 
                                    .css('margin-top','230px')
                            .css('margin-bottom','120px')
                            $(doc.document.body).find('th').each(function(index){
                                $(this).css('font-size','26px');
                                $(this).css('color','black');
                                $(this).css('background-color','F0F0F0');
                            });
                        },
                        title:'',
                        exportOptions: {
                            rows: function ( idx, data, node ) {
                                var dt = new $.fn.dataTable.Api('#itemDetails' );
                                var selected = dt.rows( { selected: true } ).indexes().toArray();
                            
                                if( selected.length === 0 || $.inArray(idx, selected) !== -1)
                                return true;
                                return false;
                            }
                        },
                        init: function(api, node, config) {$(node).removeClass('dt-button')},
                           
                    }
                ]
            }
        });
        table.buttons().container().appendTo('div.panel-heading');

    $("#datesched").datepicker({
        format: 'YYYY-MM-DD',
        minViewMode: 1,
        autoclose: true,
        onSelect: function() {
            $('table.itemDetails').dataTable().fnDestroy();
            table =
            $('table.itemDetails').DataTable({ 
                "dom": 'Btr',
                "language": {
                    "emptyTable": "Delivery date not found!",
                    "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                },
                'iDisplayLength': 100,
                processing: true,
                serverSide: true,
                async: false,
                ajax: { 
                    url: "/getprint/"+reqno,
                    dataType: 'json',
                    data: {
                        schedule: $("#datesched").val(),
                        reqno: reqno
                    },
                    error: function(data) {
                        if(data.status == 401) {
                            window.location.href = '/login';
                        }
                    }
                },
                columns: [
                    { data: 'schedule', name:'schedule'},
                    { data: 'items_id', name:'items_id'},
                    { data: 'item', name:'item'},
                    { data: 'serial', name:'serial'}
                ],
                buttons: {
                    buttons: [
                        {
                            extend: "print",
                            className: 'btn btn-primary btn-icon-split buttonsToHide',
                            titleAttr: 'Submit and print preview',
                            enabled: true,
                            autoPrint: true,
                            text: '<span class="icon text-white-50"><i class="fa fa-print" style="color:white"></i></span><span> PRINT</span>',
                            customize: function (doc) {
                                var d = new Date();
                                var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
                                var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
                                var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
                                $(doc.document.body)
                                    .prepend('<img style="position:absolute; top:10; left:20;width:100;margin-botton:50px" src="'+window.location.origin+'/idsi.png">')
                                    //.prepend('<div style="position:absolute; top:10; right:0;">My Title</div>')
                                    .prepend('<div style="position:absolute; bottom:80; left:15;font-family: arial; font-weight: bold;">Prepared By: '+$("#userlog").val()+'</div>')
                                    .prepend('<div style="position:absolute; bottom:50; left:15;font-family: arial; font-weight: bold;">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    .prepend('<div style="position:absolute; bottom:80; right:15;font-family: arial; font-weight: bold;">Received By: _____________________</div>')
                                    .prepend('<div style="position:absolute; bottom:50; right:15;font-family: arial; font-weight: bold;">Received Date: _____________________</div>')
                                    .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK MONITORING SYSTEM</div>')
                                    .prepend('<img style="position:absolute; top:400; left:300;font-size:20px;margin-botton:50px" src="'+window.location.origin+'/idsiwatermark.png">')
                                //  .prepend('<div style="position:absolute; bottom:20; left:100;">Pagina '+page.toString()+' of '+pages.toString()+'</div>');
                                //jsDate.toString()
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
                                rows: function ( idx, data, node ) {
                                    var dt = new $.fn.dataTable.Api('#defectiveTable' );
                                    var selected = dt.rows( { selected: true } ).indexes().toArray();
                                
                                    if( selected.length === 0 || $.inArray(idx, selected) !== -1)
                                    return true;
                                    return false;
                                }
                            },
                            init: function(api, node, config) {$(node).removeClass('dt-button')},
                               
                        }
                    ]
                }
            });
            table.buttons().container().appendTo('div.panel-heading');
        },
        maxDate: new Date(new Date().getFullYear(), new Date().getMonth()+1, '31'),
        minDate: 0
    });
});

