<?php
/**
 * Created by Grzegorz Możdżeń
 * Date: 19/01/2022
 * Time: 01:33
 */
?>
@extends('voyager::master')
@section('content')
    <h1 class="page-title">
        Dodaj kontakt dla dostawcy <strong>{{$supplier->name}}</strong>
    </h1>
    <div class="page-content container-fluid">
        <div class="panel panel-bordered">
            <div class="panel-body">
                @if (isset($messasge) && !empty($messasge))
                    <h5>{{$messasge}}</h5>
                @endif
                <form action="{{route('suppliers.contact.add', ['id' => $supplier_id])}}" method="post">
                    <?php echo e(csrf_field()); ?>
                    <input type="hidden" name="supplier_id" value="{{$supplier_id}}" />
                        <div class="form-group ">
                            <label for="deparment_id">Dział</label>
                            <select name="department_id" class="form-control">
                                @foreach ($departments as $departmentId => $departmentName)
                                    <option value="{{$departmentId}}">{{$departmentName}}</option>
                                @endforeach
                            </select>
                        </div>
                    <div class="form-group ">
                        <label for="email">Adres e-mail</label>
                        <input type="email" name="email" placeholder="Podaj adres e-mail" class="form-control" />
                    </div>
                    <div class="form-group ">
                        <label for="name">Imię i nazwisko</label>
                        <input type="text" name="name" required="required" placeholder="Podaj imię i nazwisko" class="form-control" />
                    </div>
                    <div class="form-group ">
                        <label for="phone">Telefon</label>
                        <input type="text" name="phone" placeholder="Podaj telefon" class="form-control" />
                    </div>
                    <div class="form-group ">
                        <label for="stanowisko">Stanowisko</label>
                        <input type="text" name="stanowisko" placeholder="Podaj stanowisko" class="form-control" />
                    </div>
                    <button class="btn btn-sm btn-primary"  type="submit">Dodaj kontakt</button>
                    <a href="{{url('/admin/suppliers/'.$supplier_id)}}" class="btn btn-sm btn-info">Powrót do karty dostawcy</a>
                </form>
            </div>
        </div>
    </div>

@endsection
