<div class="d-flex">
    <img class="p-2 align-self-end" src="/idsi.png" alt="idsi.png" style="width: auto; height: 90px;">
    <h3 class="p-2 align-self-end" style="color: #0d1a80; font-family: arial; font-weight: bold;">SERVICE CENTER STOCK MONITORING SYSTEM</h3>
    @auth
    <div class="p-2 ml-auto align-self-end d-flex" id="branchid" branchid="{{ auth()->user()->branch->id}}">
        <a href="{{route('change.password')}}">
            <div class="p-2 ml-auto" style="text-align: right;">
                    <p style="color: #0d1a80">{{ auth()->user()->name}} {{ auth()->user()->lastname}}</p>
                    <p style="color: #0d1a80">{{ auth()->user()->branch->branch}}</p>
                    <p style="color: #0d1a80">{{Carbon\Carbon::now()->toDayDateTimeString()}}</p>
                    <input type="text" id="userlog" value="{{ auth()->user()->name}} {{ auth()->user()->lastname}}" hidden>   
                    <input type="text" id="userid" value="{{ auth()->user()->id}}" hidden>   
                    <input type="text" id="userlevel" value="{{ auth()->user()->roles->first()->name}}" hidden>   
            </div>
        </a>
        <i class="fa fa-user-circle fa-4x p-2"></i>
    </div>
    @endauth
</div>