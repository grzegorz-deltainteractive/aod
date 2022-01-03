@extends('voyager::master')
@section('content')
    <h1 class="page-title">
        Edytuj kategorie
    </h1>
    <div class="page-content container-fluid">
        <div id="categories-vue-wrapper">
            <categories
                :categories="{{json_encode($categories)}}"
            ></categories>
        </div>

    </div>
@stop
