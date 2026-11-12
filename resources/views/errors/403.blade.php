@extends('errors.layout')

@php
    // Las policies del sistema explican el motivo en español; el mensaje de roles del paquete viene en inglés
    $reason = $exception->getMessage();
    $custom = $reason && ! \Illuminate\Support\Str::contains($reason, ['User does not have', 'This action is unauthorized', 'User is not logged in']);
@endphp

@section('code', '403')
@section('title', 'No tienes permiso')
@section('message', $custom ? $reason : 'Tu cuenta no tiene permiso para abrir esta página o hacer esta acción. Si crees que es un error, habla con el Supervisor o el Jefe de Área.')
