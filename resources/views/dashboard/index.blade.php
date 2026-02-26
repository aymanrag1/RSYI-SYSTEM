@extends('layouts.app')
@section('title', 'لوحة التحكم الموحدة')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-tachometer ml-2 text-primary"></i>لوحة التحكم الموحدة</h4>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb"><li class="breadcrumb-item active">الرئيسية</li></ol>
    </nav>
  </div>
</div>
<div class="px-4">

  {{-- ── HR KPIs ── --}}
  <p class="mb-2" style="font-size:13px;font-weight:700;color:#1a2236;border-bottom:2px solid #e2e8f0;padding-bottom:6px;">
    <i class="fa fa-briefcase ml-2 text-primary"></i>نظام الموارد البشرية
    <span class="badge badge-primary badge-sys float-left">HR</span>
  </p>
  <div class="row mb-4">
    @php
    $hrCards = [
      ['bg-primary',  'fa-users',               'إجمالي الموظفين',   $kpi['hr']['employees'],  'hr.employees.index'],
      ['bg-success',  'fa-calendar-check-o',    'إجازات معلقة',      $kpi['hr']['leaves'],     'hr.leaves.index'],
      ['bg-warning',  'fa-clock-o',             'غائبون اليوم',      $kpi['hr']['absent'],     'hr.employees.index'],
      ['bg-danger',   'fa-exclamation-triangle','مخالفات الشهر',     $kpi['hr']['violations'], 'hr.employees.index'],
    ];
    @endphp
    @foreach($hrCards as [$bg, $icon, $label, $val, $route])
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card stat-card text-white {{ $bg }}">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="stat-label">{{ $label }}</div>
              <div class="stat-value">{{ $val ?? '—' }}</div>
            </div>
            <i class="fa {{ $icon }} stat-icon"></i>
          </div>
        </div>
        <a class="card-footer text-white-50 text-right" href="{{ route($route) }}" style="font-size:12px;display:block;padding:8px 16px;background:rgba(0,0,0,.1);text-decoration:none;">
          عرض التفاصيل &larr;
        </a>
      </div>
    </div>
    @endforeach
  </div>

  {{-- ── Warehouse KPIs ── --}}
  <p class="mb-2" style="font-size:13px;font-weight:700;color:#1a2236;border-bottom:2px solid #e2e8f0;padding-bottom:6px;">
    <i class="fa fa-archive ml-2 text-success"></i>نظام المخازن
    <span class="badge badge-success badge-sys float-left">WH</span>
  </p>
  <div class="row mb-4">
    @php
    $whCards = [
      ['#2ecc71', 'fa-cubes',        'إجمالي الأصناف',          $kpi['wh']['products'],   'warehouse.products.index'],
      ['#16a085', 'fa-shopping-cart','طلبات شراء معلقة',        $kpi['wh']['purchases'],  'warehouse.products.index'],
      ['#27ae60', 'fa-minus-circle', 'أوامر صرف اليوم',         $kpi['wh']['withdrawals'],'warehouse.products.index'],
      ['#1abc9c', 'fa-warning',      'أصناف منخفضة المخزون',    $kpi['wh']['low_stock'],  'warehouse.products.index'],
    ];
    @endphp
    @foreach($whCards as [$color, $icon, $label, $val, $route])
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card stat-card text-white" style="background:{{ $color }}">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="stat-label">{{ $label }}</div>
              <div class="stat-value">{{ $val ?? '—' }}</div>
            </div>
            <i class="fa {{ $icon }} stat-icon"></i>
          </div>
        </div>
        <a class="card-footer text-white-50 text-right" href="{{ route($route) }}" style="font-size:12px;display:block;padding:8px 16px;background:rgba(0,0,0,.1);text-decoration:none;">
          عرض التفاصيل &larr;
        </a>
      </div>
    </div>
    @endforeach
  </div>

  {{-- ── Student Affairs KPIs ── --}}
  <p class="mb-2" style="font-size:13px;font-weight:700;color:#1a2236;border-bottom:2px solid #e2e8f0;padding-bottom:6px;">
    <i class="fa fa-graduation-cap ml-2 text-warning"></i>شئون الطلاب
    <span class="badge badge-warning badge-sys float-left">SA</span>
  </p>
  <div class="row mb-4">
    @php
    $saCards = [
      ['#e67e22', 'fa-graduation-cap',     'إجمالي الطلاب',           $kpi['sa']['students'],  'students.index'],
      ['#d35400', 'fa-file-text-o',        'مستندات معلقة',           $kpi['sa']['documents'], 'students.index'],
      ['#e74c3c', 'fa-id-card-o',          'تصاريح معلقة',            $kpi['sa']['permits'],   'students.index'],
      ['#c0392b', 'fa-exclamation-circle', 'مخالفات سلوكية الشهر',   $kpi['sa']['behavior'],  'students.index'],
    ];
    @endphp
    @foreach($saCards as [$color, $icon, $label, $val, $route])
    <div class="col-lg-3 col-md-6 mb-3">
      <div class="card stat-card text-white" style="background:{{ $color }}">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <div class="stat-label">{{ $label }}</div>
              <div class="stat-value">{{ $val ?? '—' }}</div>
            </div>
            <i class="fa {{ $icon }} stat-icon"></i>
          </div>
        </div>
        <a class="card-footer text-white-50 text-right" href="{{ route($route) }}" style="font-size:12px;display:block;padding:8px 16px;background:rgba(0,0,0,.1);text-decoration:none;">
          عرض التفاصيل &larr;
        </a>
      </div>
    </div>
    @endforeach
  </div>

  {{-- ── Charts + Quick Actions ── --}}
  <div class="row">
    <div class="col-lg-5 mb-3">
      <div class="card rsyi-card h-100">
        <div class="card-header"><i class="fa fa-pie-chart ml-2"></i>توزيع الأنظمة</div>
        <div class="card-body d-flex align-items-center justify-content-center">
          <div style="position:relative;height:200px;width:100%;">
            <canvas id="doughnutChart"></canvas>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 mb-3">
      <div class="card rsyi-card h-100">
        <div class="card-header"><i class="fa fa-bolt ml-2 text-warning"></i>وصول سريع</div>
        <div class="card-body">
          <a href="{{ route('hr.employees.create') }}" class="btn btn-outline-primary btn-block mb-2 text-right">
            <i class="fa fa-user-plus ml-2"></i>إضافة موظف جديد
          </a>
          <a href="{{ route('hr.leaves.create') }}" class="btn btn-outline-success btn-block mb-2 text-right">
            <i class="fa fa-calendar-plus-o ml-2"></i>طلب إجازة
          </a>
          <a href="{{ route('warehouse.products.create') }}" class="btn btn-outline-success btn-block mb-2 text-right">
            <i class="fa fa-plus ml-2"></i>إضافة صنف مخزون
          </a>
          <a href="{{ route('students.create') }}" class="btn btn-outline-warning btn-block mb-2 text-right">
            <i class="fa fa-graduation-cap ml-2"></i>تسجيل طالب
          </a>
        </div>
      </div>
    </div>
    <div class="col-lg-3 mb-3">
      <div class="card rsyi-card h-100">
        <div class="card-header"><i class="fa fa-exclamation-circle ml-2 text-warning"></i>تنبيهات</div>
        <div class="card-body p-0">
          @if(($kpi['hr']['leaves'] ?? 0) > 0)
          <div class="p-3 border-bottom" style="border-right:3px solid #007bff!important;">
            <small><span class="badge badge-primary badge-sys ml-1">HR</span>
            {{ $kpi['hr']['leaves'] }} طلب إجازة معلق</small>
          </div>
          @endif
          @if(($kpi['wh']['low_stock'] ?? 0) > 0)
          <div class="p-3 border-bottom" style="border-right:3px solid #28a745!important;">
            <small><span class="badge badge-success badge-sys ml-1">WH</span>
            {{ $kpi['wh']['low_stock'] }} صنف منخفض المخزون</small>
          </div>
          @endif
          @if(($kpi['sa']['documents'] ?? 0) > 0)
          <div class="p-3" style="border-right:3px solid #fd7e14!important;">
            <small><span class="badge badge-warning badge-sys ml-1">SA</span>
            {{ $kpi['sa']['documents'] }} مستند بانتظار المراجعة</small>
          </div>
          @endif
        </div>
      </div>
    </div>
  </div>

</div>
@endsection

@push('scripts')
<script>
new Chart(document.getElementById('doughnutChart'), {
  type: 'doughnut',
  data: {
    labels: ['موظفون', 'طلاب', 'أصناف مخزون'],
    datasets: [{
      data: [
        {{ $kpi['hr']['employees'] ?? 0 }},
        {{ $kpi['sa']['students'] ?? 0 }},
        {{ $kpi['wh']['products'] ?? 0 }}
      ],
      backgroundColor: ['#007bff', '#fd7e14', '#28a745'],
      borderWidth: 2,
      borderColor: '#fff'
    }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    cutoutPercentage: 65,
    legend: { position: 'bottom', labels: { fontFamily: 'Segoe UI', fontSize: 12 } }
  }
});
</script>
@endpush
