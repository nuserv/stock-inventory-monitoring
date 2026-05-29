var replaceTable;
var repdata;
var outsub = 0;
var r = 1;
var y = 1;
var clientselected = 'yes';
var billid;
var stocksid;
var ticketVerified = false;
var ticketChecking = false;
var verifiedTicket = '';
var ticketRequestId = 0;
var ticketAutoFilledBranch = false;
var serviceTicketPattern = /^[A-Z]{3}-\d{8}-\d{6}$/;
var TICKET_MESSAGES = window.SERVICE_TICKET_MESSAGES;

function getTicketValue()
{
    return formatTicketValue($('#ticket').val());
}

function formatTicketValue(value)
{
    var raw = $.trim(value).toUpperCase().replace(/[^A-Z0-9]/g, '');
    var match = raw.match(/^([A-Z]{0,3})(\d{0,8})(\d{0,6}).*$/);

    if (!match) {
        return raw;
    }

    var ticket = match[1];

    if (match[2]) {
        ticket += '-' + match[2];
    }

    if (match[2].length == 8 && match[3]) {
        ticket += '-' + match[3];
    }

    return ticket;
}

function isCompleteTicket(ticket)
{
    return serviceTicketPattern.test(ticket);
}

function hasLetterAfterTicketPrefix(value)
{
    var raw = $.trim(value).toUpperCase().replace(/[^A-Z0-9]/g, '');
    return raw.length > 3 && /[A-Z]/.test(raw.substring(3));
}

function setTicketStatus(status, message)
{
    var icon = '<i class="fa fa-minus text-muted"></i>';
    var textClass = 'text-muted';

    if (status == 'checking') {
        icon = '<i class="fa fa-spinner fa-spin text-primary"></i>';
        textClass = 'text-primary';
    } else if (status == 'valid') {
        icon = '<i class="fa fa-check text-success"></i>';
        textClass = 'text-success';
    } else if (status == 'invalid') {
        icon = '<i class="fa fa-times text-danger"></i>';
        textClass = 'text-danger';
    }

    $('#ticket-status-icon').html(icon);
    $('#ticket-status-message')
        .removeClass('text-muted text-primary text-success text-danger')
        .addClass(textClass)
        .text(message);
}

function resetTicketVerification(message)
{
    ticketVerified = false;
    ticketChecking = false;
    verifiedTicket = '';
    if (ticketAutoFilledBranch) {
        $('#client').val('');
        $('#customer').val('');
        ticketAutoFilledBranch = false;
    }
    setTicketStatus('idle', message || TICKET_MESSAGES.required);
    updateOutSubmitState();
}

function updateOutSubmitState()
{
    var canSubmit = $('#client').val() != '' && $('#customer').val() != '' && r != 1 && outsub <= 0 && ticketVerified && !ticketChecking;
    $('#out_sub_Btn').prop('disabled', !canSubmit);
}

function verifyTicket()
{
    var ticket = getTicketValue();
    ticketRequestId++;
    var currentRequestId = ticketRequestId;

    if (ticket == '') {
        resetTicketVerification(TICKET_MESSAGES.required);
        return false;
    }

    if (!isCompleteTicket(ticket)) {
        resetTicketVerification(TICKET_MESSAGES.incomplete);
        return false;
    }

    if (ticketVerified && verifiedTicket == ticket) {
        updateOutSubmitState();
        return true;
    }

    ticketVerified = false;
    ticketChecking = true;
    verifiedTicket = '';
    $('#ticket').val(ticket);
    setTicketStatus('checking', TICKET_MESSAGES.checking);
    updateOutSubmitState();

    $.ajax({
        url: 'verify-service-ticket',
        type: 'get',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        data: {
            ticket: ticket
        },
        success: function(data) {
            if (currentRequestId != ticketRequestId) {
                return;
            }

            ticketChecking = false;
            ticketVerified = data.verified === true;
            verifiedTicket = ticketVerified ? ticket : '';
            setTicketStatus(ticketVerified ? 'valid' : 'invalid', data.message || (ticketVerified ? TICKET_MESSAGES.verified : TICKET_MESSAGES.notFound));
            if (ticketVerified && ticket.indexOf('PLS') === 0 && data.branch_code) {
                autofillPlsBranch(data.branch_code);
            }
            updateOutSubmitState();
        },
        error: function(xhr) {
            if (currentRequestId != ticketRequestId) {
                return;
            }

            ticketChecking = false;
            ticketVerified = false;
            verifiedTicket = '';
            var message = TICKET_MESSAGES.verifyFailed;

            if (xhr.status == 404) {
                message = TICKET_MESSAGES.notFound;
            } else if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }

            setTicketStatus('invalid', message);
            updateOutSubmitState();
        }
    });

    return false;
}

