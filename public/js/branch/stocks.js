    var y = 1;
    var r = 1;
    var b =1;
    var table;
    var branchid;
    var stock;
    var sub = 0;
    $(document).ready(function()
    {
        branchid = $('#branchid').attr('branchid');
        table =
        $('table.stockTable').DataTable({ 
            "dom": 'lrtip',
            "language": {
                "emptyTable": " "
            },
            "pageLength": 20,
            "order": [[ 2, "asc" ]],
            processing: true,
            serverSide: true,
            ajax: 'viewStock',
            columns: [
                { data: 'category', name:'category'},
                { data: 'description', name:'description'},
                { data: 'quantity', name:'quantity'}
            ]
        });

        $('#search-ic').on("click", function () { 
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
    });
    
    $(document).on('click', '#addStockBtn', function(){
        $('#addModal').modal({backdrop: 'static', keyboard: false});

    });

    $(document).on('click', '#importBtn', function(){
        $('#importModal').modal({backdrop: 'static', keyboard: false});
    });

    $(document).on('click', '#in_Btn', function(){
        if ($('#check').val() == '[]') {
            $('.defective').prop('disabled', true)
            $('.good').prop('disabled', true)
        }else{
            $('.defective').prop('disabled', false)
            $('.good').prop('disabled', false)
        }
        $('#inOptionModal').modal({backdrop: 'static', keyboard: false});
    });

    $(document).on('click', '#out_Btn', function(){
        $('#outOptionModal').modal({backdrop: 'static', keyboard: false});
    });

    $(document).on('click', '.add_item', function(){
        var rowcount = $(this).attr('btn_id');
        if ($(this).val() == 'Add Item') {
            if($('#category'+ rowcount).val() && $('#item'+ rowcount).val() && $('#desc'+ rowcount).val() && $('#serial'+ rowcount).val()) {
                y++;
                var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-2 form-group"><select id="item'+y+'" class="form-control item" row_count="'+y+'"><option selected disabled>select item code</option></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" class="form-control desc" row_count="'+y+'"><option selected disabled>select description</option></select></div><div class="col-md-2 form-group"><input type="text" id="serial'+y+'" class="form-control serial" row_count="'+y+'" value="N/A"></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
                $(this).val('Remove');
                $('#category'+ rowcount).prop('disabled', true);
                $('#item'+ rowcount).prop('disabled', true);
                $('#desc'+ rowcount).prop('disabled', true);
                $('#serial'+ rowcount).prop('disabled', true);
                if (r < 20 ) {
                    $('#reqfield').append(additem);
                    $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
                    r++;
                }
            }
        }else{
            if (r == 20) {
                y++;
                var additem = '<div class="row no-margin" id="row'+y+'"><div class="col-md-2 form-group"><select id="category'+y+'" class="form-control category" row_count="'+y+'"></select></div><div class="col-md-2 form-group"><select id="item'+y+'" class="form-control item" row_count="'+y+'"><option selected disabled>select item code</option></select></div><div class="col-md-3 form-group"><select id="desc'+y+'" class="form-control desc" row_count="'+y+'"><option selected disabled>select description</option></select></div><div class="col-md-2 form-group"><input type="text" id="serial'+y+'" class="form-control serial" row_count="'+y+'" value="N/A"></div><div class="col-md-1 form-group"><input type="button" class="add_item btn btn-xs btn-primary" btn_id="'+y+'" value="Add Item"></div></div>';
                $('#reqfield').append(additem);
                $('#category'+ rowcount).find('option').clone().appendTo('#category'+y);
                r++;
            }
            $('#category'+rowcount).val('select category');
            $('#item'+rowcount).val('select item code');
            $('#desc'+rowcount).val('select description');
            $('#serial'+rowcount).val('select serial');
            $('#category'+rowcount).prop('disabled', false);
            $('#item'+rowcount).prop('disabled', false);
            $('#desc'+rowcount).prop('disabled', false);
            $('#serial'+rowcount).prop('disabled', false);
            $('#row'+rowcount).hide();
            $(this).val('Add Item');
            r--;
        }
    });

    $(document).on('click', '#addCatBtn', function(){
        $("#addModal .close").click();
        $('#categoryModal').modal({backdrop: 'static', keyboard: false});
    });

    $(document).on('click', '#addCodeBtn', function(){
        $("#addModal .close").click();
        $('#itemModal').modal({backdrop: 'static', keyboard: false});
    });

    $(document).on('click', '.add_cat', function(){
        var rowcount = $(this).attr('btn_id');
        if ($(this).val() == 'Add') {
            if($('#cat'+ rowcount).val()){
                y++;
                var additem = '<div class="row no-margin" id="catrow'+y+'"><div class="col-md-8 form-group"><input type="text" id="cat'+y+'" class="form-control serial" row_count="'+y+'" placeholder="Category"></div><div class="col-md-1 form-group"><input type="button" class="add_cat btn btn-xs btn-primary" btn_id="'+y+'" value="Add"></div></div>'
                $(this).val('Remove');
                $('#cat'+ rowcount).prop('disabled', true);
            }
            if (c < 10 ) {
                $('#catfield').append(additem);
                $('#cat'+ rowcount).find('option').clone().appendTo('#cat'+y);
                c++;
            }
        }else{
            if (c == 10) {
                y++;
                var additem = '<div class="row no-margin" id="catrow'+y+'"><div class="col-md-8 form-group"><input type="text" id="cat'+y+'" class="form-control serial" row_count="'+y+'" placeholder="Category"></div><div class="col-md-1 form-group"><input type="button" class="add_cat btn btn-xs btn-primary" btn_id="'+y+'" value="Add"></div></div>'
                $('#catfield').append(additem);
                $('#cat'+ rowcount).find('option').clone().appendTo('#cat'+y);
                c++;
            }
            $('#cat'+rowcount).val('');
            $('#catrow'+rowcount).hide();
            $(this).val('Add');
            c--;
        }
    });

    $(document).on('click', '.add_item-desc', function(){
        var rowcount = $(this).attr('btn_id');
        if ($(this).val() == 'Add') {
            if($('#item-desc'+ rowcount).val() && $('#itemcat'+ rowcount).val()){
                y++;
                var additem = '<div class="row no-margin" id="itemrow'+y+'"><div class="col-md-4 form-group"><select id="itemcat'+y+'" class="form-control item-category" row_count="'+y+'"></select></div><div class="col-md-4"><input type="text" id="item-desc'+y+'" class="form-control" row_count="'+y+'" placeholder="Item Description"></div><div class="col-md-1 form-group"><input type="button" class="add_item-desc btn btn-xs btn-primary" btn_id="'+y+'" value="Add"></div></div>'
                $(this).val('Remove');
                $('#item-desc'+ rowcount).prop('disabled', true);
                $('#itemcat'+ rowcount).prop('disabled', true);
            }
            if (b < 10 ) {
                $('#itemfield').append(additem);
                $('#itemcat'+ rowcount).find('option').clone().appendTo('#itemcat'+y);
                b++;
            }
        }else{
            if (b == 10) {
                y++;
                var additem = '<div class="row no-margin" id="itemrow'+y+'"><div class="col-md-4 form-group"><select id="itemcat'+y+'" class="form-control item-category" row_count="'+y+'"></select></div><div class="col-md-4"><input type="text" id="item-desc'+y+'" class="form-control" row_count="'+y+'" placeholder="Item Description"></div><div class="col-md-1 form-group"><input type="button" class="add_item-desc btn btn-xs btn-primary" btn_id="'+y+'" value="Add"></div></div>'
                $('#itemfield').append(additem);
                $('#itemcat'+ rowcount).find('option').clone().appendTo('#itemcat'+y);
                b++;
            }
            $('#itemcat'+rowcount).val('');
            $('#itemrow'+rowcount).hide();
            $(this).val('Add');
            b--;
        }
    });

    $(document).on('click', '#sub_cat_Btn', function(){
        if (sub > 0) {
            return false;
        }
        var cat = "";
        var check = 1;
        for(var q=1;q<=y;q++){
            if ($('#catrow'+q).is(":visible")) {
                if ($('.add_cat[btn_id=\''+q+'\']').val() == 'Remove') {
                    check++;
                    sub++;
                    $('#sub_cat_Btn').prop('disabled', true)
                    cat = $('#cat'+q).val();
                    $.ajax({
                        url: 'addcategory',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        type: 'POST',
                        data: {
                            cat : cat
                        },
                    });
                }
            }
        }
        if (check > 1) {
            window.location.href = 'stocks';
        }
    });

    $(document).on('click', '#sub_item_Btn', function(){
        if (sub > 0) {
            return false;
        }
        var cat = "";
        var check = 1;
        for(var q=1;q<=y;q++){
            if ($('#itemrow'+q).is(":visible")) {
                if ($('.add_item-desc[btn_id=\''+q+'\']').val() == 'Remove') {
                    check++;
                    sub++;
                    $('#sub_item_Btn').prop('disabled', true);
                    cat = $('#itemcat'+q).val();
                    item = $('#item-desc'+q).val();
                    $.ajax({
                        url: 'additem',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        dataType: 'json',
                        type: 'POST',
                        data: {
                            cat : cat,
                            item : item
                        },
                    });
                }
            }
        }
        if (check > 1) {
            window.location.href = 'stocks';
        }
    });

    $(document).on('click', '.cancel', function(){
        window.location.href = 'stocks';
    });

    $(document).on('keyup', '#client', function(){
        var id = $(this).val();
        var op = " ";
        $('#customer').val('');
        $("#customer-name").find('option').remove();
        $.ajax({
            type:'get',
            url:'client-autocomplete',
            data:{
                'id':id
            },
            success:function(data)
            {
                op+=' ';
                for(var i=0;i<data.length;i++){
                    op+='<option data-value="'+data[i].id+'" value="'+data[i].customer.toUpperCase()+'"></option>';
                }
                $("#client-name").find('option').remove().end().append(op);
                
                $('#client-id').val($('#client-name [value="'+$('#client').val()+'"]').data('value'));
            },
        });
    });

    $(document).on('keyup', '#customer', function(){
        var id = $(this).val();
        var op = " ";
        if ($('#client-id').val()) {
            var client = $('#client-id').val();
        }else{
            alert("Incomplete Client Name!");
            return false;
        }
        $.ajax({
            type:'get',
            url:'customer-autocomplete',
            data:{
                'id':id,
                'client':client
            },
            success:function(data)
            {
                op+=' ';
                for(var i=0;i<data.length;i++){
                    op+='<option data-value="'+data[i].id+'" value="'+data[i].customer_branch.toUpperCase()+'"></option>';
                }
                $("#customer-name").find('option').remove().end().append(op);
                $('#customer-id').val($('#customer-name [value="'+$('#customer').val()+'"]').data('value'));
            },
        });
    });


    $(document).on("click", "#stockTable tr", function () {
        var trdata = table.row(this).data();
        var id = trdata.items_id;
        $('table.stockDetails').dataTable().fnDestroy();
        $('#head').text(trdata.category);
        stock = 
        $('table.stockDetails').DataTable({ 
            "dom": 'rt',
            "language": {
                "emptyTable": "No Stock Available for this Item"
            },
            processing: true,
            serverSide: true,
            ajax: "/bserial/"+id,
            columns: [
                { data: 'updated_at', name:'updated_at'},
                { data: 'item', name:'item'},
                { data: 'serial', name:'serial'}
            ]
        });
        $('#stockModal').modal();
    });