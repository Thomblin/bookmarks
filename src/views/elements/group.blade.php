<fieldset id="{{$group->getId()}}" class="bookmarkgroup {{{ isset($expanded) && $expanded ? '' : 'hidden' }}}">
    <legend>{{{$group->title}}}</legend>
    <div class="content">
        @foreach($group->getLinks() as $link)
            @include('elements.link')
        @endforeach
        <p class="new">
            <img src="/graphics/plus.png" alt="add link" title="add link" class="add link"/>
        </p>
    </div>
</fieldset>