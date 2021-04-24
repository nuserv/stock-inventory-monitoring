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
            serverSide: true,
            ajax: {
                url: 'items',
            error: function(data) {
                    if(data.status == 401) {
                        window.location.href = '/login';
                    }
                }
            },
            "columnDefs": [
                {   
                    "render": function ( data, type, row, meta ) {
                        if (data.n_a == "yes") {
                            return '<button title="Click to change to no" id="yesBtn" class="btn-primary approveBtn" return_id="'+data.id+'" style="border-radius: 5px">yes</button>'
                        }else if(data.n_a == "no"){
                            return '<button title="Click to change to yes" id="noBtn" class="btn-danger approveBtn" return_id="'+data.id+'" style="border-radius: 5px">no</button>'
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
});