@extends('layouts.app')
@section('title', 'الإدارات')

@section('content')
<div class="page-header d-flex justify-content-between align-items-center">
  <div>
    <h4><i class="fa fa-sitemap ml-2 text-primary"></i>الإدارات</h4>
    <ol class="breadcrumb"><li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">الإدارات</li></ol>
  </div>
  <a href="{{ route('hr.departments.create') }}" class="btn btn-primary">
    <i class="fa fa-plus ml-1"></i>إضافة إدارة
  </a>
</div>

<div class="px-4">
  <div class="card rsyi-card">
    <div class="card-body p-0">
      <table class="table rsyi-table mb-0">
        <thead><tr><th>#</th><th>اسم الإدارة</th><th>الوصف</th><th>عدد الموظفين</th><th>إجراءات</th></tr></thead>
        <tbody>
          @forelse($departments as $dep)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td class="font-weight-bold">{{ $dep->name }}</td>
            <td>{{ $dep->description ?? '—' }}</td>
            <td><span class="badge badge-primary">{{ $dep->employees_count }}</span></td>
            <td>
              <a href="{{ route('hr.departments.edit', $dep) }}" class="btn btn-sm btn-outline-warning">
                <i class="fa fa-edit"></i>
              </a>
              @if($dep->employees_count === 0)
              <form method="POST" action="{{ route('hr.departments.destroy', $dep) }}" class="d-inline"
                    onsubmit="return confirm('هل أنت متأكد من حذف هذه الإدارة؟')">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="fa fa-trash"></i></button>
              </form>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center text-muted py-4">لا توجد إدارات</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
