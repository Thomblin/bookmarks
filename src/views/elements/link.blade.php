<p id="{!! $link->getId() !!}" class="bookmark">
    <a href="{{{$link->url}}}" target=\"_blank\">{{{$link->title}}}</a>
    @foreach($link->tags as $tag)
        <span>{{{$tag}}}</span>
    @endforeach
    <img src="?file=graphics/edit.png" alt="add tag" title="add tag" class="edit link"/>
</p>