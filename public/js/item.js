var table;
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
            "columnDefs": [
                {   
                    "render": function ( data, type, row, meta ) {
                        if (data.n_a == "no") {
                            return '<label class="switch"><input type="checkbox" class="togBtn" return_id="'+data.id+'" checked><div class="slider round"><span class="on">Yes</span><span class="off">No</span></div></label>'
                        //return '<button title="Click to change to no" id="yesBtn" class="btn-primary approveBtn"  style="border-radius: 5px">yes</button>'
                        }else if(data.n_a == "yes"){
                            return '<label class="switch"><input type="checkbox" class="togBtn" return_id="'+data.id+'"><div class="slider round"><span class="on">Yes</span><span class="off">No</span></div></label>'
                        //return '<button title="Click to change to yes" id="noBtn" class="btn-danger approveBtn" return_id="'+data.id+'" style="border-radius: 5px">no</button>'
                        }
                    },
                    "defaultContent": '',
                    "data": null,
                    "targets": [2]
                }
            ],
            columns: [
                { data: 'category', name:'category'},
                { data: 'item', name:'item'},
                { data: null}
            ]
        });

        $('.filter-input').keyup(function() {
            table.column( $(this).data('column'))
                .search( $(this).val())
                .draw();
        });
});

$(document).on('change', '.togBtn', function(){
    var returnid = $(this).attr('return_id');
    if ($(this).is(':checked')) {
        $.ajax({
            url: 'item-update',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'put',
            data: {
                item: returnid,
                stat: 'no'
            },
            success: function (data) {
                if (data) {
                    console.log('true');
                }
            },
            error: function(data) {
                $('.togBtn[return_id=\''+returnid+'\']').prop('checkbox', false);
            }
        });
    }else{
        $.ajax({
            url: 'item-update',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
            },
            dataType: 'json',
            type: 'put',
            data: {
                item: returnid,
                stat: 'yes'
            },
            success: function (data) {
                if (data) {
                    console.log('true');
                }
            },
            error: function(data) {
                $('.togBtn[return_id=\''+returnid+'\']').prop('checkbox', true);
            }
        });
    }
});