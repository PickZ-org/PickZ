<!-- BEGIN: Left Aside -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ url('/') }}/img/logo_small.png" alt="PickZ logo" class="brand-image float-none">
    </a>
    <div class="sidebar">
        <nav class="mt-3">
            <ul class="nav nav-pills nav-sidebar flex-column nav-child-indent" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link @if(Request::is('/')) active @endif">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-header">Operations</li>
                @if( Auth::user()->hasRole(['admin', 'manager']) )
                    <li class="nav-item @if(Request::is('orders*')) menu-open @endif">
                        <a href="#" class="nav-link @if(Request::is('orders*')) active @endif">
                            <i class="nav-icon fas fa-truck"></i>
                            <p>
                                Orders
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/') }}/orders/inbound"
                                   class="nav-link @if(Request::is('orders/inbound')) active @endif">
                                    <i class="fas fa-download nav-icon"></i>
                                    <p>Inbound</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/') }}/orders/outbound"
                                   class="nav-link @if(Request::is('orders/outbound')) active @endif">
                                    <i class="fas fa-upload nav-icon"></i>
                                    <p>Outbound</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/') }}/orders/archive"
                                   class="nav-link @if(Request::is('orders/archive')) active @endif">
                                    <i class="fas fa-archive nav-icon"></i>
                                    <p>Archive</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                <li class="nav-item">
                    <a href="{{ url('/') }}/stock" class="nav-link @if(Request::is('stock')) active @endif">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>Stock</p>
                    </a>
                </li>
                @if( Auth::user()->hasRole(['admin', 'manager']) )
                    <li class="nav-item @if(Request::is('tasklines*') || Request::is('tasks*')) menu-open @endif">
                        <a href="#"
                           class="nav-link @if(Request::is('tasklines*') || Request::is('tasks*')) active @endif">
                            <i class="nav-icon fas fa-tasks"></i>
                            <p>
                                Tasks
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/') }}/tasklines/putaway"
                                   class="nav-link @if(Request::is('tasklines/putaway')) active @endif">
                                    <i class="fa fa-dolly-flatbed nav-icon"></i>
                                    <p>Putaway</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/') }}/tasklines/crossdock"
                                   class="nav-link @if(Request::is('tasklines/crossdock')) active @endif">
                                    <i class="fas fa-arrows-alt-h nav-icon"></i>
                                    <p>Crossdock</p>
                                </a>
                            </li>
                            @if( ! \Configuration::get('pick_from_bulk', false)  )
                                <li class="nav-item">
                                    <a href="{{ url('/') }}/tasklines/replenishment"
                                       class="nav-link @if(Request::is('tasklines/replenishment')) active @endif">
                                        <i class="fa fa-sync-alt nav-icon"></i>
                                        <p>Replenishment</p>
                                    </a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a href="{{ url('/') }}/tasks/picking"
                                   class="nav-link @if(Request::is('tasks/picking')) active @endif">
                                    <i class="fas fa-hands nav-icon"></i>
                                    <p>Picking</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/') }}/tasks/shipping"
                                   class="nav-link @if(Request::is('tasks/shipping')) active @endif">
                                    <i class="fas fa-shipping-fast nav-icon"></i>
                                    <p>Shipping</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url('/') }}/tasklines/move"
                                   class="nav-link @if(Request::is('tasklines/move')) active @endif">
                                    <i class="fas fa-people-carry nav-icon"></i>
                                    <p>Stock moves</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                @if( Auth::user()->hasRole(['admin', 'manager']) )
                    <li class="nav-header">Administration</li>
                    <li class="nav-item">
                        <a href="{{ url('/') }}/contacts" class="nav-link @if(Request::is('contacts')) active @endif">
                            <i class="nav-icon far fa-address-book"></i>
                            <p>Contacts</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/') }}/products" class="nav-link @if(Request::is('products')) active @endif">
                            <i class="nav-icon fas fa-box-open"></i>
                            <p>Products</p>
                        </a>
                    </li>
                    @if(\Configuration::get('invoicing', false))
                        <li class="nav-item">
                            <a href="{{ url('/') }}/invoices"
                               class="nav-link @if(Request::is('invoices')) active @endif">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Invoices</p>
                            </a>
                        </li>
                    @endif
                    <li class="nav-header">System</li>
                    <li class="nav-item">
                        <a href="{{ url('/') }}/configuration"
                           class="nav-link @if(Request::is('configuration')) active @endif">
                            <i class="nav-icon fas fa-cogs"></i>
                            <p>Configuration</p>
                        </a>
                    </li>
                    @if( Auth::user()->hasRole('admin') )
                        <li class="nav-item">
                            <a href="{{ url('/') }}/users" class="nav-link @if(Request::is('users')) active @endif">
                                <i class="nav-icon fas fa-users-cog"></i>
                                <p>Users</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ url('/') }}/locations"
                               class="nav-link @if(Request::is('locations')) active @endif">
                                <i class="nav-icon fas fa-sitemap"></i>
                                <p>Locations</p>
                            </a>
                        </li>
                    @endif
                    <li class="nav-item">
                        <a href="{{ url('/') }}/logs" class="nav-link @if(Request::is('logs')) active @endif">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p>Logs</p>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</aside>
<!-- END: Left Aside -->
