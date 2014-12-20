@extends('layouts.default')
@section('content')

    <div>
        @include('elements.menu')
        @include('elements.search')
        <div style="clear:both"/>
    </div>

    <div id="groups" class="hideCollapsed">
        @foreach($storage->getConfig()->getGroups() as $group)
            @include('elements.group')
        @endforeach
    </div>

    @include('elements.modals')
@stop
