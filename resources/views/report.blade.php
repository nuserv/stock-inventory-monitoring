@extends('layouts.app')
@section('content')
<br><br>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <nav class="nav nav-tabs navbar-expand-md">
                    <div class="navbar-collapse collapse justify-content-center align-items-center w-100">
                        <ul class="nav justify-content-center">
                            <li class="nav-item" style="color: white; font-size: 20px">
                            REPORT A PROBLEM
                            </li>
                        </ul>
                    </div>
                </nav>
                <div class="card-body" style="background-color: #f2f2f2">
                    
                    <form method="POST" action="{{ route('report.problem') }}">
                        @csrf 
                        @foreach ($errors->all() as $error)
                            <p class="text-danger">{{ $error }}</p>
                        @endforeach 
                        @if (Session::has('success'))
                            <div class="alert alert-success">
                                <ul>
                                    <li>{{ Session::get('success') }}</li>
                                </ul>
                            </div>
                        @endif
                            
                        <!--div class="form-group row">
                            <label for="fullname" class="col-md-4 col-form-label text-md-right">FULL NAME</label>
  
                            <div class="col-md-6">
                                <input id="fullname" type="text" class="form-control" name="fullname" autocomplete="off" required>
                            </div>
                        </div>
  
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">E-MAIL</label>
  
                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control" name="email" autocomplete="off" required>
                            </div>
                        </div>
  
                        <div class="form-group row">
                            <label for="branch" class="col-md-4 col-form-label text-md-right">BRANCH</label>
    
                            <div class="col-md-6">
                                <input id="branch" type="text" class="form-control" name="branch" autocomplete="off" required >
                            </div>
                        </div-->
                        <div class="form-group row">
                            <label for="module" class="col-md-4 col-form-label text-md-right">MODULE</label>
    
                            <div class="col-md-6">
                                <input id="module" type="text" style="color:black" class="form-control" name="module" autocomplete="off" required >
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="remarks" class="col-md-4 col-form-label text-md-right">REMARKS</label>
    
                            <div class="col-md-6">
                                <textarea id="remarks" class="form-control" style="color:black" name="remarks" autocomplete="off" required ></textarea>
                            </div>
                        </div>
   
                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    Submit
                                </button>
                            </div>
                        </div>
                    </form>
                    <br><br>
                    <div class="form-group row mb-0">
                        <div class="col-md-12 offset-md-2">
                        You may also contact us at our viber numbers below for chat support.<br><br>
                        <span><img src="/viber.png" alt="viber.png" style="width: auto; height: 50px;"></span><span> 09992206507 / 09988488624</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection