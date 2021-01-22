@extends('layouts.master')

@section('title')
    {{ trans('icommercepricelist::pricelists.title.pricelists') }} | @parent
@endsection

@section('content')
    <div class="container">
        <div class="card-columns">
            @foreach($categories as $category)
                @if(count($category->ownProducts->where('status',1)->where('quantity','>',0)) > 0)
                <div class="card border-0">
                    <div class="card-body p-1">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <td colspan="2" class="text-center bg-primary text-white">
                                            {{ $category->title }}
                                        </td>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($category->ownProducts->where('status',1)->where('quantity','>',0) as $product)
                                    <tr>
                                        <td>
                                            <a class="text-primary" href="{{ $product->url }}">{{ $product->name }}</a>
                                        </td>
                                        <td class="text-right">
                                            {{ formatMoney($product->price) }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
@endsection
