@extends('layouts.default')
@section('content')

    <div>
        @include('elements.menu')
        @include('elements.search')
        <div style="clear:both"/>
    </div>

    <div id="groups" class="hideCollapsed">
        {{ $groupHtml }}
    </div>

@include('elements.modals')
@stop
