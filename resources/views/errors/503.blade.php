@extends('errors::minimal')

@section('title', __('Page Not Found'))
@section('code', 'Page Not Found')
@section('message', __('We are currently under maintenance. Please check back later.'))

<!-- @section('message', __($exception->getMessage() ?: 'The page you are trying to access has been moved. Please click this <a href="https://stockmonitoring.apsoft.com.ph">link</a> to proceed.')) -->