function autofillPlsBranch(branchCode)
{
    $.ajax({
        url: 'hint',
        type: 'get',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        data: {
            client: 'plsi',
            branch: branchCode
        },
        success: function(data) {
            if (data && data.found) {
                $('#client').val(data.customer);
                $('#customer').val(data.customer_branch);
                ticketAutoFilledBranch = true;
                $('#branchlist').fadeOut();
                $('#clientlist').fadeOut();
                setTicketStatus('valid', TICKET_MESSAGES.branchAutoSelected);
            } else {
                $('#customer').val('');
                ticketAutoFilledBranch = false;
                setTicketStatus('valid', TICKET_MESSAGES.branchNotFound);
            }

            updateOutSubmitState();
        },
        error: function() {
            $('#customer').val('');
            ticketAutoFilledBranch = false;
            setTicketStatus('valid', TICKET_MESSAGES.branchNotFound);
            updateOutSubmitState();
        }
    });
}

$(document).on('input', '#ticket', function(){
    var rawTicket = $('#ticket').val();
    var ticket = getTicketValue();
    $('#ticket').val(ticket);

    if (ticketVerified && verifiedTicket == ticket) {
        updateOutSubmitState();
        return;
    }

    if (hasLetterAfterTicketPrefix(rawTicket) && !isCompleteTicket(ticket)) {
        resetTicketVerification(TICKET_MESSAGES.numbersOnlyAfterPrefix);
    } else if (rawTicket.indexOf('-') !== -1 && !isCompleteTicket(ticket)) {
        resetTicketVerification(TICKET_MESSAGES.noDashNeeded);
    } else {
        resetTicketVerification(ticket == '' ? TICKET_MESSAGES.required : TICKET_MESSAGES.changed);
    }

    if (isCompleteTicket(ticket)) {
        verifyTicket();
    }
});

$(document).on('focusout', '#ticket', function(){
    verifyTicket();
});

$(document).on('keydown', '#ticket', function(e){
    if (e.key === 'Enter' || e.keyCode == 13) {
        e.preventDefault();
        verifyTicket();
    } else if ((e.key === '-' || e.keyCode == 189) && !isCompleteTicket(getTicketValue())) {
        setTicketStatus('idle', TICKET_MESSAGES.noDashNeeded);
    } else if (/^[a-zA-Z]$/.test(e.key || '') && getTicketValue().replace(/-/g, '').length >= 3 && !isCompleteTicket(getTicketValue())) {
        setTicketStatus('idle', TICKET_MESSAGES.numbersOnlyAfterPrefix);
    }
});

$(function(){
    setTicketStatus('idle', TICKET_MESSAGES.required);
});

$(document).ready(function()
{
    sunit = $('table.billableTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
            "emptyTable": "No data found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> '
        },
        processing: true,
        serverSide: true,
        ajax: 'bill',
        columns: [
            { data: 'date', name:'date'},
            { data: 'client', name:'client'},
            { data: 'ticket', name:'ticket'},
            { data: 'description', name:'description'},
            { data: 'serial', name:'serial'},
            { data: 'status', name:'status'}
        ]
    });
});

$(document).on('click', '#reqBtn', function(){
    $('#service-unitModal').modal({backdrop: 'static', keyboard: false}); 
});

$(document).on('click', '#delBtn', function(e){
    if(confirm('Are you sure you want to delete this request?')) {
        e.preventDefault();
        $.ajax({
            type:'put',
            url:'delbill',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                billid:billid,
                stocksid:stocksid
            },
            success:function()
            {
                location.reload();
            },
        });
    }
});

$(document).on('click', '#doneBtn', function(e){
    $.ajax({
        type:'put',
        url:'prcbill',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        data:{
            billid:billid,
            stocksid:stocksid,
            status: 'Completed'
        },
        success:function()
        {   
            location.reload();
        },
    });
});
$(document).on('click', '.prcBtn', function(e){
    $.ajax({
        type:'put',
        url:'prcbill',
        dataType: 'json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        data:{
            billid:billid,
            stocksid:stocksid,
            status: 'Pending'
        },
        success:function()
        {   
            setTimeout(function () {    
                location.reload();
            }, 1000)
        },
    });
});

