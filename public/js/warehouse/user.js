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
        var table = 
        $('#userTable').DataTable({ 
            "dom": 'lrtip',
            processing: true,
            serverSide: true,
            "language": {
                "emptyTable": "No registered user to this branch"
            },
            "pageLength": 15,
            ajax: 'users',
            columns: [
                { data: 'fname', name:'fname' },
                { data: 'email', name:'email' },
                { data: 'area', name:'area' },
                { data: 'branch', name:'branch' },
                { data: 'role', name:'role' },
                { data: 'status', name:'status' }
            ]
        });

        $('#userTable tbody').on('click', 'tr', function () { 
            if ($('#level').val() == 'Head' || $('#level').val() == 'Administrator') {
                return false;
            }
            var dtdata = $('#userTable tbody tr:eq(0)').data();
            var trdata = table.row(this).data();
            var area = trdata.area_id;
            var op=" ";
            $('#myid').val(trdata.id);
            $('#first_name').prop('disabled', false);
            $('#last_name').prop('disabled', false);
            $('#email').prop('disabled', false);
            $('#password').prop('disabled', true);
            $('#password_confirmation').prop('disabled', true);
            $("#divpass1").hide();
            $("#divpass2").hide();
            $('#role').prop('disabled', false);
            $('#area').prop('disabled', false);
            $('#branch').prop('disabled', false);
            $('#status').prop('disabled', false);
            $('#userModal').modal('show');
            $.ajax({
                type:'get',
                url:'getBranchName',
                data:{'id':area},
                async: false,
                success:function(data)
                {
                    var branch = $.map(data, function(value, index) {
                        return [value];
                    });
                    op+='<option selected disabled>select branch</option>';
                    branch.forEach(value => {
                        op+='<option value="'+value.id+'">'+value.branch+'</option>';
                    });
                    $('#branch').find('option').remove().end().append(op);
                    $('#branch').val(trdata.branch_id);
                },
            });
            $('#first_name').val(trdata.name);
            $('#last_name').val(trdata.lastname);
            $('#email').val(trdata.email);
            $('#area').val(area);
            $('#role').val(trdata.role);
            $('#status').val(dtdata.dataStatus);
            $('#subBtn').val('Update');

        });
        
        $('#addBtn').on('click', function(e){ 
            e.preventDefault();
            $('#subBtn').val('Save');
            
                $("#divpass1").show();
                $("#divpass2").show();
                $('#userModal').modal('show');
                $('#first_name').val('');
                $('#last_name').val('');
                $('#email').val('');
                $('#password').val('');
                $('#password_confirmation').val('');
                if ($('#myrole').val() == 2) {
                    $('#role').val('Tech');   
                }
                $('#area').val('select area');
                $('#branch').val('select branch');
                $('#status').val('1');
                $('#first_name').prop('disabled', false);
                $('#last_name').prop('disabled', false);
                $('#email').prop('disabled', false);
                $('#password').prop('disabled', false);
                $('#password_confirmation').prop('disabled', false);
                $('#role').prop('disabled', false);
                $('#area').prop('disabled', false);
                $('#branch').prop('disabled', false);
                $('#status').prop('disabled', false);
        });

        $('.area').change(function(){ 
            var area = $(this).val();
            var op=" ";
            
            $.ajax({
                type:'get',
                url:'getBranchName',
                data:{'id':area},
                success:function(data)
                {
                    var branch = $.map(data, function(value, index) {
                        return [value];
                    });
                    op+='<option selected disabled>select branch</option>';
                    branch.forEach(value => {
                        op+='<option value="'+value.id+'">'+value.branch+'</option>';
                    });
                    $('#branch').find('option').remove().end().append(op);
                    if (data.length == 1) {
                        $('#branch').val('1');
                    }else{
                        $('#branch').val('select branch');
                    }
                },
            });
        });

        $('#userForm').on('submit', function(e){ 
            e.preventDefault();
            subBtn = $('#subBtn').val();
            if(subBtn == 'Update'){
                var myid = $('#myid').val();
                $.ajax({
                    type: "PUT",
                    url: "/user_update/"+myid,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: $('#userForm').serialize(),
                    success: function(data){
                        if($.isEmptyObject(data.error)){
                            $('#userModal').modal('hide');
                            alert("User data updated");
                            window.location.reload();
                        }else{
                            alert(data.error);
                        }
                    } 
                });
            }
            if(subBtn == 'Save'){
                $.ajax({
                    type: "POST",
                    url: "user_add",
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: $('#userForm').serialize(),
                    success: function(data){
                        if($.isEmptyObject(data.error)){
                            $('#userModal').modal('hide');
                            alert("User data saved");
                            window.location.reload();
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
            title: 'Filter Columns &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',
        });

        $('#filter').on("click", function (event) { 
            for ( var i=1 ; i<=5 ; i++ ) {
                if (table.column( i ).visible()){
                    $('#filter-'+i).prop('checked', true);
                }
                else {
                    $('#filter-'+i).prop('checked', false);
                }
            }
        });

        $('body').on('click', '.userColumnCb', function(){ 
            var column = table.column( $(this).attr('data-column') );
            var colnum = $(this).attr('data-column');
            column.visible( ! column.visible() );
            $('.fl-'+colnum).val('');
            table
                .columns(colnum).search( '' )
                .draw();
        });

        $('#search-ic').on("click", function (event) { 
            for ( var i=0 ; i<=5 ; i++ ) {
                
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

    });