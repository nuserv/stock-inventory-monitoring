@extends('errors::minimal')

@section('title', __('Page Expired'))
@section('code', '419')
@section('message', __('Page Expired'))
@section('script')
<script>
    function refreshPage() { 
        window.location.replace("/");
    }  
    refreshPage();
</script>
@endsection
