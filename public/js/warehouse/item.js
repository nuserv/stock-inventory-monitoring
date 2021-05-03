var table;
var trdata;
$(document).ready(function()
{    
    table =
        $('table.itemTable').DataTable({ 
            "dom": 'lrtip',
            "language": {
                "emptyTable": "No data found!"
            },
            processing: true,
            serverSide: false,
            ajax: {
                url: 'items',
            error: function(data) {
                    if(data.status == 401) {
                        window.location.href = '/login';
                    }
                }
            },
            /*"fnRowCallback": function(nRow, aData) {
                if (aData.n_a == "yes") {
                    $('#togBtn[return_id=\''+aData.id+'\']').click()
                }else{
                }
            },*/
            columns: [
                { data: 'category', name:'category'},
                { data: 'item', name:'item'}
            ]
        });

        $('.filter-input').keyup(function() {
            table.column( $(this).data('column'))
                .search( $(this).val())
                .draw();
        });
});


$(document).on("click", "#itemTable tbody tr", function(){
    trdata = table.row(this).data();
    var id = trdata.id;
    $('#itemname').val(trdata.item);
    $('#editModal').modal({
        backdrop: 'static',
        keyboard: false
    });
});

$(document).on("keyup", "#itemname", function(){
    if (trdata.item == $(this).val()) {
        $('#sub_Btn').prop('disabled', true);
    }else{
        $('#sub_Btn').prop('disabled', false);
    }
});
$(document).on("click", "#sub_Btn", function(){
    $('#loading').show();
    $('#editModal').modal('hide');
    $.ajax({
        type:'put',
        url:'items-edit',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
        },
        data:{
            'id':trdata.id,
            'item': $('#itemname').val()
        },
        success:function(data)
        {
            location.reload();
        },
        error: function (data) {
            if(data.status == 401) {
                window.location.href = '/login';
            }
            alert(data.responseText);
        }
    });
});
$(document).on("click", ".cancel", function(){
    location.reload();
});
