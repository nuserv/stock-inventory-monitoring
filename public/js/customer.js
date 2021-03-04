var customertable;
$(document).ready(function()
{
    customertable =
    $('table.customerTable').DataTable({ 
        "dom": 'lrtip',
        "language": {
                "emptyTable": " ",
                "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> '
            },
        processing: true,
        serverSide: true,
        ajax: 'customer-list',
        columns: [
            { data: 'code', name:'code'},
            { data: 'customer', name:'customer',}
        ]
    });

    $('#search-ic').on("click", function () { 
        for ( var i=0 ; i<=6 ; i++ ) {
            
            $('.fl-'+i).val('').change();
            customertable
            .columns(i).search( '' )
            .draw();
        }
        $('.tbsearch').toggle();
        
    });

    $('.filter-input').keyup(function() { 
        customertable.column( $(this).data('column'))
            .search( $(this).val())
            .draw();
    });
});

$(document).on("click", "#customerTable tr", function () {
    
    var trdata = customertable.row(this).data();
    var id = trdata.id;
    if($('#editBtn').val() == 'Cancel'){
        $('#myid').val(id);
        $('#subBtn').val('Update');
        $('#customer_code').val(trdata.code);
        $('#customer_name').val(trdata.customer);
        $('#customerModal').modal('show');
    }else{
    window.location.href = 'customer/'+id;
    }
});

$('#customerBtn').on("click", function(){

    $('#customer_code').val('');
    $('#customer_name').val('');
    $('#subBtn').val('Save');
    $('#customerModal').modal('show');
    $('#editBtn').val('Edit Customer Details');
});

$('#editBtn').on("click", function(){
    if($(this).val() == 'Cancel'){
        $('#editBtn').val('Edit Customer Details');
    }else{
        $('#editBtn').val('Cancel');
    }
});


$('#customerForm').on('submit', function(e){ 
    e.preventDefault();
    subBtn = $('#subBtn').val();
    if(subBtn == 'Update'){
        $.ajax({
            type: "PUT",
            url: "customer_add",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data: $('#customerForm').serialize(),
            success: function(data){
                if(data > '0'){
                    customertable.draw();
                    $('#customerModal .close').click();
                }else{
                    alert("Customer already registered");
                }
            },
            error: function (data) {
                alert(data.responseText);
            }
        });
    }

    if(subBtn == 'Save'){
        $.ajax({
            type: "POST",
            url: "customer_add",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            data: $('#customerForm').serialize(),
            success: function(data){
                if(data > '0'){
                    customertable.draw();
                    $('#customerModal .close').click();
                }else{
                    alert("Customer already registered");
                }
            },
            error: function (data) {
                alert(data.responseText);
            }
        });
    }
});
