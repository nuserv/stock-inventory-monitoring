var table;
var sub = 0;
var retno;
var r = 1;
var y = 1;
var posy = 1;
var posr = 1;
var branch = "not ok";

$(document).ready(function()
{
    $("#datepullout").datepicker({
        format: 'YYYY-MM-DD',
        minViewMode: 1,
        autoclose: true,
        maxDate: 0
    });

    $.ajax({
        type:'get',
        url:'gen',
        async: false,
        success:function(result)
        {
            retno = result;
        },
    });

    $('#sub_Btn').prop('disabled', true);

    if ($('#branchname').val() != "Conversion") {
        table =
            $('table.defectiveTable').DataTable({ 
                "dom": 'lrtip',
                processing: true,
                serverSide: false,
                destroy: true,
                "language": {
                    "emptyTable": "No item/s for return found!",
                    "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                    "info": "\"Showing _START_ to _END_ of _TOTAL_ Defectives\" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",
                    select: {
                        rows: {
                            _: "You have selected %d Defective Units",
                            0: "Select Defective Units to Return",
                            1: "Only 1 Defective Unit Selected"
                        }
                    }
                },
                "pageLength": 25,
                columnDefs: [
                    {
                    orderable: false,
                    className: 'select-checkbox',      
                    targets: 0
                    },
                    {
                        "targets": [ 0 ],
                        "visible": true
                    }
                ],
                ajax: {
                    url: '/return-table',
                    async: false,
                    error: function(data) {
                        if(data.status == 401) {
                            window.location.href = '/login';
                        }
                    }
                },
                columns: [
                    { data: null, defaultContent: ''},
                    { data: 'date', name:'date'},
                    { data: 'category', name:'category'},
                    { data: 'item', name:'item'},
                    { data: 'serial', name:'serial'},
                    { data: 'name', name:'name'},
                    { data: 'status', name:'status'}
                ],
                select: {
                    style: 'single',
                    selector: 'td:first-child'
                }
            });

        if ($('#branchname').val() != "Conversion") {
            
        }
        $('table.defectiveTable').DataTable().on('select', function () {
            var rowselected = table.rows( { selected: true } ).data();
            if ($('#returnBtn').is(":visible")) {
                $('#returnBtn').val('REPAIRED');
            }
            if(rowselected.length > 0){
                $('#returnBtn').prop('disabled', false);
            }
            if ( $('#returnBtn').val() ==  'CREATE LIST' && table.rows( { selected: true } ).count() == 0) {
                $('#returnBtn').prop('disabled', true);
            }
            var selectedRows = table.rows( { selected: true } ).count();
        
            table.button( 0 ).enable( selectedRows > 0 );
        });
        $('table.defectiveTable').DataTable().on('deselect', function () {
            //var rowselected = table.rows( { selected: true } ).data();
            if ($('#returnBtn').is(":visible")) {
                $('#returnBtn').val('CREATE LIST');
            }
            var selectedRows = table.rows( { selected: true } ).count();
            table.button( 0 ).enable( selectedRows > 0 );
            var ret = table.data();
            
            if(ret.length > 0){
                for(var i=0;i<ret.length;i++){
                    if (ret[i].status == "For return") {
                        $('#returnBtn').prop('disabled', false);
                        return false;
                    }
                }  
            }
            if ( $('#returnBtn').val() ==  'CREATE LIST' && table.rows( { selected: true } ).count() == 0) {
                $('#returnBtn').prop('disabled', true);
            }
            
        });
        var data = table.data();
        if(data.length > 0){
            for(var i=0;i<data.length;i++){
                if (data[i].status == "For return") {
                    $('#returnBtn').prop('disabled', false);
                    return false;
                }
            }  
        }
    }else{
        table =
            $('table.drTable').DataTable({ 
                "dom": 'lrtip',
                processing: true,
                serverSide: false,
                destroy: true,
                "language": {
                    "emptyTable": "No item/s for return found!",
                    "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                },
                "pageLength": 25,
                ajax: {
                    url: '/return-table',
                    async: false,
                    error: function(data) {
                        if(data.status == 401) {
                            window.location.href = '/login';
                        }
                    }
                },
                columns: [
                    { data: 'date', name:'date'},
                    { data: 'drno', name:'drno'},
                    { data: 'pullout_date', name:'pullout_date'},
                    { data: 'branch', name:'branch'},
                    { data: 'customer_branch', name:'customer_branch'}
                ]
            });
    }
    
    $('.printBtn').hide();
    $('#search-ic').on("click", function () { 
        for ( var i=1 ; i<=5 ; i++ ) {
            
            $('.fl-'+i).val('').change();
            table
            .columns(i).search( '' )
            .draw();
        }
        $('.tbsearch').toggle();
    });

    $('.filter-input').keyup(function() { 
        console.log($(this).data('column'));
        table.column($(this).data('column'))
            .search($(this).val())
            .draw();
    });

    
    
});


$(document).on('click', '.printBtn', function () {
    // var data = table.rows( { selected: true } ).data()
    var id = new Array();
    table.rows().every( function ( rowIdx, tableLoop, rowLoop ) {
        var data = this.data();
        if (data.status == "For return") {
            id.push(data.id);
        }
    });
    /*if(data.length > 0){
        for(var i=0;i<data.length;i++){
            if (data[i].status == "For return") {
                id.push(data[i].id);
            }
        }  
    }*/
    $('#loading').show();
    $.ajax({
        url: 'return-update',
        async: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        dataType: 'json',
        type: 'PUT',
        data: {
            id: id,
            ret: retno
        },
        success:function()
        {
            $('#loading').show();
            var rowcount = table.data().count();
            var rows = table.rows( '.selected' ).data();
            var idss = new Array();
            for(var i=0;i<rows.length;i++){
                idss.push(rows[i].id);
            }
            var ids = new Array();
            for(var i=0;i<rowcount;i++){
                if ($.inArray(table.rows( i ).data()[0].id, idss) == -1)
                {
                    ids.push(i);
                }
            }
            table.rows( ids ).remove().draw();
            $('#loading').hide();
            setTimeout(function() { window.location.href = 'return';}, 1000);
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    }); 
});
$(document).on('click', '#returnBtn', function(){
    if ($('#returnBtn').val() == 'REPAIRED') {
        var rowselected = table.rows( { selected: true } ).data();
        $.ajax({
            url: 'repaired',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'DELETE',
            data: {
                id: rowselected[0].id,
                item: rowselected[0].item
            },
            success: function(){
                location.reload(); 
            },
            error: function (data) {
                if(data.status == 401) {
                    window.location.href = '/login';
                }
                alert(data.responseText);
            }
        });
        return false;
    }
    var rowcount = table.data().count();
    var status = new Array();
    $.ajax({
        type:'get',
        url:'gen',
        async: false,
        success:function(result)
        {
            retno = result;
        },
    });
    $('.printBtn').show();
    for(var i=0;i<rowcount;i++){
        if (table.rows( i ).data()[0].status == 'For receiving')
        {
            status.push(i);
        }
    }
    if (rowcount != status.length) {
        $('#loading').show();
        if ($('#branchname').val() != "Conversion") {
            table =
                $('table.defectiveTable').DataTable({ 
                    "dom": 'Blrtip',
                    processing: true,
                    serverSide: false,
                    destroy: true,
                    searching: false,
                    "language": {
                        "emptyTable": "No item/s for return",
                        "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                        "info": "\"Showing _START_ to _END_ of _TOTAL_ Defectives\" &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;",
                        select: {
                            rows: {
                                _: "You have selected %d Defective Units",
                                0: "Select Defective Units to Return",
                                1: "Only 1 Defective Unit Selected"
                            }
                        }
                    },
                    "pageLength": 25,
                    "order": [[ 2, "asc" ], [ 3, "asc" ]],
                    columnDefs: [
                        {
                        className: 'select-checkbox',      
                        targets: 0,
                        visible: false
                        },
                        {
                        targets: 1,
                        visible: false
                        },
                        {
                        targets: 5,
                        visible: false
                        }
                    ],
                    ajax: {
                        async: false,
                        url: '/return-table',
                        error: function(data) {
                            if(data.status == 401) {
                                window.location.href = '/login';
                            }
                        }
                    },
                    columns: [
                        { data: null, defaultContent: ''},
                        { data: 'date', name:'date'},
                        { data: 'category', name:'category'},
                        { data: 'item', name:'item'},
                        { data: 'serial', name:'serial'},
                        { data: 'name', name:'name'},
                        { data: 'status', name:'status'}
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
                                text: '<span class="icon text-white-50"><i class="fa fa-print" style="color:white"></i></span><span> SUBMIT</span>',
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
                                    .prepend('<div style="position:absolute; top:90; width:100%;left:40%;font-size:28px;font-weight: bold"><b></b>DEFECTIVE ITEMS DELIVERY RECEIPT<b></b></div>')
                                    //.prepend('<div style="position:absolute; top:90;margin: auto;font-size:16px;color: #0d1a80; font-family: arial; font-weight: bold;">Delivery receipt of defective units from '+$('#branchname').val()+'</div>')
                                    .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK MONITORING SYSTEM</div>')
                                    .prepend('<img style="position:absolute; top:400; left:300;font-size:20px;margin-botton:50px" src="'+window.location.origin+'/idsiwatermark.png">')
                                    .prepend('<div style="position:absolute; top:140;font-size:24px"><b>Prepared by:</b> '+$('#userlog').val()+'</div>')
                                    .prepend('<div style="position:absolute; top:170;font-size:24px"><b>Area.:</b> '+$('#areaname').val()+'</div>')
                                    .prepend('<div style="position:absolute; top:140;left:60%;font-size:24px"><b>Date prepared:</b> '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    .prepend('<div style="position:absolute; top:200;font-size:24px"><label for="textbranch"><b>Branch name: '+$('#branchname').val()+'</div>')
                                    .prepend('<div style="position:absolute; top:230;font-size:24px"><b>Return #: </b> '+retno+'</div>')
                                    .prepend('<div style="position:absolute; top:260;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                                    .prepend('<div style="position:absolute; top:290;font-family: arial; font-weight: bold;font-size:24px">Received Date: _____________________</div>')
                                
                                //  .prepend('<div style="position:absolute; bottom:20; left:100;">Pagina '+page.toString()+' of '+pages.toString()+'</div>');
                                //jsDate.toString()
                                    // $(doc.document.body)
                                        //.append('<div style="position:absolute; bottom:80; left:15;font-family: arial; font-weight: bold;">Prepared By: '+$("#userlog").val()+'</div>')
                                        //.append('<div style="position:absolute; bottom:50; left:15;font-family: arial; font-weight: bold;">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                        // .append('<div style="position:absolute; bottom:40; right:15;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                                        // .append('<div style="position:absolute; bottom:10; right:15;font-family: arial; font-weight: bold;font-size:24px">Received Date: _____________________</div>')
                                    $(doc.document.body).find('table')            			
                                        .removeClass('dataTable')
                                        .css('font-size','24px') 
                                        .css('margin-top','370px')
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
                                    },
                                    columns: [ 2,3,4 ]
                                },
                                init: function(node) {$(node).removeClass('dt-button')},
                            }
                        ]
                    }
                });
        }else{
            table =
                $('table.defectiveTable').DataTable({ 
                    "dom": 'Blrtip',
                    processing: true,
                    serverSide: false,
                    destroy: true,
                    searching: false,
                    "language": {
                        "emptyTable": "No item/s for return",
                        "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
                    },
                    "pageLength": 25,
                    "order": [[ 2, "asc" ], [ 3, "asc" ]],
                    ajax: {
                        async: false,
                        url: '/return-table',
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
                        { data: 'customer_branch', name:'customer_branch'}
                    ],
                    columnDefs: [
                        {
                        targets: 0,
                        visible: false
                        },
                        {
                        targets: 3,
                        visible: false
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
                                titleAttr: 'Submit and print preview',
                                enabled: true,
                                autoPrint: true,
                                text: '<span class="icon text-white-50"><i class="fa fa-print" style="color:white"></i></span><span> SUBMIT</span>',
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
                                    .prepend('<div style="position:absolute; top:90; width:100%;left:30%;font-size:28px;font-weight: bold">CONVERSION DELIVERY RECEIPT</div>')
                                    //.prepend('<div style="position:absolute; top:90;margin: auto;font-size:16px;color: #0d1a80; font-family: arial; font-weight: bold;">Delivery receipt of defective units from '+$('#branchname').val()+'</div>')
                                    .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK MONITORING SYSTEM</div>')
                                    .prepend('<img style="position:absolute; top:400; left:300;font-size:20px;margin-botton:50px" src="'+window.location.origin+'/idsiwatermark.png">')
                                    .prepend('<div style="position:absolute; top:140;font-size:24px"><b>Prepared by:</b> '+$('#userlog').val()+'</div>')
                                    //.prepend('<div style="position:absolute; top:170;font-size:24px"><b>Area.:</b> '+$('#areaname').val()+'</div>')
                                    .prepend('<div style="position:absolute; top:140;left:60%;font-size:24px"><b>Date prepared:</b> '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    //.prepend('<div style="position:absolute; top:200;font-size:24px"><label for="textbranch"><b>Branch address:&nbsp;&nbsp;</b></label><textarea id="textbranch" style="vertical-align: top;resize: none;background: transparent;border:0 none" rows="3" cols="80" readonly>'+$('#addr').val()+'</textarea></div>')
                                    .prepend('<div style="position:absolute; top:170;font-size:24px"><b>Reference #: </b> '+retno+'</div>')
                                    .prepend('<div style="position:absolute; top:200;font-family: arial;font-size:24px">Received By: _____________________</div>')
                                    .prepend('<div style="position:absolute; top:230;font-family: arial;font-size:24px">Received Date: _____________________</div>')
                                //  .prepend('<div style="position:absolute; bottom:20; left:100;">Pagina '+page.toString()+' of '+pages.toString()+'</div>');
                                //jsDate.toString()
                                    // $(doc.document.body)
                                        //.append('<div style="position:absolute; bottom:80; left:15;font-family: arial; font-weight: bold;">Prepared By: '+$("#userlog").val()+'</div>')
                                        //.append('<div style="position:absolute; bottom:50; left:15;font-family: arial; font-weight: bold;">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                        // .append('<div style="position:absolute; bottom:40; right:15;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                                        // .append('<div style="position:absolute; bottom:10; right:15;font-family: arial; font-weight: bold;font-size:24px">Received Date: _____________________</div>')
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
                                    },
                                    columns: [ 1, 2 ]
                                },
                                init: function(node) {$(node).removeClass('dt-button')},
                            }
                        ]
                    }
                });
        }
        table.rows( status ).remove().draw();
        $('#returnBtn').hide();
        $('#addBtn').hide();
        table.buttons().container().appendTo('.printBtn');
        $('#loading').hide();
        $('#printrecBtn').hide();
    }
});
$(document).on("click", "#defectiveTable tbody tr", function () {
    if (table.row(this).data().status != "For return") {
        table.row($(this)).deselect()
    }
});
$(document).on('click', '#printrecBtn', function(){
    $('#loading').show();
    window.location.href = 'returnview';
});
$(document).on('click', '#submit_Btn', function(){
    if (sub > 0) {
        return false;
    }
    var branch = $('#branch_id').val();
    var id = $('#myid').val();
    var status = 'For receiving';
    var itemid = $('#return_id').val();
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
            status: status,
            itemid: itemid
        },
        success:function()
        {
            interval = setInterval(function(){
                table.draw();
            }, 30000);
            table.draw();
            $('#returnModal .close').click();
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
});
$(document).on('click', '.cancel', function(){
    location.reload();
});
$(document).on('click', '.close', function(){
    table.draw();
    location.reload();
});