var table;
var bID;
$(document).ready(function()
{
    var url = window.location.pathname;
    var id = url.substring(url.lastIndexOf('/') + 1);
    bID = id;
    table =
    $('table.customerbranchTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
                "emptyTable": " "
            },
        processing: true,
        serverSide: true,
        ajax: "/customerbranch-list/"+id,
        columns: [
            { data: 'code', name:'code'},
            { data: 'customer_branch', name:'customer_branch',},
            { data: 'contact', name:'contact'},
            { data: 'status', name:'status',}
        ]
    });

    $('#search-ic').on("click", function () { 
        for ( var i=0 ; i<=3 ; i++ ) {
            
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
    $('#branchBtn').val('ADD \"'+$('#name').val()+'\" BRANCH')
});

$(document).on("click", "#customerbranchTable tr", function () {
    
    var trdata = table.row(this).data();
    var id = trdata.id;
    $('#saveBtn').val('Update');
    $('#myid').val(id);
    $('#branch_code').val(trdata.code);
    $('#branch_name').val(trdata.customer_branch);
    $('#address').val(trdata.address);
    $('#number').val(trdata.contact);
    $('.status').show();
    if(trdata.status == "Active"){
        $('#status').val('1');
    }else{
        $('#status').val('0');
    }
    $('#customerbranchModal').modal('show');
    
});

$('#branchBtn').on("click", function(){

    $('#saveBtn').val('Save');
    $('#branch_code').val('');
    $('#branch_name').val('');
    $('#number').val('');
    $('#address').val('');
    $('#subBtn').val('Save');
    $('#myid').val(bID);
    $('.status').hide();
    $('#customerbranchModal').modal('show');
});

$(document).on('click', '#saveBtn', function(){ 
    var subBtn = $('#saveBtn').val();
    var bcode = $('#branch_code').val();
    var bname = $('#branch_name').val();
    var number = $('#number').val();
    var address = $('#address').val();
    var bid = bID;
    var id = $('#myid').val();
    if(subBtn == 'Save'){
        $.ajax({
            type: "POST",
            url: "../cbranch_add",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data: {
                bcode: bcode,
                bname: bname,
                number: number,
                address: address,
                bid: bid
            },
            success: function(data){
                if(data > '0'){
                    table.draw();
                    $('#customerbranchModal .close').click();
                }else{
                    alert("Branch already registered");
                }
            },
            error: function (data) {
                alert(data.responseText);
            }
        });
    }

    if(subBtn == 'Update'){
        var status = $('#status').val();
        $.ajax({
            type: "Put",
            url: "../cbranch_update",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data: {
                bcode: bcode,
                bname: bname,
                number: number,
                address: address,
                status: status,
                id: id,
                bid: bid
            },
            success: function(data){
                if(data > '0'){
                    table.draw();
                    $('#customerbranchModal .close').click();
                }else{
                    alert("Branch already registered");
                }
            },
            error: function (data) {
                alert(data.responseText);
            }
        });
    }
});