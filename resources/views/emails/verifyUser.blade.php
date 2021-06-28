<!DOCTYPE html>
<html>
  <head>
    <title>Welcome Email</title>
  </head>
  <body>
    <p>Hi {{auth()->user()->name}},</p>
    <p><img src="{{asset('verifyemail.jpg')}}" width="659" height="119"></p>
    <p>Welcome to the IDSI Service Center Stock Monitoring System! <br>
      To start using the system, click on the verification link below.<br>
      <br>
      <b><a href="{{url('user/verify', $user->verifyUser->token)}}" target="_blank">{{url('user/verify', $user->verifyUser->token)}}</a></b></p>
    <br/>
    <p>If you have any questions kindly email us at
      <a href="mailto:bsms.support@ideaserv.com.ph" target="_blank">bsms.support@ideaserv.com.ph</a></p>
    <p>Thank you.<br>
    </p>
  </body>
</html>