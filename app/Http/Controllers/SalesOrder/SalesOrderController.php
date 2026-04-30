<?php

namespace App\Http\Controllers\SalesOrder;

use App\Helpers\NumberFormat;
use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\SalesOrder\PaymentMethod;
use App\Models\SalesOrder\SalesOrder;
use App\Models\SalesOrder\SalesOrderItem;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class SalesOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * Datatable Route
         */
        $datatable_route = route('sales-order.dataTable');

        /**
         * Create Route
         */
        $create_route = route('sales-order.create');

        return view('sales_order.index', compact('datatable_route', 'create_route'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        /**
         * Store Route
         */
        $store_route = route('sales-order.store');

        $payment_method = PaymentMethod::whereNull('deleted_by')->whereNull('deleted_at')->get();

        /**
         * Statement sales order create
         */
        $hide_button_hamburger_nav = true;

        return view('sales_order.create', compact('store_route', 'payment_method', 'hide_button_hamburger_nav'));
    }

    /**
     * Get Catalogue Menu
     */
    public function catalogueProduct(Request $request)
    {
        /**
         * Request Parameter
         */
        $input = $request->all();

        /**
         * Request For Create Order
         */
        if (!is_null($input['query'])) {
            /**
             * Get All Product
             */
            $products = Product::with(['categoryProduct'])
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('status', 1)
                ->where(function ($q) use ($input) {
                    $q->where('barcode', 'like', '%' . $input['query'] . '%')
                        ->orWhere('name', 'like', '%' . $input['query'] . '%')
                        ->orWhereRaw('LOWER(name) LIKE LOWER(?)', ['%' . $input['query'] . '%']);
                })
                ->paginate(4);
        } else {
            /**
             * Get All Product
             */
            $products = Product::with(['categoryProduct'])
                ->whereNull('deleted_by')
                ->whereNull('deleted_at')
                ->where('status', 1)
                ->paginate(4);
        }

        if (count($products) > 0) {
            return view('sales_order.partials.catalogue', ['products' => $products, 'update' => false]);
        } else {
            return view('sales_order.partials.notfound');
        }
    }

    /**
     * Show datatable of resource.
     */
    public function dataTable()
    {
        /**
         * Get All Sales Order
         */
        $sales_order = SalesOrder::with(['paymentMethod'])
            ->whereNull('deleted_by')
            ->whereNull('deleted_at')
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        /**
         * Datatable Configuration
         */
        $dataTable = DataTables::of($sales_order)
            ->addIndexColumn()
            ->addColumn('date', function ($data) {
                /**
                 * Return Format Date & Time
                 */
                return date('d F Y', strtotime($data->date));
            })
            ->addColumn('payment_method', function ($data) {
                /**
                 * Return Format Date & Time
                 */
                return $data->paymentMethod->name;
            })
            ->addColumn('total_sell_price', function ($data) {
                return '<div align="right"> Rp. ' . NumberFormat::formatCurrency($data->total_sell_price) . '</div>';
            })
            ->addColumn('discount_price', function ($data) {
                return $data->discount_price ? '<div align="right">Rp. ' . NumberFormat::formatCurrency($data->discount_price) . '</div>' : '-';
            })
            ->addColumn('grand_sell_price', function ($data) {
                return '<div align="right"> Rp. ' . NumberFormat::formatCurrency($data->grand_sell_price) . '</div>';
            })
            ->addColumn('date_ordering', function ($data) {
                /**
                 * Return Format Date & Time
                 */
                if (!is_null($data->date)) {
                    return date('Y-m-d', strtotime($data->date));
                } else {
                    return date('Y-m-d', strtotime($data->created_at));
                }
            })
            ->addColumn('created_by', function ($data) {
                return $data->createdBy->name;
            })
            ->addColumn('created_at', function ($data) {
                /**
                 * Return Format Date & Time
                 */
                return date('d F Y H:i:s', strtotime($data->created_at));
            })
            ->addColumn('updated_by', function ($data) {
                return $data->updatedBy->name;
            })
            ->addColumn('updated_at', function ($data) {
                /**
                 * Return Format Date & Time
                 */
                return date('d F Y H:i:s', strtotime($data->updated_at));
            })
            ->addColumn('action', function ($data) {
                $btn_action = '<div class="d-flex justify-content-start">';
                $btn_action .= '<a href="' . route('sales-order.show', ['id' => $data->id]) . '" class="btn btn-primary d-flex align-items-center" title="Detail"><i class="mdi mdi-eye mr-1"></i>Detail</a>';

                /**
                 * Validation Role Has Access Edit and Delete
                 */
                if (User::find(Auth::user()->id)->hasRole(['cashier'])) {
                    if (Auth::user()->id == $data->created_by) {
                        $btn_action .= '<a href="' . route('sales-order.edit', ['id' => $data->id]) . '" class="btn btn-warning d-flex align-items-center ml-2" title="Ubah"><i class="mdi mdi-lead-pencil mr-1"></i>Ubah</a>';
                        $btn_action .= '<button class="btn btn-danger d-flex align-items-center ml-2" onclick="destroyRecord(' . $data->id . ')" title="Hapus"><i class="mdi mdi-delete d-flex align-items-center mr-1"></i>Hapus</button>';
                    }
                } else {
                    $btn_action .= '<a href="' . route('sales-order.edit', ['id' => $data->id]) . '" class="btn btn-warning d-flex align-items-center ml-2" title="Ubah"><i class="mdi mdi-lead-pencil mr-1"></i>Ubah</a>';
                    $btn_action .= '<button class="btn btn-danger d-flex align-items-center ml-2" onclick="destroyRecord(' . $data->id . ')" title="Hapus"><i class="mdi mdi-delete d-flex align-items-center mr-1"></i>Hapus</button>';
                }
                $btn_action .= '<a href="' . route('sales-order.invoice', ['id' => $data->id]) . '" class="btn btn-light d-flex align-items-center ml-2" target="_blank" title="Cetak"><i class="mdi mdi-printer mr-1"></i>Cetak</a>';
                $btn_action .= '</div>';
                return $btn_action;
            })
            ->only(['invoice_number', 'date', 'date_ordering', 'created_by', 'created_at', 'updated_by', 'updated_at', 'payment_method', 'total_sell_price', 'discount_price', 'grand_sell_price', 'action'])
            ->rawColumns(['total_sell_price', 'discount_price', 'grand_sell_price', 'action'])
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
                'date' => 'required',
                'payment_method' => 'required',
                'sales_order_item' => 'required',
                'total_capital_price' => 'required',
                'total_sell_price' => 'required',
                'discount_price' => 'required',
                'grand_sell_price' => 'required',
                'grand_profit_price' => 'required',
            ]);

            /**
             * Create Invoice Number
             */
            $invoice_number = 'INV/' . date('Y') . '/' . strtotime($request->date) . '/' . strtotime(date('Y-m-d H:i:s'));

            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Create Sales Order Record
             */
            $sales_order = SalesOrder::lockforUpdate()->create([
                'date' => $request->date,
                'invoice_number' => $invoice_number,
                'payment_method_id' => $request->payment_method,
                'total_capital_price' => $request->total_capital_price,
                'total_sell_price' => $request->total_sell_price,
                'discount_price' => $request->discount_price,
                'grand_sell_price' => $request->grand_sell_price,
                'grand_profit_price' => $request->grand_profit_price,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
            ]);

            /**
             * Validation Create Stock In Record
             */
            if ($sales_order) {
                /**
                 * Each Sales Order Item Product Request
                 */
                foreach ($request->sales_order_item as $product_id => $sales_order_item_request_product) {
                    /**
                     * Create Sales Order Item Record
                     */
                    $sales_order_item = SalesOrderItem::lockforUpdate()->create([
                        'product_id' => $product_id,
                        'sales_order_id' => $sales_order->id,
                        'qty' => $sales_order_item_request_product['qty'],
                        'capital_price' => $sales_order_item_request_product['capital_price'],
                        'sell_price' => $sales_order_item_request_product['sell_price'],
                        'discount_price' => $sales_order_item_request_product['discount_price'],
                        'total_sell_price' => $sales_order_item_request_product['total_sell_price'],
                        'total_profit_price' => $sales_order_item_request_product['total_profit_price'],
                        'created_by' => Auth::user()->id,
                        'updated_by' => Auth::user()->id,
                    ]);

                    /**
                     * Validation Create Sales Order Item Record
                     */
                    if (!$sales_order_item) {
                        /**
                         * Failed Store Record
                         */
                        DB::rollBack();
                        return redirect()
                            ->back()
                            ->with(['failed' => 'Gagal Simpan Produk Penjualan'])
                            ->withInput();
                    }
                }

                DB::commit();
                return redirect()
                    ->route('sales-order.show', ['id' => $sales_order->id])
                    ->with(['success' => 'Berhasil Simpan Penjualan']);
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Gagal Simpan Penjualan'])
                    ->withInput();
            }
        } catch (Exception $e) {

            dd($e->getMessage());
            return redirect()
                ->route('sales-order.create')
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
             * Get Sales Order Record from id
             */
            $sales_order = SalesOrder::with(['paymentMethod', 'salesOrderItem.product'])->find($id);

            /**
             * Validation Sales Order id
             */
            if (!is_null($sales_order)) {
                /**
                 * Show Capital Price Access Based Role
                 */
                $show_capital_price = User::find(Auth::user()->id)->hasRole(['admin']);

                return view('sales_order.detail', compact('sales_order', 'show_capital_price'));
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
        }
    }

    /**
     * Export invoice the specified resource.
     */
    public function invoice(string $id)
    {
        try {
            /**
             * Get Sales Order Record from id
             */
            $sales_order = SalesOrder::with(['paymentMethod', 'salesOrderItem.product'])->find($id);

            /**
             * Validation Sales Order id
             */
            if (!is_null($sales_order)) {
                /**
                 * Return PDF format
                 */
                return PDF::loadView('sales_order.invoice', ['sales_order' => $sales_order])->stream($sales_order->invoice_number . '.pdf');
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
            }
        } catch (Exception $e) {
            return redirect()
                ->back()
                ->with(['failed' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        try {
            /**
             * Get Sales Order Record from id
             */
            $sales_order = SalesOrder::with(['paymentMethod', 'salesOrderItem.product'])->find($id);

            /**
             * Validation Sales Order id
             */
            if (!is_null($sales_order)) {
                /**
                 * Update Route
                 */
                $update_route = route('sales-order.update', ['id' => $id]);

                /**
                 * Get All Payment Method
                 */
                $payment_method = PaymentMethod::whereNull('deleted_by')->whereNull('deleted_at')->get();

                /**
                 * Statement sales order create
                 */
                $hide_button_hamburger_nav = true;

                return view('sales_order.edit', compact('update_route', 'sales_order', 'payment_method', 'hide_button_hamburger_nav'));
            } else {
                return redirect()
                    ->back()
                    ->with(['failed' => 'Invalid Request!']);
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
                'date' => 'required',
                'payment_method' => 'required',
                'sales_order_item' => 'required',
                'total_capital_price' => 'required',
                'total_sell_price' => 'required',
                'discount_price' => 'required',
                'grand_sell_price' => 'required',
                'grand_profit_price' => 'required',
            ]);

            /**
             * Last Sales Order Record
             */
            $sales_order = SalesOrder::with(['paymentMethod', 'salesOrderItem.product'])->find($id);

            /**
             * Begin Transaction
             */
            DB::beginTransaction();

            /**
             * Update Sales Order
             */
            $sales_order_updated = SalesOrder::where('id', $id)->update([
                'date' => $request->date,
                'payment_method_id' => $request->payment_method,
                'total_capital_price' => $request->total_capital_price,
                'total_sell_price' => $request->total_sell_price,
                'discount_price' => $request->discount_price,
                'grand_sell_price' => $request->grand_sell_price,
                'grand_profit_price' => $request->grand_profit_price,
                'updated_by' => Auth::user()->id,
            ]);

            if ($sales_order_updated) {

                /**
                 * Destroy Last Sales Order Item
                 */
                $sales_order_item_destroy = SalesOrderItem::where('sales_order_id', $id)->update([
                    'deleted_by' => Auth::user()->id,
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);

                if ($sales_order_item_destroy) {

                    /**
                     * Each Sales Order Item Product Request
                     */
                    foreach ($request->sales_order_item as $product_id => $sales_order_item_request_product) {

                        /**
                         * Create Sales Order Item Record
                         */
                        $sales_order_item = SalesOrderItem::lockforUpdate()->create([
                            'product_id' => $product_id,
                            'sales_order_id' => $sales_order->id,
                            'qty' => $sales_order_item_request_product['qty'],
                            'capital_price' => $sales_order_item_request_product['capital_price'],
                            'sell_price' => $sales_order_item_request_product['sell_price'],
                            'discount_price' => $sales_order_item_request_product['discount_price'],
                            'total_sell_price' => $sales_order_item_request_product['total_sell_price'],
                            'total_profit_price' => $sales_order_item_request_product['total_profit_price'],
                            'created_by' => Auth::user()->id,
                            'updated_by' => Auth::user()->id,
                        ]);

                        /**
                         * Validation Create Sales Order Item Record
                         */
                        if (!$sales_order_item) {
                            /**
                             * Failed Store Record
                             */
                            DB::rollBack();
                            return redirect()
                                ->back()
                                ->with(['failed' => 'Gagal Perbarui dan Simpan Produk Penjualan'])
                                ->withInput();
                        }
                    }

                    DB::commit();
                    return redirect()
                        ->route('sales-order.show', ['id' => $sales_order->id])
                        ->with(['success' => 'Berhasil Perbarui Penjualan']);
                } else {
                    /**
                     * Failed Update Destroy
                     */
                    DB::rollBack();
                    return redirect()
                        ->back()
                        ->with(['failed' => 'Gagal Hapus Data Produk Penjualan'])
                        ->withInput();
                }
            } else {
                /**
                 * Failed Update Record
                 */
                DB::rollBack();
                return redirect()
                    ->back()
                    ->with(['failed' => 'Failed Update Sales Order'])
                    ->withInput();
            }
        } catch (Exception $e) {

            dd($e->getMessage());
            return redirect()
                ->route('sales-order.update', ['id' => $id])
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
             * Update Sales Order Record
             */
            $sales_order_destroy = SalesOrder::where('id', $id)->update([
                'deleted_by' => Auth::user()->id,
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);

            /**
             * Validation Update Sales Order Record
             */
            if ($sales_order_destroy) {
                /**
                 * Update Sales Order Item Record
                 */
                $sales_order_item_destroy = SalesOrderItem::where('sales_order_id', $id)->update([
                    'deleted_by' => Auth::user()->id,
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);

                if ($sales_order_item_destroy) {
                    DB::commit();
                    session()->flash('success', 'Sales Order Successfully Deleted');
                } else {
                    /**
                     * Failed Store Record
                     */
                    DB::rollBack();
                    session()->flash('failed', 'Failed Delete Sales Order');
                }
            } else {
                /**
                 * Failed Store Record
                 */
                DB::rollBack();
                session()->flash('failed', 'Failed Delete Sales Order');
            }
        } catch (Exception $e) {
            session()->flash('failed', $e->getMessage());
        }
    }
}
