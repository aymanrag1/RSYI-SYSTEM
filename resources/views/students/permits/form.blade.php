@extends('layouts.app')
@section('title', $type === 'exit' ? 'تصريح خروج جديد' : 'تصريح مبيت جديد')

@section('content')
<div class="page-header">
  <h4>
    <i class="fa fa-{{ $type === 'exit' ? 'sign-out' : 'moon-o' }} ml-2 text-info"></i>
    {{ $type === 'exit' ? 'تصريح خروج جديد' : 'تصريح مبيت جديد' }}
  </h4>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
    <li class="breadcrumb-item">
      @if($type === 'exit')
        <a href="{{ route('students.exit-permits.index') }}">تصاريح الخروج</a>
      @else
        <a href="{{ route('students.overnight-permits.index') }}">تصاريح المبيت</a>
      @endif
    </li>
    <li class="breadcrumb-item active">جديد</li>
  </ol>
</div>
<div class="px-4">
  <div class="card rsyi-card" style="max-width:650px;">
    <div class="card-body">
      <form method="POST" action="{{ $type === 'exit' ? route('students.exit-permits.store') : route('students.overnight-permits.store') }}">
        @csrf
        <div class="row">
          <div class="col-md-12 mb-3">
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

          @if($type === 'exit')
            <div class="col-md-6 mb-3">
              <label>تاريخ ووقت الخروج <span class="text-danger">*</span></label>
              <input type="datetime-local" name="exit_date" class="form-control @error('exit_date') is-invalid @enderror"
                     value="{{ old('exit_date') }}" required>
              @error('exit_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label>وقت العودة المتوقع <span class="text-danger">*</span></label>
              <input type="time" name="return_time" class="form-control @error('return_time') is-invalid @enderror"
                     value="{{ old('return_time') }}" required>
              @error('return_time') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          @else
            <div class="col-md-4 mb-3">
              <label>من تاريخ <span class="text-danger">*</span></label>
              <input type="date" name="from_date" class="form-control @error('from_date') is-invalid @enderror"
                     value="{{ old('from_date') }}" required>
              @error('from_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4 mb-3">
              <label>إلى تاريخ <span class="text-danger">*</span></label>
              <input type="date" name="to_date" class="form-control @error('to_date') is-invalid @enderror"
                     value="{{ old('to_date') }}" required>
              @error('to_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-4 mb-3">
              <label>الوجهة <span class="text-danger">*</span></label>
              <input type="text" name="destination" class="form-control @error('destination') is-invalid @enderror"
                     value="{{ old('destination') }}" placeholder="مثال: القاهرة" required>
              @error('destination') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
          @endif

          <div class="col-md-12 mb-3">
            <label>سبب / ملاحظات</label>
            <textarea name="reason" class="form-control" rows="3">{{ old('reason') }}</textarea>
          </div>
        </div>
        <hr>
        <div class="d-flex justify-content-end" style="gap:8px;">
          @if($type === 'exit')
            <a href="{{ route('students.exit-permits.index') }}" class="btn btn-secondary">إلغاء</a>
          @else
            <a href="{{ route('students.overnight-permits.index') }}" class="btn btn-secondary">إلغاء</a>
          @endif
          <button type="submit" class="btn btn-info text-white"><i class="fa fa-paper-plane ml-1"></i>تقديم الطلب</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
