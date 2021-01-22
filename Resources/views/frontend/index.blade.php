@extends('iprofile::frontend.layouts.master')
@section('profileBreadcrumb')
    <x-isite::breadcrumb>
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}">{{ trans('core::core.breadcrumb.home') }}</a>
        </li>
        <li class="breadcrumb-item active" aria-current="page">{{ trans('icommercepricelist::pricelists.title.pricelists') }}</li>
    </x-isite::breadcrumb>
@endsection

@section('profileTitle')
    {{ trans('icommercepricelist::pricelists.title.pricelists') }}
@endsection
@section('profileContent')
    <div class="container">
        <div class="card-columns">
            @foreach($categories as $category)
                @if(count($category->ownProducts) > 0)
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
                                @foreach($category->ownProducts as $product)
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
@stop

@section('profileExtraFooter')
    @include('icommerce::frontend.partials.extra-footer')
@endsection
