var stockTable;
    var table;
    $(document).on('click', function (e) 
    {
        $('[data-toggle="popover"]').each(function () {
            if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
                if ($(this).data('bs.popover')) {
                    (($(this).popover('hide').data('bs.popover') || {}).inState || {}).click = false  
                }
            }
        });
    });

    $(document).ready(function()
    {
        $('#saveBtn').hide();

        table =
        $('table.branchTable').DataTable({ 
            "dom": 'lrtip',
            "language": {
                "emptyTable": " "
            },
            processing: true,
            serverSide: true,
            ajax: {
                url: 'branches',
                error: function(data, error, errorThrown) {
                    if(data.status == 401) {
                        window.location.href = '/login';
                    }
                }
            },
            columns: [
                { data: 'branch', name:'branch', "width": "14%" },
                { data: 'area', name:'area', "width": "14%"},
                { data: 'head', name:'head', "width": "14%"},
                { data: 'phone', name:'phone',"width": "14%"},
                { data: 'email', name:'email',"width": "14%"},
                { data: 'status', name:'status', "width": "14%"},
                { data: 'address', name:'address', "width": "14%"}
            ]
        });

        $('#branchTable tbody').on('click', 'tr', function () { 
            var dtdata = $('#branchTable tbody tr:eq(0)').data();
            var trdata = table.row(this).data();
            var id = trdata.id;
            console.log(trdata.id);
            $('table.branchDetails').dataTable().fnDestroy();
            $('#table').show();
            stockTable =
            $('table.branchDetails').DataTable({ 
                "dom": 'lrtip',
                "language": {
                    "emptyTable": " "
                },
                processing: true,
                serverSide: true,
                ajax: "/stocks/"+id,
                
                columns: [
                    { data: 'item', name:'item', "width": "17%"},
                    { data: 'initial', name:'initial', "width": "17%"},
                    { data: 'available', name:'available', "width": "14%"},
                    { data: 'stock_out', name:'stock_out', "width": "14%"}
                ]
            });
            $('#branch_name').prop('disabled', true);
            $('#address').prop('disabled', true);
            $('#area').prop('disabled', true);
            $('#contact_person').prop('disabled', true);
            $('#mobile').prop('disabled', true);
            $('#email').prop('disabled', true);
            $('#status').prop('disabled', true);
            $('#myid').val(trdata.id);
            $('#branch_name').val(trdata.branch);
            $('#address').val(trdata.address);
            $('#area').val(trdata.area_id);
            $('#contact_person').val(trdata.head);
            $('#mobile').val(trdata.phone);
            $('#email').val(trdata.email);
            $('#status').val(dtdata.dataStatus);
            $('#myid').val(trdata.id);
            $('#editBtn').val('Edit');
            $('#editBtn').show();
            $('#saveBtn').hide();
            $('#branchModal').modal('show');
        });

        $('#addBtn').on('click', function(e){ 
            e.preventDefault();
            $('#branchModal').modal('show');
            $('#branch_name').val('');
            $('#address').val('');
            $('#area').val('select area');
            $('#contact_person').val('');
            $('#mobile').val('');
            $('#email').val('');
            $('#status').val('select status');
            $('#branch_name').prop('disabled', false);
            $('#address').prop('disabled', false);
            $('#area').prop('disabled', false);
            $('#contact_person').prop('disabled', false);
            $('#mobile').prop('disabled', false);
            $('#email').prop('disabled', false);
            $('#status').prop('disabled', false);
            $('#editBtn').val('Save');
            $('#editBtn').hide();
            $('#saveBtn').show();
            $('#table').hide();
            
        });

        $('#editBtn').on('click', function(){
            $('#branch_name').prop('disabled', false);
            $('#address').prop('disabled', false);
            $('#area').prop('disabled', false);
            $('#contact_person').prop('disabled', false);
            $('#mobile').prop('disabled', false);
            $('#email').prop('disabled', false);
            $('#status').prop('disabled', false);
            $('#editBtn').hide();
            $('#saveBtn').show();
        });

        $('#branchForm').on('submit', function(e){ 
            e.preventDefault();
            editBtn = $('#editBtn').val();;
            if(editBtn == 'Edit'){
                var myid = $('#myid').val();
                $.ajax({
                    type: "PUT",
                    url: "/branch_update/"+myid,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: $('#branchForm').serialize(),
                    success: function(data){
                        if($.isEmptyObject(data.error)){
                            $('#branchModal .close').click();
                            table.draw();
                        }else{
                            alert(data.error);
                        }
                    } 
                });
            }
            if(editBtn == 'Save'){
                $.ajax({
                    type: "POST",
                    url: "branch_add",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: $('#branchForm').serialize(),
                    success: function(data){
                        if($.isEmptyObject(data.error)){
                            $('#branchModal .close').click();
                            table.draw();
                        }else{
                            alert(data.error);
                        }
                    }
                });
            }
        });

        $('#filter').popover({ 
            html: true,
            sanitize: false,
            title: 'Filter Columns &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
        });

        $('#filter').on("click", function (event) { 
            for ( var i=1 ; i<=6 ; i++ ) {
                if (table.column( i ).visible()){
                    $('#filter-'+i).prop('checked', true);
                }
                else {
                    $('#filter-'+i).prop('checked', false);
                }
            }
        });

        $('body').on('click', '.branchColumnCb', function(){ 
            var column = table.column( $(this).attr('data-column') );
            var colnum = $(this).attr('data-column');
            $('.fl-'+colnum).val('');
            table
                .columns(colnum).search( '' )
                .draw();
            column.visible( ! column.visible() );
            
        });

        $('#search-ic').on("click", function (event) { 
            for ( var i=0 ; i<=6 ; i++ ) {
                
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

        $('.mfilter-input').keyup(function() {
            stockTable.column($(this).data('column'))
                .search($(this).val())
                .draw();
        });

    });