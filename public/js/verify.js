$(document).ready( function() { // Wait until document is fully parsed
    $("#resend").on('click', function(){
        if ($('#email').val()) {
            if (emailValidate($('#email').val())) {
                $('#loading').show();
                $.ajax({
                    url: '/send/verification',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="ctok"]').attr('content')
                    },
                    dataType: 'json',
                    type: 'get',
                    data: {
                        email: $('#email').val() 
                    },
                    success: function (data){
                        if (data == true) {
                            alert('We sent you an activation code. Check your email and click on the link to verify.')
                            window.location.href='/logout';
                        }else{
                            alert('Email address already exist!');
                            $('#loading').hide();
                        }
                    },
                    error: function (data) {
                        alert(data.responseText);
                    }
                });
            }else{
                alert('Please enter a valid email address!');
            }
        }
    });
    function emailValidate(email){
        var check = "" + email;
        if((check.search('@')>=0)&&(check.search(/\./)>=0))
            if(check.search('@')<check.split('@')[1].search(/\./)+check.search('@')) return true;
            else return false;
        else return false;
    }
    
    
});