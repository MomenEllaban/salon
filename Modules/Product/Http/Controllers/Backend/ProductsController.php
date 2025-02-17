<?php

namespace Modules\Product\Http\Controllers\Backend;

use App\Authorizable;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Location\Models\Location;
use Modules\Product\Http\Requests\ProductRequest;
use Modules\Product\Models\Brands;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductCategory;
use Modules\Product\Models\ProductGallery;
use Modules\Product\Models\ProductVariation;
use Modules\Product\Models\ProductVariationCombination;
use Modules\Product\Models\ProductVariationStock;
use Modules\Product\Models\Variations;
use Modules\Product\Models\VariationValue;
use Modules\Tag\Models\Tag;
use Yajra\DataTables\DataTables;

class ProductsController extends Controller
{
    // use Authorizable;

    public function __construct()
    {
        // Page Title
        $this->module_title = 'product.title';
        // module name
        $this->module_name = 'products';

        // module icon
        $this->module_icon = 'fa-solid fa-clipboard-list';

        $this->middleware(['permission:view_product_variations'])->only('index');
        $this->middleware(['permission:edit_product_variations'])->only('edit', 'update');
        $this->middleware(['permission:add_product_variations'])->only('store');
        $this->middleware(['permission:delete_product_variations'])->only('destroy');

        view()->share([
            'module_title' => $this->module_title,
            'module_icon' => $this->module_icon,
            'module_name' => $this->module_name,
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(Request $request)
    {
        $filter = [
            'status' => $request->status,
        ];

        $module_action = 'List';

        $export_import = true;
        $export_columns = [
            [
                'value' => 'name',
                'text' => ' Name',
            ],
        ];

        $brands = Brands::where('status', 1)->get();

        $categories = ProductCategory::where('status', 1)->get();

        $export_url = route('backend.products.export');

        return view('product::backend.products.index_datatable', compact('module_action', 'filter', 'export_import', 'export_columns', 'export_url', 'brands', 'categories'));
    }

    /**
     * Select Options for Select 2 Request/ Response.
     *
     * @return Response
     */
    public function index_list(Request $request)
    {
        $query_data = Product::query();

        $category_id = explode(',', $request->category_id);
        if (isset($category_id) && $request->category_id !== 'undefined') {
            if (is_array($category_id)) {
                $query_data = $query_data->whereHas('product_category', function ($q) use ($category_id) {
                    $q->whereIn('category_id', $category_id);
                });
            } else {
                $query_data = $query_data->whereHas('product_category', function ($q) use ($category_id) {
                    $q->where('category_id', $category_id);
                });
            }
        }

        $query_data = $query_data->get();

        $data = [];

        foreach ($query_data as $row) {
            $data[] = [
                'id' => $row->id,
                'name' => $row->name,
            ];
        }

        return response()->json($data);
    }

    public function index_list_with_varient()
    {
        $products = Product::where('status', 1)->get();

        $location_id = 1;

        $data = [];

        foreach ($products as $key => $product) {
            if ($product->has_variation) {
                foreach ($product->product_variations as $key => $product_variation) {

                    $code_array = array_filter(explode('/', $product_variation->variation_key));
                    $lstKey = array_key_last($code_array);
                    $name = '';
                    foreach ($code_array as $key2 => $comb) {
                        $comb = explode(':', $comb);
                        $variation = Variations::find($comb[0]);
                        $variationVal = VariationValue::find($comb[1]);
                        if ($variation && $variationVal) {
                            $option_name = $variation->name;
                            $choice_name = $variationVal->name;

                            $name .= $choice_name;

                            if ($lstKey != $key2) {
                                $name .= '-';
                            }
                        }
                    }

                    if ($name !== '') {

                        $stock = $product_variation->product_variation_stock()
                            ->where('location_id', $location_id)
                            ->first();
                        if ($stock->stock_qty > 0) {
                            $data[] = [
                                'id' => $product_variation->id,
                                'text' => $product->name.' - '.$name,
                                'extra_data' => json_encode(['variation_id' => $product_variation->id, 'product_id' => $product->id, 'discounted_price' => getDiscountedProductPrice($product_variation->price, $product->id), 'discount_value' => $product->discount_value, 'discount_type' => $product->discount_type, 'qty' => $stock->stock_qty, 'price' => $product_variation->price, 'variation_name' => $name]),
                            ];
                        }

                    }
                }

            } else {
                $first_variation = $product->product_variations->first();
                $first_variation_stock = $first_variation
                    ->product_variation_stock()
                    ->where('location_id', $location_id)
                    ->first();

                $price = $first_variation->price;
                $stock_qty = 0;
                if ($first_variation_stock) {
                    $stock_qty = $first_variation_stock->stock_qty;
                }
                $sku = $first_variation->sku;
                if ($stock_qty > 0) {
                    $data[] = [
                        'id' => $first_variation->id,
                        'text' => $product->name,
                        'extra_data' => json_encode(['variation_id' => $first_variation->id, 'product_id' => $product->id, 'qty' => $stock_qty, 'price' => $first_variation->price, 'discounted_price' => getDiscountedProductPrice($first_variation->price, $product->id), 'discount_value' => $product->discount_value, 'discount_type' => $product->discount_type, 'variation_name' => null]),
                    ];
                }
            }
        }

        return $data;
    }

    public function index_data(Request $request, Datatables $datatable)
    {
        $query = Product::with(['brand', 'categories']);

        $filter = $request->filter;

        if (isset($filter)) {
            if (isset($filter['column_status'])) {
                $query->where('status', $filter['column_status']);
            }
        }

        if (isset($filter)) {
            if (isset($filter['brand_id'])) {
                $query->where('brand_id', $filter['brand_id']);
            }
        }

        if (isset($filter) && isset($filter['category_id'])) {
            $query->whereHas('categories', function ($q) use ($filter) {
                $q->where('category_id', $filter['category_id']);
            });
        }

        return $datatable->eloquent($query)
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" class="form-check-input select-table-row"  id="datatable-row-'.$data->id.'"  name="datatable_ids[]" value="'.$data->id.'" onclick="dataTableRowCheck('.$data->id.')">';
            })
            ->addColumn('action', function ($data) {
                return view('product::backend.products.action_column', compact('data'));
            })
            // ->addColumn('image', function ($data) {
            //     return "<img src='".$data->feature_image."' class='avatar avatar-50 rounded-pill'>";
            // })
            ->editColumn('name', function ($data) {
                return view('backend.branch.branch_id', compact('data'));
            })
            ->editColumn('is_featured', function ($data) {
                $checked = '';
                if ($data->is_featured) {
                    $checked = 'checked="checked"';
                }

                return '
                            <div class="form-check form-switch ">
                                <input type="checkbox" data-url="'.route('backend.products.update_is_featured', $data->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input"  id="datatable-row-'.$data->id.'"  name="is_featured" value="'.$data->id.'" '.$checked.'>
                            </div>
                          ';
            })
            ->editColumn('status', function ($data) {
                $checked = '';
                if ($data->status) {
                    $checked = 'checked="checked"';
                }

                return '
                            <div class="form-check form-switch  ">
                                <input type="checkbox" data-url="'.route('backend.products.update_status', $data->id).'" data-token="'.csrf_token().'" class="switch-status-change form-check-input"  id="datatable-row-'.$data->id.'"  name="status" value="'.$data->id.'" '.$checked.'>
                            </div>
                          ';
            })
            ->editColumn('categories', function ($data) {
                $categories = '<div class="d-flex flex-wrap gap-2">';

                if (count($data->categories) > 0) {
                    foreach ($data->categories as $key => $value) {
                        $categories .= '<span class="badge rounded-pill bg-secondary">'.$value->name.'</span>';
                    }
                } else {
                    $categories .= '-';
                }
                $categories .= '</div>';

                return $categories;
            })
            ->filterColumn('categories', function ($query, $keyword) {
                $query->whereHas('categories', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', '%'.$keyword.'%');
                });
            })
            ->editColumn('min_price', function ($data) {
                if ($data->max_price != $data->min_price) {
                    return \Currency::format($data->min_price).' - '.\Currency::format($data->max_price);
                } else {
                    return \Currency::format($data->min_price);
                }
            })
            ->editColumn('brand', function ($data) {
                return $data->brand->name ?? '-';
            })
            ->orderColumn('brand', function ($query, $order) {
                $query->select('products.*')
                    ->leftJoin('brands', 'brands.id', '=', 'products.brand_id')
                    ->orderBy('brands.name', $order);
            }, 1)
            ->filterColumn('brand', function ($query, $keyword) {
                if (! empty($keyword)) {
                    $query->whereHas('brand', function ($q) use ($keyword) {
                        $q->where('name', 'like', '%'.$keyword.'%');
                    });
                }
            })
            ->editColumn('updated_at', function ($data) {
                $module_name = $this->module_name;

                $diff = Carbon::now()->diffInHours($data->updated_at);

                if ($diff < 25) {
                    return $data->updated_at->diffForHumans();
                } else {
                    return $data->updated_at->isoFormat('llll');
                }
            })
            ->rawColumns(['action', 'status', 'image', 'check', 'categories', 'is_featured'])
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(ProductRequest $request)
    {
        if ($request->has_variation == 1 && ! $request->has('combinations')) {
            return response()->json(['message' => 'Invalid product variations, please check again', 'status' => false], 402);
        }

        $product = new Product;
        $product->name = $request->name;
        $product->slug = Str::slug($request->name, '-').'-'.strtolower(Str::random(5));
        $product->brand_id = $request->brand_id;
        $product->unit_id = $request->unit_id;
        $product->sell_target = $request->sell_target ?? 0;

        $product->description = $request->description;
        $product->short_description = $request->short_description;

        if ($request->has('has_variation') && $request->has('combinations') && $request->has_variation && $request->combinations != 'undefined') {
            $request->combinations = json_decode($request->combinations, true);

            $product->min_price = min(array_column($request->combinations, 'price'));
            $product->max_price = max(array_column($request->combinations, 'price'));
        } else {
            $product->min_price = $request->price;
            $product->max_price = $request->price;
        }

        // discount
        $product->discount_value = $request->discount_value ?? 0;
        $product->discount_type = $request->discount_type;

        if ($request->date_range != null) {
            if (Str::contains($request->date_range, 'to')) {
                $date_var = explode(' to ', $request->date_range);
            } else {
                $date_var = [date('d-m-Y'), date('d-m-Y')];
            }
            $product->discount_start_date = strtotime($date_var[0]);
            $product->discount_end_date = strtotime($date_var[1]);
        }

        // stock qty based on all variations / no variation
        if (
            $request->has_variation == 1 && $request->has('combinations') && is_array($request->combinations) && ! empty($request->combinations)) {
            $product->stock_qty = array_sum(array_column($request->combinations, 'stock'));
        } else {
            $product->stock_qty = $request->stock;
        }

        $product->status = $request->status;
        $product->has_variation = ($request->has_variation == 1 && count($request->combinations) > 0) ? 1 : 0;

        // shipping info
        $product->standard_delivery_hours = $request->standard_delivery_hours ?? 72;
        $product->express_delivery_hours = $request->express_delivery_hours ?? 24;
        $product->min_purchase_qty = $request->min_purchase_qty ?? 1;
        $product->max_purchase_qty = $request->max_purchase_qty ?? 1;

        $product->is_featured = $request->is_featured;
        $product->save();

        // tags
        $tag_ids = [];

        if (! empty($request->tags) && is_string($request->tags) && $request->taxes !== 'undefined') {
            $request->tags = json_decode($request->tags, true);

            foreach ($request->tags as $key => $value) {
                $tag = Tag::updateOrCreate(['name' => $value], ['name' => $value]);
                $tag_ids[] = $tag->id;
            }
        }

        $product->tags_data()->sync($tag_ids);

        // category

        $category_ids = [];

        if (! empty($request->category_ids) && is_string($request->category_ids) && $request->category_ids !== 'undefined') {
            $request->category_ids = json_decode($request->category_ids, true);
            $product->categories()->sync($request->category_ids);
        }

        $location = Location::where('is_default', 1)->first();
        if ($request->has_variation == 1) {
            if ($request->has('combinations') && is_array($request->combinations) && ! empty($request->combinations)) {
                foreach ($request->combinations as $variation) {
                    $product_variation = new ProductVariation;
                    $product_variation->product_id = $product->id;
                    $product_variation->variation_key = $variation['variation_key'];
                    $product_variation->price = $variation['price'];
                    $product_variation->sku = $variation['sku'];
                    $product_variation->code = $variation['code'];
                    $product_variation->save();

                    $product_variation_stock = new ProductVariationStock;
                    $product_variation_stock->product_variation_id = $product_variation->id;
                    $product_variation_stock->location_id = $location->id;
                    $product_variation_stock->stock_qty = $variation['stock'];
                    $product_variation_stock->save();

                    foreach (array_filter(explode('/', $variation['variation_key'])) as $combination) {
                        $product_variation_combination = new ProductVariationCombination;
                        $product_variation_combination->product_id = $product->id;
                        $product_variation_combination->product_variation_id = $product_variation->id;
                        $product_variation_combination->variation_id = explode(':', $combination)[0];
                        $product_variation_combination->variation_value_id = explode(':', $combination)[1];
                        $product_variation_combination->save();
                    }
                }
            }
        } else {
            $variation = new ProductVariation;
            $variation->product_id = $product->id;
            $variation->sku = $request->sku;
            $variation->code = $request->code;
            $variation->price = $request->price;
            $variation->save();
            $product_variation_stock = new ProductVariationStock;
            $product_variation_stock->product_variation_id = $variation->id;
            $product_variation_stock->location_id = $location->id;
            $product_variation_stock->stock_qty = $request->stock;
            $product_variation_stock->save();
        }

        if ($request->feature_image) {
            storeMediaFile($product, $request->file('feature_image'));
        }

        $message = 'New Product Added';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $data = Product::findOrFail($id);
        $data->category_ids = $data->categories->pluck('id')->toArray();
        $data->tags = $data->tags_data->pluck('name')->toArray();
        $data->date_range = date('Y-m-d', $data->discount_start_date).' to '.date('Y-m-d', $data->discount_end_date);

        if ($data->has_variation) {
            $varComb = $data->variation_combinations()->select('variation_id', 'variation_value_id')->get()->toArray();

            $groupedData = [];

            foreach ($varComb as $item) {
                $variationId = $item['variation_id'];
                $variationValueId = $item['variation_value_id'];

                if (! isset($groupedData[$variationId])) {
                    $groupedData[$variationId] = [];
                }

                if (! in_array($variationValueId, $groupedData[$variationId])) {
                    $groupedData[$variationId][] = $variationValueId;
                }
            }

            $finalGroupedData = [];
            foreach ($groupedData as $variationId => $variationValueIds) {
                $finalGroupedData[] = [
                    'variation' => $variationId,
                    'variationValue' => $variationValueIds,
                ];
            }

            $variations = $finalGroupedData;
            $combinations = $data->product_variations;
            $location = Location::where('is_default', 1)->first();
            foreach ($combinations as $key => $value) {
                $combinations[$key] = [
                    'product_id' => $value->product_id,
                    'variation' => $value->sku,
                    'variation_key' => $value->variation_key,
                    'stock' => $value->product_variation_stock()->where('location_id', $location->id)->first()->stock_qty,
                    'code' => $value->code,
                    'sku' => $value->sku,
                    'price' => $value->price,
                ];
            }
            $data['variations'] = $variations;
            $data['combinations'] = $combinations;
        } else {
            $variation = $data->product_variations->first();
            $location = Location::where('is_default', 1)->first();
            $data['stock'] = $variation->product_variation_stock()->where('location_id', $location->id)->first()->stock_qty;
            $data['code'] = $variation->code;
            $data['price'] = $variation->price;
            $data['sku'] = $variation->sku;
        }

        return response()->json(['data' => $data, 'status' => true]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(ProductRequest $request, $id)
    {
        if ($request->has('has_variation') && ! $request->has('combinations')) {
            return response()->json(['message' => 'Invalid product variations, please check again', 'status' => false], 402);
        }
        $product = Product::findOrFail($id);

        $oldProduct = clone $product;

        $product->name = $request->name;
        $product->slug = (! is_null($request->slug)) ? Str::slug($request->slug, '-') : Str::slug($request->name, '-').'-'.strtolower(Str::random(5));
        $product->description = $request->description;
        $product->sell_target = $request->sell_target;
        $product->brand_id = $request->brand_id;
        $product->unit_id = $request->unit_id;
        $product->short_description = $request->short_description;

        if ($request->has('has_variation') && $request->has('combinations') && $request->has_variation && $request->combinations != 'undefined') {
            $request->combinations = json_decode($request->combinations, true);

            $product->min_price = min(array_column($request->combinations, 'price'));
            $product->max_price = max(array_column($request->combinations, 'price'));
        } else {
            $product->min_price = $request->price;
            $product->max_price = $request->price;
        }

        // discount
        $product->discount_value = $request->discount_value ?? 0;
        $product->discount_type = $request->discount_type;

        if ($request->date_range != null) {
            if (Str::contains($request->date_range, 'to')) {
                $date_var = explode(' to ', $request->date_range);
            } else {
                $date_var = [date('d-m-Y'), date('d-m-Y')];
            }
            $product->discount_start_date = strtotime($date_var[0]);
            $product->discount_end_date = strtotime($date_var[1]);
        }

        // stock qty based on all variations / no variation
        if (
            $request->has('has_variation') && $request->has('combinations') && is_array($request->combinations) && ! empty($request->combinations)) {
            $product->stock_qty = array_sum(array_column($request->combinations, 'stock'));
        } else {
            $product->stock_qty = $request->stock;
        }

        $product->status = $request->status;
        $product->has_variation = ($request->has_variation == 1 && $request->has('combinations')) ? 1 : 0;

        // shipping info
        $product->standard_delivery_hours = $request->standard_delivery_hours ?? 0;
        $product->express_delivery_hours = $request->express_delivery_hours ?? 0;
        $product->min_purchase_qty = $request->min_purchase_qty ?? 0;
        $product->max_purchase_qty = $request->max_purchase_qty ?? 0;

        $product->is_featured = $request->is_featured;
        $product->save();

        // tags
        $tag_ids = [];

        if (! empty($request->tags) && is_string($request->tags) && $request->taxes !== 'undefined') {
            $request->tags = json_decode($request->tags, true);

            foreach ($request->tags as $key => $value) {
                $tag = Tag::updateOrCreate(['name' => $value], ['name' => $value]);
                $tag_ids[] = $tag->id;
            }
        }

        $product->tags_data()->sync($tag_ids);

        // category

        $category_ids = [];

        if (! empty($request->category_ids) && is_string($request->category_ids) && $request->category_ids !== 'undefined') {
            $request->category_ids = json_decode($request->category_ids, true);

            foreach ($request->category_ids as $key => $value) {
                $category_ids[] = [
                    'category_id' => $value,
                ];
            }
            $product->categories()->sync($request->category_ids);
        }

        // taxes
        $tax = [];

        if (! empty($request->taxes) && is_string($request->taxes) && $request->taxes !== 'undefined') {
            $request->taxes = json_decode($request->taxes, true);

            foreach ($request->taxes as $key => $value) {
                if (isset($value['tax_id'], $value['tax_value'], $value['tax_type'])) {
                    $tax[] = [
                        'tax_id' => $value['tax_id'],
                        'tax_value' => $value['tax_value'],
                        'tax_type' => $value['tax_type'],
                    ];
                }
            }
            $product->product_taxes()->sync($tax);
        }

        $location = Location::where('is_default', 1)->first();

        if ($request->has_variation == 1 && $request->has('combinations') && is_array($request->combinations) && ! empty($request->combinations)) {
            // foreach ($request->combinations as $variation) {

            $new_requested_variations = collect($request->combinations);
            $new_requested_variations_key = $new_requested_variations->pluck('variation_key')->toArray();
            $old_variations_keys = $product->product_variations->pluck('variation_key')->toArray();
            $old_matched_variations = $new_requested_variations->whereIn('variation_key', $old_variations_keys);
            $new_variations = $new_requested_variations->whereNotIn('variation_key', $old_variations_keys);

            // delete old variations that isn't requested
            $product->product_variations->whereNotIn('variation_key', $new_requested_variations_key)->each(function ($variation) use ($location) {
                foreach ($variation->combinations as $comb) {
                    $comb->delete();
                }
                $variation->product_variation_stock_without_location()->where('location_id', $location->id)->delete();
                $variation->delete();
            });

            // update old matched variations
            foreach ($old_matched_variations as $variation) {
                $p_variation = ProductVariation::where('product_id', $product->id)->where('variation_key', $variation['variation_key'])->first();
                $p_variation->price = $variation['price'];
                $p_variation->sku = $variation['sku'];
                $p_variation->code = $variation['code'];
                $p_variation->save();

                // update stock of this variation
                $productVariationStock = $p_variation->product_variation_stock_without_location()->where('location_id', $location->id)->first();
                if (is_null($productVariationStock)) {
                    $productVariationStock = new ProductVariationStock;
                    $productVariationStock->product_variation_id = $p_variation->id;
                }
                $productVariationStock->stock_qty = $variation['stock'];
                $productVariationStock->location_id = $location->id;
                $productVariationStock->save();
            }

            // store new requested variations
            foreach ($new_variations as $variation) {
                $product_variation = new ProductVariation;
                $product_variation->product_id = $product->id;
                $product_variation->variation_key = $variation['variation_key'];
                $product_variation->price = $variation['price'];
                $product_variation->sku = $variation['sku'];
                $product_variation->code = $variation['code'];
                $product_variation->save();

                $product_variation_stock = new ProductVariationStock;
                $product_variation_stock->product_variation_id = $product_variation->id;
                $product_variation_stock->stock_qty = $variation['stock'];
                $product_variation_stock->save();

                foreach (array_filter(explode('/', $variation['variation_key'])) as $combination) {
                    $product_variation_combination = new ProductVariationCombination;
                    $product_variation_combination->product_id = $product->id;
                    $product_variation_combination->product_variation_id = $product_variation->id;
                    $product_variation_combination->variation_id = explode(':', $combination)[0];
                    $product_variation_combination->variation_value_id = explode(':', $combination)[1];
                    $product_variation_combination->save();
                }
            }
        } else {
            // check if old product is variant then delete all old variation & combinations
            if ($oldProduct->has_variation) {
                if (isset($product->product_variations)) {
                    foreach ($product->product_variations as $variation) {
                        if (isset($variation->combinations)) {
                            foreach ($variation->combinations as $comb) {
                                $comb->delete();
                            }
                            $variation->delete();
                        }
                    }
                }
            }

            $variation = $product->product_variations->first();
            $variation->product_id = $product->id;
            $variation->variation_key = null;
            $variation->sku = $request->sku;
            $variation->code = $request->code;
            $variation->price = $request->price;
            $variation->save();

            if ($variation->product_variation_stock) {
                $productVariationStock = $variation->product_variation_stock_without_location()->where('location_id', $location->id)->first();

                if (is_null($productVariationStock)) {
                    $productVariationStock = new ProductVariationStock;
                }

                $productVariationStock->product_variation_id = $variation->id;
                $productVariationStock->stock_qty = $request->stock;
                $productVariationStock->location_id = $location->id;
                $productVariationStock->save();
            } else {
                $product_variation_stock = new ProductVariationStock;
                $product_variation_stock->product_variation_id = $variation->id;
                $product_variation_stock->stock_qty = $request->stock;
                $product_variation_stock->save();
            }
        }

        if ($request->feature_image == null) {
            $product->clearMediaCollection('feature_image');
        }

        if ($request->hasFile('feature_image')) {
            storeMediaFile($product, $request->file('feature_image'));
        }

        $message = 'Product Updated Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if (env('IS_DEMO')) {
            return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
        }
        $data = Product::findOrFail($id);

        $data->delete();

        $message = 'Product Deleted Successfully';

        return response()->json(['message' => $message, 'status' => true], 200);
    }

    public function update_status(Request $request, Product $id)
    {
        $id->update(['status' => $request->status]);

        return response()->json(['status' => true, 'message' => __('product.status_update')]);
    }

    public function update_is_featured(Request $request, Product $id)
    {
        $id->update(['is_featured' => $request->status]);

        return response()->json(['status' => true, 'message' => __('product.is_featured_update')]);
    }

    public function bulk_action(Request $request)
    {

        $ids = explode(',', $request->rowIds);

        $actionType = $request->action_type;

        $message = __('messages.bulk_update');

        // dd($actionType, $ids, $request->status);
        switch ($actionType) {
            case 'change-is_featured':
                Product::whereIn('id', $ids)->update(['is_featured' => $request->is_featured]);
                break;

            case 'change-status':
                $products = Product::whereIn('id', $ids)->update(['status' => $request->status]);
                $message = __('messages.bulk_status_update');
                break;

            case 'delete':
                if (env('IS_DEMO')) {
                    return response()->json(['message' => __('messages.permission_denied'), 'status' => false], 200);
                }
                $products = Product::whereIn('id', $ids)->delete();
                $message = __('messages.bulk_status_delete');
                break;

            default:
                return response()->json(['status' => false, 'message' => __('product.invalid_action')]);
                break;
        }

        return response()->json(['status' => true, 'message' => __('messages.bulk_update')]);
    }

    public function getGalleryImages($id)
    {
        $product = Product::findOrFail($id);

        $data = ProductGallery::where('product_id', $id)->get();

        return response()->json(['data' => $data, 'product' => $product, 'status' => true]);
    }

    public function uploadGalleryImages(Request $request, $id)
    {
        $gallery = collect($request->gallery, true);

        $images = ProductGallery::where('product_id', $id)->whereNotIn('id', $gallery->pluck('id'))->get();

        foreach ($images as $key => $value) {
            $value->clearMediaCollection('gallery_images');
            $value->delete();
        }

        foreach ($gallery as $key => $value) {
            if ($value['id'] == 'null') {
                $productGallery = ProductGallery::create([
                    'product_id' => $id,
                ]);

                $productGallery->addMedia($value['file'])->toMediaCollection('gallery_images');

                $productGallery->full_url = $productGallery->getFirstMediaUrl('gallery_images');
                $productGallery->save();
            }
        }

        return response()->json(['message' => __('product.update_product_gallery'), 'status' => true]);
    }
}
