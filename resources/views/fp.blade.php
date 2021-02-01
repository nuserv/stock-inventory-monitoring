<!doctype html>
    <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
        
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
            <link rel="stylesheet" type="text/css" href="{{ url('/css/style.css') }}" />
            <link rel="stylesheet" type="text/css" href="{{ url('/css/styles.css') }}" />
            <link rel="stylesheet" href="//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
            <link href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" rel="Stylesheet" type="text/css" />
            <!--link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.23/sl-1.3.1/datatables.min.css"/-->
            <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.23/b-1.6.5/b-print-1.6.5/sl-1.3.1/datatables.min.css"/>
            <title>{{$title}}</title>
            <link rel="icon" href="{{asset('favicon.ico')}}" type="image/x-icon" />
            <link rel="shortcut icon" href="{{asset('favicon.ico')}}" type="image/x-icon" />
            <style>
                #loading {
                    display: none;
                    position: absolute;
                    top: 0;
                    left: 0;
                    z-index: 100;
                    width: 100vw;
                    height: 100vh;
                    background-color: rgba(192, 192, 192, 0.5);
                    background-image: url("{{asset('loading.gif')}}");
                    background-repeat: no-repeat;
                    background-position: center;
                    }
            </style>
        </head>
    
        <body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">   
                <div class="card-body" style="background-color: #cc0066">
                    <form id="gen" style="background-color: #f0f0f0">
                        <br>
                        <div class="form-group row">
                            <label for="series" class="col-md-4 col-form-label text-md-right">Series:</label>
  
                            <div class="col-md-6">
                                <input id="series" type="text" class="form-control" name="series" autocomplete="off">
                            </div>
                        </div>
  
                        <div class="form-group row">
                            <label for="number" class="col-md-4 col-form-label text-md-right">Number:</label>
  
                            <div class="col-md-6">
                                <input id="number" type="text" class="form-control" name="number" autocomplete="off" required>
                            </div>
                        </div>
  
                        <div class="form-group row">
                            <label for="length" class="col-md-4 col-form-label text-md-right">Length of random characters:</label>
    
                            <div class="col-md-6">
                                <input id="length" type="text" class="form-control" name="length" autocomplete="off">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="voucher" class="col-md-4 col-form-label text-md-right">No. of Voucher:</label>
    
                            <div class="col-md-6">
                                <input id="voucher" type="text" class="form-control" name="voucher" autocomplete="current-password" required>
                            </div>
                        </div>
   
                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="btn" class="btn btn-primary" id="generate">Generate</button>
                            </div>
                        </div>
                        <br>
                    </form>
                <br><hr><br>
                    <form id="check" style="background-color: #f0f0f0">
                        <br>
                        <div class="form-group row">
                            <div class="col-md-1"></div>
                            <div class="col-md-4">
                                <textarea id="generated" rows="15" class="form-control" name="generated" readonly></textarea>
                            </div>
                            <div class="col-md-3" style="text-align: center">
                                <input type="btn" class="btn btn-primary" id="check" value="Check">
                            </div>
                            <div class="col-md-4">
                                <textarea id="checked" rows="15" class="form-control" name="checked" readonly></textarea>
                            </div>
                        </div>
                        <br>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.js"></script>
            <script src="https://cdn.datatables.net/1.10.23/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
            <!--script src="https://cdn.datatables.net/select/1.3.1/js/dataTables.select.min.js"></script-->
            <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.23/b-1.6.5/b-print-1.6.5/sl-1.3.1/datatables.min.js"></script>
            <script type="text/javascript" src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
            <script type="text/javascript" src="{{asset('js/moment.min.js')}}"></script>
<script>

$(document).on('click', '#generate', function(e){
    e.preventDefault();
    $('#generated').val('');
    $.ajax({
        type:'get',
        url:'fpgenerate',
        dataType: 'json',
        data:{
            series: $('#series').val(),
            number: $('#number').val(),
            length: $('#length').val(),
            voucher: $('#voucher').val()
        },
        success:function(data)
        {
            for(var i=0;i<data.length;i++){
                if($('#generated').val() == '') {
                    $('#generated').val(data[i]);
                }else{
                    $('#generated').val($('#generated').val()+'\n'+data[i]);
                }
            }
            console.log($('#generated').val());
        },
    });
});

$(document).on('click', '#checked', function(e){
    e.preventDefault();
    $('#generated').val('');
    var voucher = $('#generated').val().split("\n");
    var total = voucher.length;
    voucher.forEach(function(value, index){
        setTimeout(
            function(){
                var sec
            }
        )
    });


    $.ajax({
        type:'get',
        url:'fpgenerate',
        dataType: 'json',
        data:{
            series: $('#series').val(),
            number: $('#number').val(),
            length: $('#length').val(),
            voucher: $('#voucher').val()
        },
        success:function(data)
        {
            for(var i=0;i<data.length;i++){
                if($('#generated').val() == '') {
                    $('#generated').val(data[i]);
                }else{
                    $('#generated').val($('#generated').val()+'\n'+data[i]);
                }
            }
            console.log($('#generated').val());
        },
    });
});
</script>
</html>