@extends('admin.base')
@inject('modules', 'Biigle\Services\Modules')

@section('title', 'Admin area')

@section('admin-content')
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="pull-right">
                        <span title="Users who have been logged in once">{{ number_format($loginUsers) }}</span> <span title="All users" class="text-muted">/ {{number_format($allUsers)}}</span>
                    </span>
                    <a href="{{route('admin-users')}}" title="Users">Users</a>
                </h3>
            </div>
            <div class="panel-body">
                <p class="h1 text-center">
                    <span title="Active users in the last 24 hours">{{number_format($activeUsersLastDay)}}</span> <span class="text-muted">/</span> <span title="Active users in the last week">{{number_format($activeUsersLastWeek)}}</span> <span class="text-muted">/</span> <span title="Active users in the last month">{{number_format($activeUsersLastMonth)}}</span>
                </p>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <span class="pull-right">
                        <span title="Version of the BIIGLE core">{{ config('biigle.version') }}</span>
                    </span>
                    <span title="Version of BIIGLE core and the installed modules">Version</span>
                </h3>
            </div>
            @unless (empty($installedModules))
                <ul class="list-group dashboard-version-list">
                    @foreach ($installedModules as $module)
                        <li class="list-group-item">{{$module['name']}}: {{$module['version']}}</li>
                    @endforeach
                </ul>
            @else
                <div class="panel-body">
                    <p class="h1 text-center text-muted">No modules installed</p>
                </div>
            @endunless
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <a href="{{route('projects-index')}}" title="Projects"><h3 class="panel-title">Projects</h3></a>
            </div>
            <div class="panel-body">
                <p class="h1 text-center">{{ number_format(Biigle\Project::count()) }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <a href="{{route('search', ['t' => 'volumes'])}}" title="Volumes"><h3 class="panel-title">Volumes</h3></a>
            </div>
            <div class="panel-body">
                <p class="h1 text-center">{{ number_format(Biigle\Volume::count()) }}</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <a href="{{route('search', ['t' => 'images'])}}" title="Images"><h3 class="panel-title">Images</h3></a>
            </div>
            <div class="panel-body">
                <p class="h1 text-center">{{ number_format(Biigle\Image::count()) }}</p>
            </div>
        </div>
    </div>
<?php
    $height = 50;
    $width = 40;
?>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Image Annotations
                    <span class="pull-right">{{ $totalAnnotations }}</span>
                </h3>
            </div>
            <div class="panel-body">
                <svg style="display:block; margin:auto;" class="chart" width="300" height="{{ $height + 20 }}">
                    <line stroke="#ccc" x1="0" y1="{{$height}}" x2="300" y2="{{$height}}" />
                    @foreach($annotationWeek as $index => $day)
                        <?php $h = round($height * $day['percent']); ?>
                        <g transform="translate({{ $index * $width }}, 0)">
                            <rect fill="#ccc" y="{{$height - $h}}" width="{{ $width / 2 }}" height="{{ $h }}"><title>{{ $day['count'] }}</title></rect>
                            <text fill="{{$day['count'] ? '#ccc' : '#888'}}" x="0" y="{{ $height + 15 }}" dy=".35em">{{ $day['day']->format('D') }}</text>
                        </g>
                    @endforeach
                </svg>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    <a href="{{route('search', ['t' => 'videos'])}}" title="Videos">Videos</a>
                    <span class="pull-right">{{number_format(Biigle\Video::count())}}</span>
                </h3>
            </div>
            <div class="panel-body">
                <p class="h1 text-center">{{number_format(round(Biigle\Video::sum('duration') / 3600))}}&nbsp;h</p>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">
                    Video Annotations
                    <span class="pull-right">{{ $totalVideoAnnotations }}</span>
                </h3>
            </div>
            <div class="panel-body">
                <svg style="display:block; margin:auto;" class="chart" width="300" height="{{ $height + 20 }}">
                    <line stroke="#ccc" x1="0" y1="{{$height}}" x2="300" y2="{{$height}}" />
                    @foreach($videoAnnotationWeek as $index => $day)
                        <?php $h = round($height * $day['percent']); ?>
                        <g transform="translate({{ $index * $width }}, 0)">
                            <rect fill="#ccc" y="{{$height - $h}}" width="{{ $width / 2 }}" height="{{ $h }}"><title>{{ $day['count'] }}</title></rect>
                            <text fill="{{$day['count'] ? '#ccc' : '#888'}}" x="0" y="{{ $height + 15 }}" dy=".35em">{{ $day['day']->format('D') }}</text>
                        </g>
                    @endforeach
                </svg>
            </div>
        </div>
    </div>
    @foreach ($modules->getMixins('adminIndex') as $module => $nestedMixins)
        @include($module.'::adminIndex')
    @endforeach
@endsection
