var table;
var send = 1;
var retno;
var rowcount;
var pullout;
var items;
var pull_no;
$(document).ready(function()
{
    table =
    $('table.pulloutTable').DataTable({ 
        "dom": 'lrtip',
        processing: true,
        serverSide: false,
        "language": {
            "emptyTable": "No pullout found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
        },
        "pageLength": 25,
        "order": [ 0, "asc" ],
        ajax: {
            url: 'pullget',
            data:{
                list: 'list'
            },
            error: function(data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
            }
        },
        columns: [
            { data: 'updated_at', name:'updated_at'},
            { data: 'pullout_no', name:'pullout_no'},
            { data: 'status', name:'status'}
        ]
    });
    
});
$(document).on("click", "#pulloutTable tr", function () {
    var data = table.row(this).data();
    $('#head').text(data.branch+' - Pullout Details');
    $('#pulloutModal').modal({backdrop: 'static', keyboard: false});
    pull_no = data.pullout_no;
    Promise.all([pullitems()]).then(() => { 
        pullout =
        $('table.pulloutitems').DataTable({ 
            "dom": 'Blrtip',
            processing: true,
            serverSide: false,
            "language": {
                "emptyTable": "No pullout found!",
                "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
            },
            "order": [ 0, "asc" ],
            "pageLength": 25,
            ajax: {
                url: 'pullitem',
                data: {
                    pullno: data.pullout_no,
                },
                error: function(data) {
                    if(data.status == 401) {
                        window.location.href = '/login';
                    }
                }
            },
            columns: [
                { data: 'category', name:'category'},
                { data: 'item', name:'item'},
                { data: 'serial', name:'serial'},
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
                        titleAttr: 'PRINT',
                        enabled: true,
                        autoPrint: true,
                        text: '<span class="icon text-white-50"><i class="fa fa-print" style="color:white"></i></span><span> PRINT</span>',
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
                            .prepend('<div style="position:absolute; top:90; width:100%;left:40%;font-size:28px;font-weight: bold"><b></b>PULLOUT ITEMS DELIVERY RECEIPT<b></b></div>')
                            //.prepend('<div style="position:absolute; top:90;margin: auto;font-size:16px;color: #0d1a80; font-family: arial; font-weight: bold;">Delivery receipt of defective units from '+$('#branchname').val()+'</div>')
                            .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK MONITORING SYSTEM</div>')
                            .prepend('<img style="position:absolute; top:400; left:300;font-size:20px;margin-botton:50px" src="'+window.location.origin+'/idsiwatermark.png">')
                            .prepend('<div style="position:absolute; top:140;font-size:24px"><b>Prepared by:</b> '+$('#userlog').val()+'</div>')
                            .prepend('<div style="position:absolute; top:170;font-size:24px"><b>Area.:</b> '+$('#areaname').val()+'</div>')
                            .prepend('<div style="position:absolute; top:140;left:70%;font-size:24px"><b>Date prepared:</b> '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                            .prepend('<div style="position:absolute; top:200;font-size:24px"><label for="textbranch"><b>Branch name: '+$('branchname').val()+'</div>')
                            .prepend('<div style="position:absolute; top:230;font-size:24px"><b>Pullout #: </b> '+pull_no+'</div>')
                            //  .prepend('<div style="position:absolute; bottom:20; left:100;">Pagina '+page.toString()+' of '+pages.toString()+'</div>');
                            //jsDate.toString()
                            //$(doc.document.body)
                                //.append('<div style="position:absolute; bottom:80; left:15;font-family: arial; font-weight: bold;">Prepared By: '+$("#userlog").val()+'</div>')
                                //.append('<div style="position:absolute; bottom:50; left:15;font-family: arial; font-weight: bold;">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                .append('<div style="position:absolute; bottom:40; right:15;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                                .append('<div style="position:absolute; bottom:10; right:15;font-family: arial; font-weight: bold;font-size:24px">Received Date: _____________________</div>')
                            $(doc.document.body).find('table')            			
                                .removeClass('dataTable')
                                .css('font-size','24px') 
                                .css('margin-top','310px')
                                .css('margin-bottom','510px');
                            $(doc.document.body).find('th').each(function(index){
                                $(this).css('font-size','26px');
                                $(this).css('color','black');
                                $(this).css('background-color','F0F0F0');
                            });   
                        },
                        title:'',
                        exportOptions: {
                            rows: function (idx) {
                                var dt = new $.fn.dataTable.Api('#pulloutitems' );
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
        pullout.buttons().container().appendTo('.printBtn');
    });
    
    function pullitems() {
        return $.ajax({
            type:'get',
            url: "pullitem",
            data: {
                pullno: data.pullout_no,
            },
            success:function(data)
            {
                items = data.data.length;
            },
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
                alert(data.responseText);
            }
        });
    }
});

$(document).on('click', '.cancel', function(){
    $('#loading').show();
    location.reload();
});