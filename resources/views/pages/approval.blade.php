<html>
<head>
    <link href='https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.min.css' rel='stylesheet'>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.24/dist/sweetalert2.all.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
<input type="hidden" id="code" value="{{$code->code}}">
<input type="hidden" id="reqno" value="{{$code->request_no}}">
    <script>
    $(document).ready(function()
    {   
        Swal.fire({
            title: 'Confirm Delete Request',
            html: "Are you sure you want to approve the delete request number "+$('#reqno').val()+"?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type:'get',
                    url:'/delreqapproved',
                    async: false,
                    data:{
                        code: $('#code').val()
                    },
                    success:function(data)
                    {
                        Swal.fire({
                            title: 'Deleted!',
                            html: 'Request no. '+$('#reqno').val()+' to delete has been approved!',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.top.close();
                            }
                        });
                        setTimeout(function() {
                            window.top.close();
                        }, 20000);
                    },
                    error: function (data) {
                        if(data.status == 401) {
                            window.location.href = '/login';
                        }
                        alert(data.responseText);
                    }
                });
            }
        });
    });
    </script>
</body>
</html>