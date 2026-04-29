<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item @if (Route::currentRouteName() == 'home') active @endif">
            <a class="nav-link" href="{{ route('home') }}">
                <i class="mdi mdi-home menu-icon"></i>
                <span class="menu-title">Home</span>
            </a>
        </li>
        @if (Illuminate\Support\Facades\Auth::user()->hasRole('admin'))
            <li class="nav-item @if (Route::currentRouteName() == 'dashboard.index') active @endif">
                <a class="nav-link" href="{{ route('dashboard.index') }}">
                    <i class="mdi mdi-chart-bar menu-icon"></i>
                    <span class="menu-title">Dashboard</span>
                </a>
            </li>
            <li class="nav-item @if (Route::currentRouteName() == 'sales-order.index') active @endif">
                <a class="nav-link" href="{{ route('sales-order.index') }}">
                    <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                    <span class="menu-title">Sales Order</span>
                </a>
            </li>
            {{-- <li class="nav-item @if (Route::currentRouteName() == 'stock-in.index' || Route::currentRouteName() == 'stock-out.index') active @endif">
                <a class="nav-link" data-toggle="collapse" href="#stock" aria-expanded="false" aria-controls="stock">
                    <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                    <span class="menu-title">Manajemen Stok</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse @if (Route::currentRouteName() == 'stock-in.index' || Route::currentRouteName() == 'stock-out.index') show @endif" id="stock">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link @if (Route::currentRouteName() == 'stock-in.index') active @endif"
                                href="{{ route('stock-in.index') }}">
                                Stok Masuk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if (Route::currentRouteName() == 'stock-out.index') active @endif"
                                href="{{ route('stock-out.index') }}">
                                Stok Keluar
                            </a>
                        </li>
                    </ul>
                </div>
            </li> --}}
            <li class="nav-item @if (Route::currentRouteName() == 'category-product.index' || Route::currentRouteName() == 'product.index') active @endif">
                <a class="nav-link" data-toggle="collapse" href="#product" aria-expanded="false"
                    aria-controls="product">
                    <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                    <span class="menu-title">Produk</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse @if (Route::currentRouteName() == 'category-product.index' || Route::currentRouteName() == 'product.index') show @endif" id="product">
                    <ul class="nav flex-column sub-menu">
                        <li class="nav-item">
                            <a class="nav-link @if (Route::currentRouteName() == 'product.index') active @endif"
                                href="{{ route('product.index') }}">
                                Produk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link @if (Route::currentRouteName() == 'category-product.index') active @endif"
                                href="{{ route('category-product.index') }}">
                                Kategori Produk
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item @if (Route::currentRouteName() == 'payment-method.index') active @endif">
                <a class="nav-link" href="{{ route('payment-method.index') }}">
                    <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                    <span class="menu-title">Metode Pembayaran</span>
                </a>
            </li>
            <li class="nav-item @if (Route::currentRouteName() == 'user.index') active @endif">
                <a class="nav-link" href="{{ route('user.index') }}">
                    <i class="mdi mdi-account menu-icon"></i>
                    <span class="menu-title">Users</span>
                </a>
            </li>
        @elseif (Illuminate\Support\Facades\Auth::user()->hasRole('cashier'))
            <li class="nav-item @if (Route::currentRouteName() == 'sales-order.index') active @endif">
                <a class="nav-link" href="{{ route('sales-order.index') }}">
                    <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                    <span class="menu-title">Sales Order</span>
                </a>
            </li>
            <li class="nav-item @if (Route::currentRouteName() == 'product.index') active @endif">
                <a class="nav-link" href="{{ route('product.index') }}">
                    <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                    <span class="menu-title">Product</span>
                </a>
            </li>
        @endif
    </ul>
</nav>
