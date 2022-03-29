@extends('voyager::master')
@section('content')
    <h1 class="page-title">
        Edytuj kategorie
    </h1>
    <div class="page-content container-fluid">
        <div id="categories-vue-wrapper">
            <categories
                :categories="{{json_encode($categories)}}"
                save-url="{{route('pools.categories.save', ['id' => $id])}}"
            ></categories>
            <a href="/admin/pools/" class="btn btn-sm" style="background-color: #cccccc">Zapisz - powrót do definicji</a>
        </div>

    </div>
@stop
