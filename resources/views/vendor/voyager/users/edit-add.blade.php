@extends('voyager::master')

@section('page_title', __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular'))

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_header')
    <h1 class="page-title page-title-users">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.'.(isset($dataTypeContent->id) ? 'edit' : 'add')).' '.$dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
@stop

@section('content')
    <div class="page-content container-fluid page-users-wrapper">
        <form class="form-edit-add" role="form"
              action="@if(!is_null($dataTypeContent->getKey())){{ route('voyager.'.$dataType->slug.'.update', $dataTypeContent->getKey()) }}@else{{ route('voyager.'.$dataType->slug.'.store') }}@endif"
              method="POST" enctype="multipart/form-data" autocomplete="off">
            <!-- PUT Method if we are editing -->
            @if(isset($dataTypeContent->id))
                {{ method_field("PUT") }}
            @endif
            {{ csrf_field() }}

            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-bordered">
                        {{-- <div class="panel"> --}}
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="panel-body">

                            <div class="form-group col-md-6">
                                <label for="imie">Imię</label>
                                <input type="text" class="form-control" id="imie" name="imie" placeholder="Imię"
                                       value="{{ old('imie', $dataTypeContent->imie ?? '') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="nazwisko">Nazwisko</label>
                                <input type="text" class="form-control" id="nazwisko" name="nazwisko"
                                       placeholder="Nazwisko"
                                       value="{{ old('nazwisko', $dataTypeContent->nazwisko ?? '') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="miasto">Miasto</label>
                                <input type="text" class="form-control" id="miasto" name="miasto" placeholder="Miasto"
                                       value="{{ old('miasto', $dataTypeContent->miasto ?? '') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="telefon">Telefon</label>
                                <input type="text" class="form-control" id="telefon" name="telefon"
                                       placeholder="Telefon"
                                       value="{{ old('telefon', $dataTypeContent->telefon ?? '') }}">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="name">{{ __('voyager::generic.name') }}</label>
                                <input type="text" class="form-control" id="name" name="name"
                                       placeholder="{{ __('voyager::generic.name') }}"
                                       value="{{ old('name', $dataTypeContent->name ?? '') }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="email">{{ __('voyager::generic.email') }}</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="{{ __('voyager::generic.email') }}"
                                       value="{{ old('email', $dataTypeContent->email ?? '') }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="password">{{ __('voyager::generic.password') }}</label>
                                @if(isset($dataTypeContent->password))
                                    <br>
                                    <small>{{ __('voyager::profile.password_hint') }}</small>
                                @endif
                                <input type="password" class="form-control" id="password" name="password" value=""
                                       autocomplete="new-password">
                            </div>

                            @can('editRoles', $dataTypeContent)
                                <div class="form-group col-md-6">
                                    <label for="default_role">Rola</label>
                                    @php
                                        $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};

                                        $row     = $dataTypeRows->where('field', 'user_belongsto_role_relationship')->first();
                                        $options = $row->details;

                                    @endphp
                                    @include('voyager::formfields.relationship')
                                </div>





                            @endcan
                            <div class="form-group col-md-6">
                                <label for="dzial">Dział</label>
                                @php
                                    $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};

                                    $row     = $dataTypeRows->where('field', 'user_belongstomany_department_relationship')->first();
                                    $options = $row->details;
                                @endphp
                                @include('voyager::formfields.relationship')
                            </div>
                            <div class="form-group col-md-6">
                                <label for="dzial">Laboratorium</label>
                                @php
                                    $dataTypeRows = $dataType->{(isset($dataTypeContent->id) ? 'editRows' : 'addRows' )};

                                    $row     = $dataTypeRows->where('field', 'user_belongstomany_laboratory_relationship')->first();
                                    $options = $row->details;
                                @endphp
                                @include('voyager::formfields.relationship')
                            </div>
                            @php
                                if (isset($dataTypeContent->locale)) {
                                    $selected_locale = $dataTypeContent->locale;
                                } else {
                                    $selected_locale = config('app.locale', 'en');
                                }

                            @endphp
                            <div class="form-group col-md-6" style="display: none;">
                                <label for="locale">{{ __('voyager::generic.locale') }}</label>
                                <select class="form-control select2" id="locale" name="locale">
                                    @foreach (Voyager::getLocales() as $locale)
                                        <option value="{{ $locale }}"
                                            {{ ($locale == $selected_locale ? 'selected' : '') }}>{{ $locale }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6" style="display: none;">
                                <label for="additional_roles">Role dodatkowe</label>
                                @php
                                    $row     = $dataTypeRows->where('field', 'user_belongstomany_role_relationship')->first();
                                    $options = $row->details;
                                @endphp
                                @include('voyager::formfields.relationship')
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary  save">
                        {{ __('voyager::generic.save') }}
                    </button>
                    <a href="javascript:history.back();" class="btn btn-sm btn-secondary ">Powrót</a>
                </div>


            </div>


        </form>

        <iframe id="form_target" name="form_target" style="display:none"></iframe>
        <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
              enctype="multipart/form-data" style="width:0px;height:0;overflow:hidden">
            {{ csrf_field() }}
            <input name="image" id="upload_file" type="file" onchange="$('#my_form').submit();this.value='';">
            <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
        </form>
    </div>
@stop

@section('javascript')
    <script>
        $('document').ready(function () {
            $('.toggleswitch').bootstrapToggle();
        });
    </script>
@stop
