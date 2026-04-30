<?php

namespace App\Http\Controllers\Product;

use App\Helpers\NumberFormat;
use App\Http\Controllers\Controller;
use App\Models\Product\CategoryProduct;
use App\Models\Product\Product;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Datatable Route
         */
        $datatable_route = route('product.dataTable');

        /**
         * Create Access Based Role
         */
        $can_create = User::find(Auth::user()->id)->hasRole(['admin']);

        return view('product.index', compact('datatable_route', 'can_create'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /**
         * Get All Category Product
         */
        $category_product = CategoryProduct::whereNull('deleted_by')->whereNull('deleted_at')->get();

        return view('product.create', compact('category_product'));
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Product
         */
        $products = Product::whereNull('deleted_by')->whereNull('deleted_at')->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('picture', function ($data) {
                return file_exists($data->picture) ? '<img src="' . asset($data->picture) . '" alt="" class="rounded w-25 h-25">' : null;
            })
            ->addColumn('category', function ($data) {
                /**
                 * Return Relation Category Product
                 */
                if (!is_null($data->category_product_id)) {
                    return $data->categoryProduct->name;
                } else {
                    return $data->category_product_id;
                }
            })
            ->addColumn('sell_price', function ($data) {
                return $data->sell_price ? '<div align="right">Rp. ' . NumberFormat::formatCurrency($data->sell_price) . '</div>' : '-';
            })
            ->addColumn('discount_price', function ($data) {
                return $data->discount_price ? '<div align="right">Rp. ' . NumberFormat::formatCurrency($data->discount_price) . '</div>' : '-';
            })
            ->addColumn('status', function ($data) {
                /**
                 * Validation Status
                 */
                if ($data->status == 1) {
                    return '<span class="badge badge-success pl-3 pr-3">Active</span>';
                } else {
                    if ($data->status == 0) {
                        return '<span class="badge badge-danger pl-3 pr-3">Inactive</span>';
                    }
                }
            })
            ->addColumn('created_at', function ($data) {
                return date('d F Y H:i:s', strtotime($data->created_at));
            })
            ->addColumn('updated_at', function ($data) {
                return date('d F Y H:i:s', strtotime($data->updated_at));
            })
            ->addColumn('action', function ($data) {
                $btn_action = '<div class="d-flex justify-content-start">';
                $btn_action .= '<a href="' . route('product.show', ['id' => $data->id]) . '" class="btn btn-primary d-flex align-items-center" title="Detail"><i class="mdi mdi-eye mr-1"></i>Detail</a>';

                /**
                 * Validation Role Has Access Edit and Delete
                 */
                if (User::find(Auth::user()->id)->hasRole(['admin'])) {
                    $btn_action .= '<a href="' . route('product.edit', ['id' => $data->id]) . '" class="btn btn-warning d-flex align-items-center ml-2" title="Ubah"><i class="mdi mdi-lead-pencil mr-1"></i>Ubah</a>';
                    $btn_action .= '<button class="btn btn-danger d-flex align-items-center ml-2" onclick="destroyRecord(' . $data->id . ')" title="Hapus"><i class="mdi mdi-delete d-flex align-items-center mr-1"></i>Hapus</button>';
                }
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['picture', 'name', 'category', 'sell_price', 'discount_price', 'status', 'created_at', 'updated_at', 'action'])
            ->rawColumns(['picture', 'sell_price', 'discount_price', 'status', 'action'])
            ->make(true);

        return $dataTable;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'name' => 'required|string',
                'barcode' => 'required',
                'category_product' => 'required',
                'status' => 'required',
                'capital_price' => 'required',
                'sell_price' => 'required',
                'picture' => 'required',
            ]);

            /**
             * Validation Unique Field Record
             */
            $name_check = Product::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where(function ($q) use ($request) {
                    $q->where('name', $request->name)
                        ->orWhereRaw('LOWER(name) = LOWER(?)', [$request->name]);
                })
                ->first();

            $barcode_check = Product::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('barcode', $request->barcode)
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($name_check)) {

                /**
                 * Validation Unique Field Record
                 */
                if (is_null($barcode_check)) {

                    /**
                     * Begin Transaction
                     */
                    DB::beginTransaction();

                    /**
                     * Create Product Record
                     */
                    $product = Product::lockforUpdate()->create([
                        'category_product_id' => $request->category_product,
                        'slug' => Str::slug($request->name),
                        'barcode' => $request->barcode,
                        'name' => $request->name,
                        'capital_price' => $request->capital_price,
                        'sell_price' => $request->sell_price,
                        'discount_price' => $request->discount_price,
                        'description' => $request->description,
                        'status' => $request->status,
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);

                    /**
                     * Validation Create Product Record
                     */
                    if ($product) {
                        /**
                         * Path Configuration
                         */
                        $path = 'public/uploads/product';
                        $path_store = 'storage/uploads/product';

                        /**
                         * Validation Check Path
                         */
                        if (!Storage::exists($path)) {
                            Storage::makeDirectory($path);
                        }

                        /**
                         * File Name Configuration
                         */
                        $exploded_name = explode(' ', strtolower($request->name));
                        $name_product_config = implode('_', $exploded_name);
                        $file = $request->file('picture');
                        $file_name = $product->id . '_' . $name_product_config . '.' . $file->getClientOriginalExtension();

                        /**
                         * Upload File
                         */
                        $file->storePubliclyAs($path, $file_name);

                        /**
                         * Validation File Success Uploaded
                         */
                        if (Storage::exists($path . '/' . $file_name)) {
                            /**
                             * Update Product with File Picture
                             */
                            $product_update = Product::where('id', $product->id)->update([
                                'picture' => $path_store . '/' . $file_name,
                            ]);

                            /**
                             * Validation Update Product Record
                             */
                            if ($product_update) {
                                DB::commit();
                                return redirect()
                                    ->route('product.index')
                                    ->with(['success' => 'Berhasil Simpan Produk']);
                            } else {
                                /**
                                 * Failed Store Record
                                 */
                                DB::rollBack();
                                return redirect()
                                    ->back()
                                    ->with(['failed' => 'Gagal Simpan Dan Upload Foto Produk'])
                                    ->withInput();
                            }
                        } else {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Gagal Upload Foto Produk'])
                                ->withInput();
                        }
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Gagal Tambah Produk'])
                            ->withInput();
                    }
                } else {
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Kode Barcode Produk Telah Tersedia'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Nama Produk Telah Tersedia'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            /**
             * Get Product Record from id
             */
            $product = Product::with(['categoryProduct'])->find($id);

            /**
             * Create Access Based Role
             */
            $can_show_capital = User::find(Auth::user()->id)->hasRole(['admin']);

            /**
             * Validation Product id
             */
            if (!is_null($product)) {
                return view('product.detail', compact('product', 'can_show_capital'));
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Data Tidak Ditemukan!']);
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
        }
    }

    /**
     * Show detail the specified resource.
     */
    public function getProduct(Request $request)
    {
        try {
            /**
             * Get Product Record from id
             */
            $product = Product::with(['categoryProduct'])->find($request->product);

            /**
             * Validation Product Size id
             */
            if (!is_null($product)) {
                return response()->json($product, 200);
            } else {
                return response()->json(null, 404);
            }
        } catch (Exception $e) {
            return response()->json($e->getMessage(), 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            /**
             * Get Product Record from id
             */
            $product = Product::with(['categoryProduct', 'createdBy', 'updatedBy'])->find($id);

            /**
             * Validation Product id
             */
            if (!is_null($product)) {
                /**
                 * Get All Category Product
                 */
                $category_product = CategoryProduct::whereNull('deleted_by')->whereNull('deleted_at')->get();

                return view('product.edit', compact('product', 'category_product'));
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Data Tidak Ditemukan!']);
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            /**
             * Validation Request Body Variables
             */
            $request->validate([
                'name' => 'required|string',
                'barcode' => 'required',
                'category_product' => 'required',
                'status' => 'required',
                'capital_price' => 'required',
                'sell_price' => 'required',
            ]);

            /**
             * Validation Unique Field Record
             */
            $name_check = Product::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('id', '!=', $id)
                ->where(function ($q) use ($request) {
                    $q->where('name', $request->name)
                        ->orWhereRaw('LOWER(name) = LOWER(?)', [$request->name]);
                })
                ->first();

            $barcode_check = Product::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('id', '!=', $id)
                ->where('barcode', $request->barcode)
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($name_check)) {

                /**
                 * Validation Unique Field Record
                 */
                if (is_null($barcode_check)) {

                    /**
                     * Get Product Record from id
                     */
                    $product = Product::find($id);

                    /**
                     * Validation Product id
                     */
                    if (!is_null($product)) {

                        /**
                         * Begin Transaction
                         */
                        DB::beginTransaction();

                        /**
                         * Update Product Record
                         */
                        $product_update = Product::where('id', $id)->update([
                            'category_product_id' => $request->category_product,
                            'slug' => Str::slug($request->name),
                            'barcode' => $request->barcode,
                            'name' => $request->name,
                            'capital_price' => $request->capital_price,
                            'sell_price' => $request->sell_price,
                            'discount_price' => $request->discount_price,
                            'description' => $request->description,
                            'status' => $request->status,
                            'updated_by' => Auth::user()->id,
                        ]);

                        /**
                         * Validation Update Product Record
                         */
                        if ($product_update) {

                            /**
                             * Validation update has request file
                             */
                            if (!empty($request->allFiles())) {
                                /**
                                 * Path Configuration
                                 */
                                $path = 'public/uploads/product';
                                $path_store = 'storage/uploads/product';

                                /**
                                 * Validation Check Path
                                 */
                                if (!Storage::exists($path)) {
                                    Storage::makeDirectory($path);
                                }

                                /**
                                 * Get Filename Picture Record
                                 */
                                $picture_record_exploded = explode('/', $product->picture);
                                $file_name_record = $picture_record_exploded[count($picture_record_exploded) - 1];

                                /**
                                 * Remove Has File Exist
                                 */
                                if (Storage::exists($path . '/' . $file_name_record)) {
                                    Storage::delete($path . '/' . $file_name_record);
                                }

                                /**
                                 * File Name Configuration
                                 */
                                $exploded_name = explode(' ', strtolower($request->name));
                                $name_product_config = implode('_', $exploded_name);
                                $file = $request->file('picture');
                                $file_name = $id . '_' . $name_product_config . '.' . $file->getClientOriginalExtension();

                                /**
                                 * Upload File
                                 */
                                $file->storePubliclyAs($path, $file_name);

                                /**
                                 * Validation File Success Uploaded
                                 */
                                if (Storage::exists($path . '/' . $file_name)) {
                                    /**
                                     * Update Product with File Picture
                                     */
                                    $product_picture_update = $product->update([
                                        'picture' => $path_store . '/' . $file_name,
                                    ]);

                                    /**
                                     * Validation Update Product Picture Record
                                     */
                                    if ($product_picture_update) {
                                        DB::commit();
                                        return redirect()
                                            ->route('product.index')
                                            ->with(['success' => 'Berhasil Perbarui Produk']);
                                    } else {
                                        /**
                                         * Failed Store Record
                                         */
                                        DB::rollBack();
                                        return redirect()
                                            ->back()
                                            ->with(['failed' => 'Gagal Perbarui Foto Produk'])
                                            ->withInput();
                                    }
                                } else {
                                    /**
                                     * Failed Store Record
                                     */
                                    DB::rollBack();
                                    return redirect()
                                        ->back()
                                        ->with(['failed' => 'Gagal Upload Foto Produk'])
                                        ->withInput();
                                }
                            } else {
                                DB::commit();
                                return redirect()
                                    ->route('product.index')
                                    ->with(['success' => 'Berhasil Perbarui Produk']);
                            }
                        } else {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Gagal Perbarui Produk'])
                                ->withInput();
                        }
                    } else {
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Data Tidak Ditemukan!']);
                    }
                } else {
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Kode Barcode Produk Telah Tersedia'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Nama Produk Telah Tersedia'])
                    ->withInput();
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Update Product Record
             */
            $product_destroy = Product::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Product Record
             */
            if ($product_destroy) {
                DB::commit();
                session()->flash('success', 'Product Successfully Deleted');
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Product');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
