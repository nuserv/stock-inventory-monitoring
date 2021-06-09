@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <nav class="nav nav-tabs navbar-expand-md">
                    <div class="navbar-collapse collapse justify-content-center align-items-center w-100">
                        <ul class="nav justify-content-center">
                            <li class="nav-item" style="color: white; font-size: 20px">
                                E-MAIL ADDRESS VERIFICATION
                            </li>
                        </ul>
                    </div>
                </nav>

                <div class="card-body bg-secondary">
                    <form action="/send/verification" method="GET" id="send" class="text-center">
                        {{ __('Please check your inbox to verify your e-mail address to continue.') }}<br>
                        {{ __('If you don\'t received a verification e-mail, hit RESEND button below to resend the link.' )}}<br>
                        {{ __('If the e-mail address below is incorrect input the correct email and hit RESEND.')}}<br><br>
                        <input type="text" class="text-center" style="width: 300px" id="email" value="{{auth()->user()->email}}"><br><br>
                        <input type="button" id="resend" class="btn btn-primary" value="Resend"><br>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection