@extends('layouts.app')
@section('title', 'تسجيل مخالفة سلوكية')

@section('content')
<div class="page-header">
  <h4><i class="fa fa-exclamation-triangle ml-2 text-danger"></i>تسجيل مخالفة سلوكية</h4>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.behavior.index') }}">المخالفات السلوكية</a></li>
    <li class="breadcrumb-item active">تسجيل</li>
  </ol>
</div>
<div class="px-4">
  <div class="card rsyi-card" style="max-width:700px;">
    <div class="card-body">
      <form method="POST" action="{{ route('students.behavior.store') }}">
        @csrf
        <div class="row">
          <div class="col-md-6 mb-3">
            <label>الطالب <span class="text-danger">*</span></label>
            <select name="student_id" class="form-control rsyi-select2 @error('student_id') is-invalid @enderror" required>
              <option value="">— اختر الطالب —</option>
              @foreach($students as $std)
                <option value="{{ $std->id }}" {{ old('student_id') == $std->id ? 'selected' : '' }}>
                  {{ $std->full_name }} ({{ $std->file_number }})
                </option>
              @endforeach
            </select>
            @error('student_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-6 mb-3">
            <label>نوع المخالفة <span class="text-danger">*</span></label>
            <input type="text" name="violation_type" class="form-control @error('violation_type') is-invalid @enderror"
                   value="{{ old('violation_type') }}" placeholder="مثال: غياب، تأخر، سلوك..." required>
            @error('violation_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label>تاريخ المخالفة <span class="text-danger">*</span></label>
            <input type="date" name="violation_date" class="form-control @error('violation_date') is-invalid @enderror"
                   value="{{ old('violation_date', date('Y-m-d')) }}" required>
            @error('violation_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-4 mb-3">
            <label>النقاط المخصومة</label>
            <input type="number" name="points" class="form-control @error('points') is-invalid @enderror"
                   value="{{ old('points', 0) }}" min="0">
          </div>
          <div class="col-md-12 mb-3">
            <label>وصف المخالفة <span class="text-danger">*</span></label>
            <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                      rows="3" required>{{ old('description') }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          <div class="col-md-12 mb-3">
            <label>الإجراء المتخذ</label>
            <textarea name="action_taken" class="form-control" rows="2" placeholder="الإجراء التأديبي...">{{ old('action_taken') }}</textarea>
          </div>
        </div>
        <hr>
        <div class="d-flex justify-content-end" style="gap:8px;">
          <a href="{{ route('students.behavior.index') }}" class="btn btn-secondary">إلغاء</a>
          <button type="submit" class="btn btn-danger"><i class="fa fa-save ml-1"></i>تسجيل المخالفة</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
