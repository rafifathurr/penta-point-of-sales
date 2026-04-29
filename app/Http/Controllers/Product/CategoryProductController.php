<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\CategoryProduct;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class CategoryProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Datatable Route
         */
        $datatable_route = route('category-product.dataTable');

        return view('category_product.index', compact('datatable_route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('category_product.create');
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Category Product
         */
        $category_product = CategoryProduct::whereNull('deleted_by')->whereNull('deleted_at')->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($category_product)
            ->addIndexColumn()
            ->addColumn('created_at', function ($data) {
                return date('d F Y H:i:s', strtotime($data->created_at));
            })
            ->addColumn('updated_at', function ($data) {
                return date('d F Y H:i:s', strtotime($data->updated_at));
            })
            ->addColumn('action', function ($data) {
                $btn_action = '<div class="d-flex justify-content-start">';
                $btn_action .= '<a href="' . route('category-product.edit', ['id' => $data->id]) . '" class="btn btn-warning d-flex align-items-center" title="Ubah"><i class="mdi mdi-lead-pencil mr-1"></i>Ubah</a>';
                $btn_action .= '<button class="btn btn-danger d-flex align-items-center ml-2" onclick="destroyRecord(' . $data->id . ')" title="Hapus"><i class="mdi mdi-delete d-flex align-items-center mr-1"></i>Hapus</button>';
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['name', 'description', 'created_at', 'updated_at', 'action'])
            ->rawColumns(['action'])
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
            ]);

            /**
             * Validation Unique Field Record
             */
            $name_check = CategoryProduct::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('name', $request->name)
                ->where('name', strtolower($request->name))
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($name_check)) {
                /**
                 * Begin Transaction
                 */
                DB::beginTransaction();

                /**
                 * Create Category Product Record
                 */
                $category_product = CategoryProduct::lockforUpdate()->create([
                    'name' => $request->name,
                    'description' => $request->description,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);

                /**
                 * Validation Create Category Product Record
                 */
                if ($category_product) {
                    DB::commit();
                    return redirect()
                        ->route('category-product.index')
                        ->with(['success' => 'Berhasil Simpan Kategori Produk']);
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Gagal Simpan Kategori Produk'])
                        ->withInput();
                }
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Nama Telah Tersedia'])
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
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            /**
             * Get Category Product Record from id
             */
            $category_product = CategoryProduct::find($id);

            /**
             * Validation Category Product id
             */
            if (!is_null($category_product)) {
                return view('category_product.edit', compact('category_product'));
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
            ]);

            /**
             * Validation Unique Field Record
             */
            $name_check = CategoryProduct::whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('name', $request->name)
                ->where('name', strtolower($request->name))
                ->where('id', '!=', $id)
                ->first();

            /**
             * Validation Unique Field Record
             */
            if (is_null($name_check)) {
                /**
                 * Get Category Product from id
                 */
                $category_product = CategoryProduct::find($id);

                /**
                 * Validation Category Product id
                 */
                if (!is_null($category_product)) {
                    /**
                     * Begin Transaction
                     */
                    DB::beginTransaction();

                    /**
                     * Update Category Product Record
                     */
                    $category_product_update = CategoryProduct::where('id', $id)->update([
                        'name' => $request->name,
                        'description' => $request->description,
                        'updated_by' => Auth::user()->id,
                    ]);

                    /**
                     * Validation Update Category Product Record
                     */
                    if ($category_product_update) {
                        DB::commit();
                        return redirect()
                            ->route('category-product.index')
                            ->with(['success' => 'Berhasil Perbarui Kategori Produk']);
                    } else {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Gagal Perbarui Kategori Produk'])
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
                    ->with(['failed' => 'Nama Telah Tersedia'])
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
             * Update Category Product Record
             */
            $category_product_destroy = CategoryProduct::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Category Product Record
             */
            if ($category_product_destroy) {
                DB::commit();
                session()->flash('success', 'Berhasil Hapus Kategori Produk');
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Gagal Hapus Kategori Produk');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
