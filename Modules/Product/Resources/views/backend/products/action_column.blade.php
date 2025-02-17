<div class="d-flex gap-2 align-items-center">
    <button type='button'
        data-custom-module='{{ json_encode(['product_id' => $data->id, 'brand_id' => $data->brand_id, 'category_id' => $data->categories->pluck('id')->toArray()]) }}'
        data-custom-target='#form-offcanvas-stock' data-custom-event='custom_form'
        class='btn btn-soft-primary btn-sm rounded text-nowrap' data-bs-toggle="tooltip"
        title="{{ __('product.add_stock') }}"><i class="fa-solid fa-plus"></i> {{ __('product.stock') }}</button>
    <button type='button' data-gallery-module="{{ $data->id }}" data-gallery-target='#product-gallery-form'
        data-gallery-event='product_gallery' class='btn btn-soft-info btn-sm rounded text-nowrap'
        data-bs-toggle="tooltip" title="{{ __('messages.gallery_for_product') }}"><i
            class="fa-solid fa-images"></i></button>
    <button type="button" class="btn btn-soft-primary btn-sm" data-crud-id="{{ $data->id }}"
        title="{{ __('messages.edit') }} " data-bs-toggle="tooltip"> <i class="fa-solid fa-pen-clip"></i></button>
    <a href="{{ route('backend.products.destroy', $data->id) }}" id="delete-{{ $module_name }}-{{ $data->id }}"
        class="btn btn-soft-danger btn-sm" data-type="ajax" data-method="DELETE" data-token="{{ csrf_token() }}"
        data-bs-toggle="tooltip" title="{{ __('messages.delete') }}"
        data-confirm="{{ __('messages.are_you_sure?', ['module' => __('product.singular_title'), 'name' => $data->name]) }}">
        <i class="fa-solid fa-trash"></i></a>
</div>
