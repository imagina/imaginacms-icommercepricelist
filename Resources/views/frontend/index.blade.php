@extends('layouts.master')

@section('title')
    {{ trans('icommercepricelist::pricelists.title.pricelists') }} | @parent
@endsection

@section('content')
    <div class="container">
        <div class="row">
            @foreach($priceLists as $priceList)
                <div class="col-12 col-sm-4">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <td colspan="2" class="text-center">
                                        {{ $priceList->name }}
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($priceList->products as $product)
                                <tr>
                                    <td>
                                        <a class="text-primary" href="{{ $product->url }}">{{ $product->name }}</a>
                                    </td>
                                    <td class="text-right">
                                        {{ $product->price }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
