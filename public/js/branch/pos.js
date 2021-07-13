$(document).ready(function()
{
    table =
        $('table.posTable').DataTable({ 
            "dom": 'lrtip',
            processing: true,
            serverSide: false,
            "language": {
                "emptyTable": "No item/s for return found!",
                "processing": '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Searching...</span> ',
            },
            "pageLength": 25,
            ajax: {
                url: '/postable',
                error: function(data) {
                    if(data.status == 401) {
                        window.location.href = '/login';
                    }
                }
            },
            columns: [
                { data: 'date', name:'date'},
                { data: 'drno', name:'drno'},
                { data: 'pos_model', name:'pos_model'},
                { data: 'serial', name:'serial'},
                { data: 'customer', name:'customer'}
            ]
        });
});