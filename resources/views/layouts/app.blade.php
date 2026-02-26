<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'لوحة التحكم') — RSYI</title>

  <!-- Bootstrap 4 RTL -->
  <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.2.1/css/bootstrap.min.css">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Select2 RTL -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">

  <style>
    body        { font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif; background:#f4f6f9; }
    .sidebar    { background:#1a2236; min-height:100vh; width:240px; position:fixed; top:56px; right:0; overflow-y:auto; z-index:100; }
    .main-content { margin-right:240px; padding-top:56px; min-height:100vh; }
    .navbar     { height:56px; position:fixed; top:0; width:100%; z-index:200; }
    .sidebar .nav-link { color:#a0aec0; padding:9px 20px; border-radius:6px; margin:1px 8px; font-size:14px; transition:all .15s; }
    .sidebar .nav-link:hover,.sidebar .nav-link.active { background:#2d3a55; color:#fff; }
    .sidebar .nav-link i { width:20px; margin-left:10px; }
    .sidebar-heading { color:#4a5568; font-size:11px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; padding:14px 20px 5px; }
    /* Cards */
    .stat-card  { border:none; border-radius:10px; box-shadow:0 2px 10px rgba(0,0,0,.08); }
    .stat-icon  { font-size:38px; opacity:.2; }
    .stat-value { font-size:28px; font-weight:700; }
    .stat-label { font-size:13px; opacity:.85; }
    .rsyi-card  { border:none; border-radius:10px; box-shadow:0 2px 8px rgba(0,0,0,.06); margin-bottom:20px; }
    .rsyi-card .card-header { background:#fff; border-bottom:1px solid #e9ecef; font-weight:600; font-size:14px; padding:14px 18px; border-radius:10px 10px 0 0!important; }
    /* Tables */
    .rsyi-table thead th { background:#1a2236; color:#a0aec0; font-size:11px; font-weight:700; letter-spacing:.8px; border:none; padding:10px 14px; }
    .rsyi-table tbody tr:hover { background:#f0f4ff; }
    .rsyi-table td { vertical-align:middle; padding:10px 14px; border-color:#e9ecef; font-size:13px; }
    /* Filters */
    .filters-bar { background:#fff; padding:14px 18px; border-radius:8px; margin-bottom:16px; box-shadow:0 1px 4px rgba(0,0,0,.06); }
    .filters-bar label { font-size:12px; font-weight:600; color:#4a5568; }
    /* Status pills */
    .status-pill { display:inline-block; padding:3px 12px; border-radius:20px; font-size:11px; font-weight:600; }
    .status-active   { background:#d4edda; color:#155724; }
    .status-inactive { background:#e2e3e5; color:#383d41; }
    .status-pending  { background:#fff3cd; color:#856404; }
    .status-rejected { background:#f8d7da; color:#721c24; }
    /* Badges */
    .badge-sys { font-size:10px; padding:3px 8px; border-radius:20px; }
    .badge-hr  { background:#007bff; color:#fff; }
    .badge-wh  { background:#28a745; color:#fff; }
    .badge-sa  { background:#fd7e14; color:#fff; }
    /* Page header */
    .page-header { background:#fff; padding:16px 24px; margin-bottom:20px; border-bottom:1px solid #e9ecef; }
    .page-header h4 { margin:0; font-size:18px; font-weight:700; color:#1a2236; }
    .page-header .breadcrumb { margin:0; background:none; padding:0; font-size:12px; }
    @media(max-width:768px) { .sidebar{display:none;} .main-content{margin-right:0;} }
  </style>
  @stack('styles')
</head>
<body>

{{-- ── Navbar ── --}}
<nav class="navbar navbar-expand navbar-dark bg-primary px-3">
  <a class="navbar-brand font-weight-bold" href="{{ route('dashboard') }}">
    <i class="fa fa-institution ml-2"></i> RSYI
  </a>
  <span class="navbar-text text-white-50 d-none d-md-inline" style="font-size:13px;">
    {{ config('app.name') }}
  </span>
  <ul class="navbar-nav mr-auto">
    <li class="nav-item d-none d-md-block">
      <span class="nav-link text-white-50" id="rsyi-date" style="font-size:12px;"></span>
    </li>
  </ul>
  <ul class="navbar-nav">
    <li class="nav-item">
      <span class="nav-link text-white">
        <i class="fa fa-user-circle ml-1"></i>
        {{ session('user_name', 'مستخدم') }}
        <small class="text-white-50">({{ implode(', ', session('user_roles', [])) }})</small>
      </span>
    </li>
    <li class="nav-item">
      <form method="POST" action="{{ route('logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-link nav-link text-white py-0">
          <i class="fa fa-power-off"></i>
        </button>
      </form>
    </li>
  </ul>
</nav>

{{-- ── Sidebar ── --}}
<aside class="sidebar pt-2">

  <h6 class="sidebar-heading">الرئيسية</h6>
  <ul class="nav flex-column">
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
        <i class="fa fa-tachometer"></i> لوحة التحكم
      </a>
    </li>
  </ul>

  <h6 class="sidebar-heading"><i class="fa fa-briefcase ml-1"></i> الموارد البشرية</h6>
  <ul class="nav flex-column">
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('hr.employees.*') ? 'active' : '' }}" href="{{ route('hr.employees.index') }}">
        <i class="fa fa-users"></i> الموظفون
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('hr.departments.*') ? 'active' : '' }}" href="{{ route('hr.departments.index') }}">
        <i class="fa fa-sitemap"></i> الإدارات
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('hr.leaves.*') ? 'active' : '' }}" href="{{ route('hr.leaves.index') }}">
        <i class="fa fa-calendar-check-o"></i> الإجازات
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('hr.attendance.*') ? 'active' : '' }}" href="{{ route('hr.attendance.index') }}">
        <i class="fa fa-clock-o"></i> الحضور والغياب
      </a>
    </li>
  </ul>

  <h6 class="sidebar-heading"><i class="fa fa-archive ml-1"></i> المخازن</h6>
  <ul class="nav flex-column">
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('warehouse.products.*') ? 'active' : '' }}" href="{{ route('warehouse.products.index') }}">
        <i class="fa fa-cubes"></i> الأصناف
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('warehouse.add-orders.*') ? 'active' : '' }}" href="{{ route('warehouse.add-orders.index') }}">
        <i class="fa fa-plus-square"></i> أوامر الإضافة
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('warehouse.withdrawal-orders.*') ? 'active' : '' }}" href="{{ route('warehouse.withdrawal-orders.index') }}">
        <i class="fa fa-minus-square"></i> أوامر الصرف
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('warehouse.purchase-requests.*') ? 'active' : '' }}" href="{{ route('warehouse.purchase-requests.index') }}">
        <i class="fa fa-shopping-cart"></i> طلبات الشراء
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('warehouse.suppliers.*') ? 'active' : '' }}" href="{{ route('warehouse.suppliers.index') }}">
        <i class="fa fa-truck"></i> الموردون
      </a>
    </li>
  </ul>

  <h6 class="sidebar-heading"><i class="fa fa-graduation-cap ml-1"></i> شئون الطلاب</h6>
  <ul class="nav flex-column">
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('students.index') || request()->routeIs('students.show') || request()->routeIs('students.edit') || request()->routeIs('students.create') ? 'active' : '' }}" href="{{ route('students.index') }}">
        <i class="fa fa-graduation-cap"></i> الطلاب
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('students.cohorts.*') ? 'active' : '' }}" href="{{ route('students.cohorts.index') }}">
        <i class="fa fa-layer-group"></i> الدفعات
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('students.exit-permits.*') ? 'active' : '' }}" href="{{ route('students.exit-permits.index') }}">
        <i class="fa fa-sign-out"></i> تصاريح الخروج
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('students.overnight-permits.*') ? 'active' : '' }}" href="{{ route('students.overnight-permits.index') }}">
        <i class="fa fa-moon-o"></i> تصاريح المبيت
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link {{ request()->routeIs('students.behavior.*') ? 'active' : '' }}" href="{{ route('students.behavior.index') }}">
        <i class="fa fa-exclamation-triangle"></i> المخالفات السلوكية
      </a>
    </li>
  </ul>

</aside>

{{-- ── Main Content ── --}}
<div class="main-content">

  {{-- Flash messages --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible mx-3 mt-3">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <i class="fa fa-check-circle ml-2"></i> {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible mx-3 mt-3">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <i class="fa fa-exclamation-circle ml-2"></i> {{ session('error') }}
    </div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger alert-dismissible mx-3 mt-3">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <ul class="mb-0">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @yield('content')
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
<script>
  // Date display
  document.getElementById('rsyi-date').textContent =
    new Date().toLocaleDateString('ar-EG', {weekday:'long', year:'numeric', month:'long', day:'numeric'});

  // Select2
  $(document).ready(function() {
    $('.rsyi-select2').select2({ dir:'rtl', width:'100%' });
  });

  // Confirm delete
  $(document).on('click','.confirm-delete', function(e) {
    if (!confirm('هل أنت متأكد من الحذف؟ لا يمكن التراجع.')) e.preventDefault();
  });
</script>
@stack('scripts')
</body>
</html>
