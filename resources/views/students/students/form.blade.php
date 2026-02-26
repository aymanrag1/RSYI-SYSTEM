@extends('layouts.app')
@section('title', isset($student) ? 'تعديل بيانات طالب' : 'تسجيل طالب جديد')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-graduation-cap ml-2 text-warning"></i>{{ isset($student) ? 'تعديل بيانات طالب' : 'تسجيل طالب جديد' }}</h4>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
      <li class="breadcrumb-item"><a href="{{ route('students.index') }}">الطلاب</a></li>
      <li class="breadcrumb-item active">{{ isset($student) ? 'تعديل' : 'تسجيل' }}</li>
    </ol>
  </div>
</div>

<div class="px-4">
  <div class="card rsyi-card">
    <div class="card-header"><i class="fa fa-user-plus ml-2"></i>{{ isset($student) ? 'تعديل: ' . $student->full_name : 'بيانات الطالب الجديد' }}</div>
    <div class="card-body">
      <form method="POST"
            action="{{ isset($student) ? route('students.update', $student) : route('students.store') }}">
        @csrf
        @if(isset($student)) @method('PUT') @endif

        <div class="row">

          {{-- رقم الملف (create only) --}}
          @unless(isset($student))
          <div class="col-md-4 mb-3">
            <label>رقم الملف <span class="text-danger">*</span></label>
            <input type="text" name="file_number" class="form-control @error('file_number') is-invalid @enderror"
                   value="{{ old('file_number') }}" placeholder="مثال: STD-2024-001" required>
            @error('file_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          @endunless

          {{-- الاسم الأول --}}
          <div class="col-md-4 mb-3">
            <label>الاسم الأول <span class="text-danger">*</span></label>
            <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                   value="{{ old('first_name', $student->first_name ?? '') }}" required>
            @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- الاسم الأخير --}}
          <div class="col-md-4 mb-3">
            <label>الاسم الأخير <span class="text-danger">*</span></label>
            <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                   value="{{ old('last_name', $student->last_name ?? '') }}" required>
            @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- الرقم الوطني / رقم القيد (create only) --}}
          @unless(isset($student))
          <div class="col-md-4 mb-3">
            <label>الرقم الوطني <span class="text-danger">*</span></label>
            <input type="text" name="national_id" class="form-control @error('national_id') is-invalid @enderror"
                   value="{{ old('national_id') }}" required>
            @error('national_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          @endunless

          {{-- تاريخ الميلاد (create only) --}}
          @unless(isset($student))
          <div class="col-md-4 mb-3">
            <label>تاريخ الميلاد <span class="text-danger">*</span></label>
            <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror"
                   value="{{ old('birth_date') }}" required>
            @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          @endunless

          {{-- الجنس (create only) --}}
          @unless(isset($student))
          <div class="col-md-4 mb-3">
            <label>الجنس <span class="text-danger">*</span></label>
            <select name="gender" class="form-control @error('gender') is-invalid @enderror" required>
              <option value="">— اختر —</option>
              <option value="male"   {{ old('gender') === 'male'   ? 'selected' : '' }}>ذكر</option>
              <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>أنثى</option>
            </select>
            @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          @endunless

          {{-- الدفعة --}}
          <div class="col-md-4 mb-3">
            <label>الدفعة <span class="text-danger">*</span></label>
            <select name="cohort_id" class="form-control rsyi-select2 @error('cohort_id') is-invalid @enderror" required>
              <option value="">— اختر الدفعة —</option>
              @foreach($cohorts as $cohort)
                <option value="{{ $cohort->id }}" {{ old('cohort_id', $student->cohort_id ?? '') == $cohort->id ? 'selected' : '' }}>
                  {{ $cohort->name }}
                </option>
              @endforeach
            </select>
            @error('cohort_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- تاريخ القيد (create only) --}}
          @unless(isset($student))
          <div class="col-md-4 mb-3">
            <label>تاريخ القيد <span class="text-danger">*</span></label>
            <input type="date" name="enrollment_date" class="form-control @error('enrollment_date') is-invalid @enderror"
                   value="{{ old('enrollment_date') }}" required>
            @error('enrollment_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          @endunless

          {{-- الهاتف --}}
          <div class="col-md-4 mb-3">
            <label>رقم الهاتف</label>
            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                   value="{{ old('phone', $student->phone ?? '') }}">
            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- البريد الإلكتروني --}}
          <div class="col-md-4 mb-3">
            <label>البريد الإلكتروني</label>
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $student->email ?? '') }}">
            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

          {{-- الحالة (edit only) --}}
          @if(isset($student))
          <div class="col-md-4 mb-3">
            <label>الحالة <span class="text-danger">*</span></label>
            <select name="status" class="form-control @error('status') is-invalid @enderror" required>
              <option value="active"    {{ old('status', $student->status) === 'active'    ? 'selected' : '' }}>نشط</option>
              <option value="suspended" {{ old('status', $student->status) === 'suspended' ? 'selected' : '' }}>موقوف</option>
              <option value="expelled"  {{ old('status', $student->status) === 'expelled'  ? 'selected' : '' }}>مفصول</option>
              <option value="graduated" {{ old('status', $student->status) === 'graduated' ? 'selected' : '' }}>خريج</option>
              <option value="withdrawn" {{ old('status', $student->status) === 'withdrawn' ? 'selected' : '' }}>منسحب</option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>
          @endif

          {{-- العنوان --}}
          <div class="col-md-12 mb-3">
            <label>العنوان</label>
            <textarea name="address" class="form-control @error('address') is-invalid @enderror"
                      rows="2">{{ old('address', $student->address ?? '') }}</textarea>
            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
          </div>

        </div>

        <hr>
        <div class="d-flex justify-content-end" style="gap:8px;">
          <a href="{{ route('students.index') }}" class="btn btn-secondary">إلغاء</a>
          <button type="submit" class="btn btn-warning text-white">
            <i class="fa fa-save ml-1"></i>{{ isset($student) ? 'حفظ التعديلات' : 'تسجيل الطالب' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
