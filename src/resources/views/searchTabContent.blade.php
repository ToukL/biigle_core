@if($type === 'label-trees')
<h2 class="lead">{{number_format($labelTreeResultCount)}} label tree results</h2>
<ul class="search-results">
    @foreach ($results as $tree)
        <li>
            <small class="pull-right text-muted">Updated on {{$tree->updated_at->toFormattedDateString()}}</small>
            <span class="search-results__name">
                @if ($tree->visibility_id === Biigle\Visibility::privateId())
                    <span class="text-muted fa fa-lock" title="This label tree is private"></span>
                @endif
                <a href="{{route('label-trees', $tree->id)}}">{{$tree->name}}</a>
            </span><br>
            {{$tree->description}}
        </li>
    @endforeach

    @if ($results->isEmpty())
        <p class="well well-lg text-center">
            We couldn't find any label trees
            @if ($query)
                matching '{{$query}}'.
            @else
                for you. Why don't you <a href="{{route('label-trees-create')}}" title="Create a new label tree">create</a> one?
            @endif
        </p>
    @endif
</ul>
@endif
