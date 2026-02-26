@extends('layouts.app')
@section('title', 'طلب إجازة جديد')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-calendar ml-2 text-warning"></i>طلب إجازة جديد</h4>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
      <li class="breadcrumb-item"><a href="{{ route('hr.leaves.index') }}">الإجازات</a></li>
      <li class="breadcrumb-item active">طلب جديد</li>
    </ol>
  </div>
</div>

<div class="px-4">
  <div class="card rsyi-card">
    <div class="card-header"><i class="fa fa-plus ml-2"></i>تفاصيل طلب الإجازة</div>
    <div class="card-body">
      <form method="POST" action="{{ route('hr.leaves.store') }}">
        @csrf

        <div class="row">

          <div class="col-md-6 mb-3">
            <label>الموظف <span class="text-danger">*</span></label>
            <select name="employee_id" class="form-control rsyi-select2 @error('employee_id') is-invalid @enderror" required>
              <option value="">— اختر الموظف —</option>
              @foreach($employees as $emp)
                <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
                  {{ $emp->full_name }} ({{ $emp->emp_number }})
                </option>
              @endforeach
            </select>
            @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6 mb-3">
            <label>نوع الإجازة <span class="text-danger">*</span></label>
            <select name="leave_type" class="form-control @error('leave_type') is-invalid @enderror" required>
              <option value="">— اختر النوع —</option>
              <option value="annual"    {{ old('leave_type') === 'annual'    ? 'selected' : '' }}>سنوية</option>
              <option value="sick"      {{ old('leave_type') === 'sick'      ? 'selected' : '' }}>مرضية</option>
              <option value="emergency" {{ old('leave_type') === 'emergency' ? 'selected' : '' }}>طارئة</option>
              <option value="unpaid"    {{ old('leave_type') === 'unpaid'    ? 'selected' : '' }}>بدون راتب</option>
            </select>
            @error('leave_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-4 mb-3">
            <label>تاريخ البدء <span class="text-danger">*</span></label>
            <input type="date" name="start_date" id="start_date"
                   class="form-control @error('start_date') is-invalid @enderror"
                   value="{{ old('start_date') }}" required>
            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-4 mb-3">
            <label>تاريخ الانتهاء <span class="text-danger">*</span></label>
            <input type="date" name="end_date" id="end_date"
                   class="form-control @error('end_date') is-invalid @enderror"
                   value="{{ old('end_date') }}" required>
            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-4 mb-3">
            <label>عدد الأيام <span class="text-danger">*</span></label>
            <input type="number" name="days" id="days_count"
                   class="form-control @error('days') is-invalid @enderror"
                   value="{{ old('days') }}" min="1" required>
            @error('days') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-12 mb-3">
            <label>سبب الإجازة</label>
            <textarea name="reason" class="form-control @error('reason') is-invalid @enderror"
                      rows="3" placeholder="اكتب سبب الإجازة...">{{ old('reason') }}</textarea>
            @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

        </div>

        <hr>
        <div class="d-flex justify-content-end" style="gap:8px;">
          <a href="{{ route('hr.leaves.index') }}" class="btn btn-secondary">إلغاء</a>
          <button type="submit" class="btn btn-primary">
            <i class="fa fa-paper-plane ml-1"></i>تقديم الطلب
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
// Auto-calculate days between dates
function calcDays() {
  var s = document.getElementById('start_date').value;
  var e = document.getElementById('end_date').value;
  if (s && e) {
    var diff = (new Date(e) - new Date(s)) / 86400000 + 1;
    if (diff > 0) document.getElementById('days_count').value = diff;
  }
}
document.getElementById('start_date').addEventListener('change', calcDays);
document.getElementById('end_date').addEventListener('change', calcDays);
</script>
@endpush
@endsection
