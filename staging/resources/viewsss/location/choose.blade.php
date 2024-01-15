@extends('login')

@section('content')
    <!-- Content Wrapper -->
    <div class="login-wrapper">
        <div class="back-link">
            <a href="{{ route('logout') }}" class="btn btn-add">Logout</a>
        </div>
        <div class="container-center">
            <div class="login-area">
                <div class="panel panel-bd panel-custom">
                    <div class="panel-heading">
                        <div class="view-header">
                            <div class="header-icon">
                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                            </div>
                            <div class="header-title">
                                <h3>Choose Location</h3>
                                <small><strong>Please choose location.</strong></small>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        @foreach($locations as $value)
                            <a  href="{{ URL::to('/locations/saveLocation/'.$value->id) }}" >
                                <button type="button" class="btn btn-default w-md m-b-5">{{$value->name}}</button>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
