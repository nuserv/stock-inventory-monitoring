var sunit;
var service = '';
var serial = '';
var desc = '';
var status = '';
var trdata;
var repdesc, customer_branch_data, item_data, user_data;

function fetchCustomerBranch(BranchCode, Column){
    var specificId = BranchCode;
    var specificRow = customer_branch_data.find(row => parseInt(row.branch_id) === parseInt(specificId));
    // console.log(specificId+'--'+specificRow[Column])
    if(!specificRow){
        return '';
    }
    else{
        return specificRow[Column] ? specificRow[Column] : '';
    }
}

function fetchItem(ItemCode, Column){
    var specificId = ItemCode;
    var specificRow = item_data.find(row => parseInt(row.item_code) === parseInt(specificId));
    if(!specificRow){
        return '';
    }
    else{
        return specificRow[Column] ? specificRow[Column] : '';
    }
}

function fetchUser(UserId, Column){
    var specificId = UserId;
    var specificRow = user_data.find(row => parseInt(row.user_id) === parseInt(specificId));
    // console.log(specificId+'--'+specificRow[Column])
    if(!specificRow){
        return '';
    }
    else{
        return specificRow[Column] ? specificRow[Column] : '';
    }
}
$('#loading').show();
function getCurrentDate() {
    var currentDate = new Date();
    var formattedDate = currentDate.getFullYear() + '-' + padNumber(currentDate.getMonth() + 1) + '-' + padNumber(currentDate.getDate());
    return formattedDate;
}
function padNumber(number) {
    return (number < 10 ? '0' : '') + number;
}
$(document).ready(function()
{
    $.ajax({
        url: '/get_customer_branch',
        method: 'GET',
        async: false,
        success: function(response){
            customer_branch_data = response.data;
        }
    });
    $.ajax({
        url: '/get_item',
        method: 'GET',
        async: false,
        success: function(response){
            item_data = response.data;
        }
    });
    $.ajax({
        url: '/get_user',
        method: 'GET',
        async: false,
        success: function(response){
            user_data = response.data;
        }
    });
        
    sunit = $('table.sUnitTable').DataTable({ 
        "dom": 'Bflrtip',
        buttons: [
            { 
                extend: 'excel',
                className: 'excelButton', 
                title: 'Service Monitoring -'+ getCurrentDate(),
                text: 'Export'
            }
        ],
        "language": {
            "emptyTable": "No data found!",
            "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> '
        },
        processing: false,
        serverSide: false,
        ajax: 'serviceMonitoring',
        columns: [
            {
                data: 'updated_at',
                render: function(data, type, row) {
                    if (type === 'display' || type === 'filter') {
                        var date = new Date(data);
                        var formattedDateTime = date.toLocaleString('en-US', { month: 'short', day: '2-digit', year: 'numeric', hour: 'numeric', minute: 'numeric', second: 'numeric' });
                        formattedDateTime = formattedDateTime.replace(' at', '');
                        return '<div class="formatted-date" style="width:90px">' + formattedDateTime + '</div>';
                    }
                    return data; // Return the original data for sorting and other purposes
                },
                "width": "20%"
            },
            // { data: 'client', name:'client'},
            {
                data: null,
                render: function(data, type, row, meta){
                    var cell_value = fetchCustomerBranch(row.customer_branches_id, 'customer_name')+ '-' + fetchCustomerBranch(row.customer_branches_id, 'branch_name');
                    return `<div style="white-space: normal; width: 250px;">${cell_value.toUpperCase()}</div>`;
                }
            },
            // { data: 'description', name:'description'},
            {
                data: null,
                render: function(data, type, row, meta){
                    var cell_value = fetchItem(row.items_id, 'item_name');
                    return `<div style="white-space: normal; width: 250px;">${cell_value.toUpperCase()}</div>`;
                }
            },
            { data: 'serial', name:'serial'},
            { data: 'status', "render": function ( data, type, row, meta ) {
                    if (data == "PULL OUT") {
                        return `<div style="white-space: normal; width: 100px;">${cell_value.toUpperCase()}</div>`;
                    }
                    else{
                        return `<div style="white-space: normal; width: 100px;">${data.toUpperCase()}</div>`;
                    }
                },
            },
            // { data: 'client', name:'client'},
            {
                data: null,
                render: function(data, type, row, meta){
                    var cell_value = fetchUser(row.user_id, 'branch_name');
                    return `<div style="white-space: normal; width: 100px;">${cell_value.toUpperCase()}</div>`;
                }
            },
            // { data: 'serviceby', name:'serviceby'}
            {
                data: null,
                render: function(data, type, row, meta){
                    var cell_value = fetchUser(row.user_id, 'user_name')+' '+fetchUser(row.user_id, 'user_middlename')+' '+fetchUser(row.user_id, 'user_lastname');
                    return `<div style="white-space: normal; width: 150px;">${cell_value.toUpperCase()}</div>`;
                }
            },
        ],
        "initComplete": function(settings, json) {
            $('#loading').hide();
        }
    });
});