$(document).on('click', '#approveBtn', function(e){
    if(confirm('Are you sure you want to approve this request?')) {
        e.preventDefault();
        $.ajax({
            type:'put',
            url:'approvebill',
            dataType: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                billid:billid,
                stocksid:stocksid
            },
            success:function()
            {
                location.reload();
            },
        });
    }
});

$(document).on("click", "#billableTable tr", function () {
    trdata = sunit.row(this).data();
    if (trdata.user_id != $('#userid').val()) {
        if ($('#userlevel').val() != 'Warehouse Manager') {
            if ($('#userlevel').val() != 'Head') {
                return false;
            }
        }
    }
    billid = trdata.id;
    stocksid = trdata.stocks_id;
    $('#service-inModal').modal({backdrop: 'static', keyboard: false}); 
    $('#inclient').val(trdata.client_name.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#incustomer').val(trdata.customer_name.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#outitem').val(trdata.description.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#outserial').val(trdata.serial.replace(/&#039;/g, '\'').replace(/&quot;/g, '\"').replace(/&amp;/g, '\&').replace(/&AMP;/g, '\&'));
    $('#indate').val(trdata.date);
    $('#outstatus').val(trdata.status);
    if ($('#userlevel').val() == 'Warehouse Manager') {
        $('#inengr').val(trdata.serviceby);
        if (trdata.status == "Approved") {
            $('#approveBtn').remove();
        }
    }else{

        $('#inengr').val(trdata.serviceby);

        if (trdata.status == "Approved") {
            $('#delBtn').remove();
            $('#doneBtn').remove();
            $('#printBtn').remove();
            $('.prcBtn').show();
            var data = [
                [
                    trdata.description,
                    trdata.serial
                ]
            ]
            table = $('table.billitemTable').DataTable({ 
                "dom": 'Brtip',
                serverSide: false,
                destroy: true,
                data:data,
                buttons: {
                    buttons: [
                        {
                            extend: 'print',
                            className: 'btn btn-primary btn-icon-split',
                            titleAttr: 'PRINT',
                            enabled: false,
                            autoPrint: true,
                            text: '<span class="icon text-white-50"><i class="fa fa-print" style="color:white"></i></span><span> Proceed</span>',
                            customize: function (doc) {
                                var d = new Date();
                                var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
                                var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
                                var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
                                
                                $(doc.document.body)
                                    .prepend('<img style="position:absolute; top:10; left:20;width:100;margin-botton:50px" src="'+window.location.origin+'/idsi.png">')
                                    //.prepend('<div style="position:absolute; top:10; right:0;">My Title</div>')
                                    .prepend('<div style="position:absolute; top:90; width:100%;left:40%;font-size:28px;font-weight: bold"><b></b>DELIVERY RECEIPT<b></b></div>')
                                    //.prepend('<div style="position:absolute; top:90;margin: auto;font-size:16px;color: #0d1a80; font-family: arial; font-weight: bold;">Delivery receipt of defective units from '+$('#branchname').val()+'</div>')
                                    .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK MONITORING SYSTEM</div>')
                                    .prepend('<img style="position:absolute; top:400; left:300;font-size:20px;margin-botton:50px" src="'+window.location.origin+'/idsiwatermark.png">')
                                    .prepend('<div style="position:absolute; top:140;font-size:24px"><b>Date:</b> '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    .prepend('<div style="position:absolute; top:200;font-size:24px"><b>Client Name:</b> '+$('#incustomer').val()+'</div>')
                                    .prepend('<div style="position:absolute; top:170;font-size:24px"><label for="textbranch"><b>Service By: '+$('#branchname').val()+' - '+$('#userlog').val()+'</div>')
                                    // .prepend('<div style="position:absolute; top:230;font-family: arial; font-weight: bold;font-size:24px">Prepared By: '+$("#userlog").val()+'</div>')
                                    // .prepend('<div style="position:absolute; top:260;font-family: arial; font-weight: bold;font-size:24px">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    // .prepend('<div style="position:absolute; top:230;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                                    // .prepend('<div style="position:absolute; top:260;font-family: arial; font-weight: bold;font-size:24px">Received Date: _____________________</div>')
                                            //  .prepend('<div style="position:absolute; bottom:20; left:100;">Pagina '+page.toString()+' of '+pages.toString()+'</div>');
                                //jsDate.toString()
                                // $(doc.document.body)
                                //     //.append('<div style="position:absolute; bottom:80; left:15;font-family: arial; font-weight: bold;">Prepared By: '+$("#userlog").val()+'</div>')
                                //     //.append('<div style="position:absolute; bottom:50; left:15;font-family: arial; font-weight: bold;">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    .append('<div style="position:absolute; bottom:80; right:15;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                                    .append('<div style="position:absolute; bottom:50; right:15;font-family: arial; font-weight: bold;font-size:24px">Date: _____________________</div>')
                                $(doc.document.body).find('table')            			
                                    .removeClass('dataTable')
                                    .css('font-size','24px') 
                                    .css('margin-top','270px')
                                    .css('margin-bottom','250px')
                                $(doc.document.body).find('th').each(function(index){
                                    $(this).css('font-size','26px');
                                    $(this).css('color','black');
                                    $(this).css('background-color','F0F0F0');
                                });                
                            },
                            title:'',
                            exportOptions: {
                                rows: function ( idx, data, node ) {
                                    var dt = new $.fn.dataTable.Api('#billitemTable');
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
            table.buttons().container().appendTo('.prcBtn');
            table.button( 0 ).enable( true );
        }
        if (trdata.status == "Pending") {
            $('#delBtn').remove();
            $('.prcbtn').remove();
            var data = [
                [
                    trdata.description,
                    trdata.serial
                ]
            ]
            table = $('table.billitemTable').DataTable({ 
                "dom": 'Brtip',
                serverSide: false,
                destroy: true,
                data:data,
                buttons: {
                    buttons: [
                        {
                            extend: 'print',
                            className: 'btn btn-primary btn-icon-split',
                            titleAttr: 'PRINT',
                            enabled: false,
                            autoPrint: true,
                            text: '<span class="icon text-white-50"><i class="fa fa-print" style="color:white"></i></span><span> Reprint</span>',
                            customize: function (doc) {
                                var d = new Date();
                                var hour = String(d.getHours()).padStart(2, '0') % 12 || 12
                                var ampm = (String(d.getHours()).padStart(2, '0') < 12 || String(d.getHours()).padStart(2, '0') === 24) ? "AM" : "PM";
                                var months = ["January","February","March","April","May","June","July","August","September","October","November","December"];
                                
                                $(doc.document.body)
                                    .prepend('<img style="position:absolute; top:10; left:20;width:100;margin-botton:50px" src="'+window.location.origin+'/idsi.png">')
                                    //.prepend('<div style="position:absolute; top:10; right:0;">My Title</div>')
                                    .prepend('<div style="position:absolute; top:90; width:100%;left:40%;font-size:28px;font-weight: bold"><b></b>DELIVERY RECEIPT<b></b></div>')
                                    //.prepend('<div style="position:absolute; top:90;margin: auto;font-size:16px;color: #0d1a80; font-family: arial; font-weight: bold;">Delivery receipt of defective units from '+$('#branchname').val()+'</div>')
                                    .prepend('<div style="position:absolute; top:40; left:125;font-size:28px;color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK MONITORING SYSTEM</div>')
                                    .prepend('<img style="position:absolute; top:400; left:300;font-size:20px;margin-botton:50px" src="'+window.location.origin+'/idsiwatermark.png">')
                                    .prepend('<div style="position:absolute; top:140;font-size:24px"><b>Date:</b> '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    .prepend('<div style="position:absolute; top:200;font-size:24px"><b>Client Name:</b> '+$('#incustomer').val()+'</div>')
                                    .prepend('<div style="position:absolute; top:170;font-size:24px"><label for="textbranch"><b>Service By: '+$('#branchname').val()+' - '+$('#userlog').val()+'</div>')
                                    // .prepend('<div style="position:absolute; top:230;font-family: arial; font-weight: bold;font-size:24px">Prepared By: '+$("#userlog").val()+'</div>')
                                    // .prepend('<div style="position:absolute; top:260;font-family: arial; font-weight: bold;font-size:24px">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    // .prepend('<div style="position:absolute; top:230;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                                    // .prepend('<div style="position:absolute; top:260;font-family: arial; font-weight: bold;font-size:24px">Received Date: _____________________</div>')
                                            //  .prepend('<div style="position:absolute; bottom:20; left:100;">Pagina '+page.toString()+' of '+pages.toString()+'</div>');
                                //jsDate.toString()
                                // $(doc.document.body)
                                //     //.append('<div style="position:absolute; bottom:80; left:15;font-family: arial; font-weight: bold;">Prepared By: '+$("#userlog").val()+'</div>')
                                //     //.append('<div style="position:absolute; bottom:50; left:15;font-family: arial; font-weight: bold;">Prepared Date: '+months[d.getMonth()]+' '+d.getDate()+', ' +d.getFullYear()+' '+hour+':'+String(d.getMinutes()).padStart(2, '0')+ampm+'</div>')
                                    .append('<div style="position:absolute; bottom:80; right:15;font-family: arial; font-weight: bold;font-size:24px">Received By: _____________________</div>')
                                    .append('<div style="position:absolute; bottom:50; right:15;font-family: arial; font-weight: bold;font-size:24px">Date: _____________________</div>')
                                $(doc.document.body).find('table')            			
                                    .removeClass('dataTable')
                                    .css('font-size','24px') 
                                    .css('margin-top','270px')
                                    .css('margin-bottom','250px')
                                $(doc.document.body).find('th').each(function(index){
                                    $(this).css('font-size','26px');
                                    $(this).css('color','black');
                                    $(this).css('background-color','F0F0F0');
                                });                
                            },
                            title:'',
                            exportOptions: {
                                rows: function ( idx, data, node ) {
                                    var dt = new $.fn.dataTable.Api('#billitemTable');
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
            table.buttons().container().appendTo('.printBtn');
            table.button( 0 ).enable( true );
        }
        if (trdata.status == "For approval") {
            $('.prcBtn').remove();
            $('#doneBtn').remove();
            $('#printBtn').remove();
            $('#inengr').val(trdata.serviceby)
        }
    }
});
$(document).on('click', '#clientdiv', function () {
   $('#client').prop('disabled', false);
   if ($('#client').is(':disabled')) { 
        clientselected = 'no';
   }
});

$(document).on('keyup', '#customer', function(){ 
    ticketAutoFilledBranch = false;
    var withclient = 'no';
    var clientname = "";
    $('#clientlist').fadeOut();  
    if ($('#client').is(':enabled')) {
        if ($('#client').val()) {
            withclient = 'yes';
            clientname = $('#client').val();
            if (clientselected != "yes") {
                alert("Incorrect Client Name!");
            }
        }else{
            $('#client').val('');
        }
    }
    var query = $(this).val();
    var ul = '<ul class="dropdown-menu" style="display:block; position:relative;overflow: scroll;height: 13em;z-index: 200;">';
    if(query != ''){
        $.ajax({
            url:"hint",
            type:"get",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                hint:query,
                withclient: withclient,
                clientname: clientname,
            },
            success:function(data){
                var datas = $.map(data, function(value, index) {
                    return [value];
                });
                datas.forEach(value => {
                    ul+='<li style="color:black" id="licustomer">'+value.customer_branch+'</li>';
                });
                console.log(ul);
                $('#branchlist').fadeIn();  
                $('#branchlist').html(ul);
                updateOutSubmitState();
            }
        });
    }
});
$(document).on('click', 'li', function(){  
    var select = $(this).text();
    var id = $(this).attr('id');
    if (id == 'licustomer') {
        ticketAutoFilledBranch = false;
        $('#customer').val($(this).text());  
        $('#branchlist').fadeOut();  
        $.ajax({
            url:"hint",
            type:"get",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                client:'yes',
                branch: select.trim()
            },
            success:function(data){
                if (data) {
                    $('#client').val(data);  
                    updateOutSubmitState();
                }else{
                    $('#client').val('');  
                    updateOutSubmitState();
                }
            }
        });
    }else{
        clientselected = "yes";
        $('#client').val($(this).text());  
        $('#clientlist').fadeOut();
        updateOutSubmitState();
    }
    
});
$(document).on('keyup', '#client', function(){ 
    var query = $(this).val();
    clientselected = 'no';
    $('#branchlist').fadeOut();  
    updateOutSubmitState();
    var ul = '<ul class="dropdown-menu" style="display:block; position:relative;overflow: scroll;height: 13em;z-index: 200;">';
    if(query != ''){
        $.ajax({
            url:"getclient",
            type:"get",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data:{
                hint:query,
            },
            success:function(data){
                var datas = $.map(data, function(value, index) {
                    return [value];
                });
                datas.forEach(value => {
                    ul+='<li style="color:black" id="liclient">'+value.customer+'</li>';
                });
                $('#clientlist').fadeIn();  
                $('#clientlist').html(ul);
                $('#customer').val('');  
            }
        });
    }
});

$(document).on('change', '.outcategory', function(){
    var descOp = " ";
    var count = $(this).attr('row_count');
    var id = $(this).val();
    $.ajax({
        type:'get',
        url:'itemcode',
        data:{'id':id},
        success:function(data)
        {
            var itemcode = $.map(data, function(value, index) {
                return [value];
            });
            descOp+='<option selected disabled>select item description</option>';
            itemcode.forEach(value => {
                descOp+='<option value="'+value.id+'">'+value.item.toUpperCase()+'</option>';
            });
            $("#outdesc" + count).find('option').remove().end().append(descOp);
        },
    });
});

$(document).on('change', '.outitem', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();        
    $('#outdesc' + count).val(id);
});

$(document).on('change', '.outdesc', function(){
    var count = $(this).attr('row_count');
    var id = $(this).val();
    var stockCount = 0;
    var serialOp = " ";
    
    for(var i=1;i<=y;i++){
        if (i != count ) {
            if ($('#outdesc'+i).val() == $(this).val()) {
                stockCount++;
            }
        }
    }
    Promise.all([ajaxCall1(), ajaxCall2()]).then(() => { // try removing ajax 1 or replacing with ajax2
        for(var i=1;i<=y;i++){
            if ($('#outdesc'+i).val() == $(this).val()) {
                rmserial = $('#outserial'+i).val();
                //$("#outserial"+count+" option[value=\'"+rmserial+"\']").remove();
            }
        }
    });
    
    function ajaxCall1() {
        return $.ajax({
            type:'get',
            url:'getstock',
            data:{'id':id},
            success:function(data)
            {
                if (data != "") {
                    $('#outstock' + count).val(data[0].stock - stockCount);
                    $('#outstock' + count).css('color', 'black');
                    $('#outstock' + count).css("border", "");
                    if ($('#outstock' + count).val() <= 0) {
                        $('#outstock' + count).css('color', 'red');
                        $('#outstock' + count).css("border", "5px solid red");
                    }
                }else{
                    $('#outstock' + count).val('0');
                    $('#outstock' + count).css('color', 'red');
                    $('#outstock' + count).css("border", "5px solid red");
                }
            },
        });
    }
    function ajaxCall2() {
        return $.ajax({
            type:'get',
            url:'getserials',
            data:{'id':id},
            success:function(data)
            {
                var serial = $.map(data, function(value, index) {
                    return [value];
                });
                serialOp+='<option selected disabled>select serial</option>';
                for(var i=1;i<=y;i++){
                    if ($('#outdesc'+i).val() == id) {
                        rmserial = $('#outserial'+i).val();
                        $.each(serial, function(idx, item) {
                            if (item.serial == rmserial) {
                                serial.splice(idx, 1); // Remove current item
                                return false; // End the loop
                            }
                        });
                    }
                }
                serial.forEach(value => {
                    serialOp+='<option value="'+value.serial+'">'+value.serial+'</option>';
                });
                $("#outserial" + count).find('option').remove().end().append(serialOp);
            },
        });
    }
    
});

$(document).on('click', '.out_add_item', function(){
    var rowcount = $(this).attr('btn_id');
    if ($(this).val() == 'Add Item') {
        if($('#outcategory'+ rowcount).val() && $('#outdesc'+ rowcount).val() && $('#outserial'+ rowcount).val()) {
            y++;
            var additem = '<div class="row no-margin" id="outrow'+y+'"><div class="col-md-2 form-group"><select style="color:black" id="outcategory'+y+'" class="form-control outcategory" row_count="'+y+'"></select></div><div class="col-md-3 form-group"><select style="color:black" id="outdesc'+y+'" class="form-control outdesc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><select id="outserial'+y+'" class="form-control outserial" row_count="'+y+'" style="color: black;"><option selected disabled>select serial</option></select></div><div class="col-md-1 form-group"><input type="number" class="form-control" min="0" name="outstock'+y+'" id="outstock'+y+'" placeholder="0" style="color:black; width: 6em" disabled></div><div class="col-md-1 form-group"><input type="button" class="out_add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            $(this).val('Remove');
            $('#outcategory'+ rowcount).prop('disabled', true);
            $('#outdesc'+ rowcount).prop('disabled', true);
            $('#outserial'+ rowcount).prop('disabled', true);
            if (r < 20 ) {
                $('#outfield').append(additem);
                $('#outcategory'+ rowcount).find('option').clone().appendTo('#outcategory'+y);
                r++;
            }
        }
    }else{
        if (r == 20) {
            y++;
            var additem = '<div class="row no-margin" id="outrow'+y+'"><div class="col-md-2 form-group"><select id="outcategory'+y+'" class="form-control outcategory" row_count="'+y+'"></select></div><div class="col-md-3 form-group"><select id="outdesc'+y+'" class="form-control outdesc" row_count="'+y+'"><option selected disabled>select item description</option></select></div><div class="col-md-2 form-group"><select id="outserial'+y+'" class="form-control outserial" row_count="'+y+'" style="color: black;"><option selected disabled>select serial</option></select></div><div class="col-md-1 form-group"><input type="number" class="form-control" min="0" name="outstock'+y+'" id="outstock'+y+'" placeholder="0" style="color:black; width: 6em" disabled></div><div class="col-md-1 form-group"><input type="button" class="out_add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
            $('#outfield').append(additem);
            $('#outcategory'+ rowcount).find('option').clone().appendTo('#outcategory'+y);
            r++;
        }
        $('#outcategory'+rowcount).val('select category');
        $('#outdesc'+rowcount).val('select item description');
        $('#outserial'+rowcount).val('select serial');
        $('#outcategory'+rowcount).prop('disabled', false);
        $('#outdesc'+rowcount).prop('disabled', false);
        $('#outserial'+rowcount).prop('disabled', false);
        $('#outrow'+rowcount).hide();
        $(this).val('Add Item');
        r--;
    }
    if (r == 1) {
        updateOutSubmitState();
    }else{
        updateOutSubmitState();
    }
});
$(document).on('click', '.out_sub_Btn', function(){
    if ($('#client').val() == "") {
        alert('Invalid Client Name!');
        return false;
    }
    if ($.trim($('#ticket').val()) == "") {
        alert(TICKET_MESSAGES.required);
        return false;
    }
    if (!ticketVerified || verifiedTicket != getTicketValue()) {
        alert(TICKET_MESSAGES.verifyFirst);
        return false;
    }
    if (r == 1 || outsub > 0) {
        alert('Please add item/s.');
        return false;
    }
    var cat = "";
    var item = "";
    var check = 1;
    //if ($('#customer-id').val() != "") {
        $('#service-unitModal').modal('toggle');
        $('#loading').show();
        for(var q=1;q<=y;q++){
            if ($('#outrow'+q).is(":visible")) {
                if ($('.out_add_item[btn_id=\''+q+'\']').val() == 'Remove') {
                    check++;
                    outsub++;
                    $('.out_sub_Btn').prop('disabled', true)
                    cat = $('#outcategory'+q).val();
                    item = $('#outdesc'+q).val();
                    serial = $('#outserial'+q).val();
                    purpose = 'billable';
                    $.ajax({
                        url:"getcustomerid",
                        type:"get",
                        async:false,
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                        },
                        data:{
                            customer:$('#customer').val(),
                        },
                        success:function(data){
                            var customer = data.id;
                            var client = data.customer_id;
                            $.ajax({
                                url: 'service-out',
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                                },
                                async:false,
                                dataType: 'json',
                                type: 'PUT',
                                data: {
                                    item: item,
                                    cat : cat,
                                    purpose: purpose,
                                    serial: serial,
                                    customer: customer,
                                    client: client,
                                    ticket: $.trim($('#ticket').val()).toUpperCase()
                                },
                                error: function (data) {
                                    alert(data.responseText);
                                }
                            });
                        }
                    });
                    
                }
            }
            if (q==y) {
                window.location.href = 'billable';
            }
        }
    /*}else{
        alert("Invalid Customer Name!");
        return false;
    }*/
    
});
$(document).on('click', '.close', function(){
    location.reload();
});
