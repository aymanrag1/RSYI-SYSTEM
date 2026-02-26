<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>تسجيل الدخول — RSYI</title>
  <link rel="stylesheet" href="https://cdn.rtlcss.com/bootstrap/v4.2.1/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <style>
    body { background: linear-gradient(135deg, #1a2236 0%, #2d4a8a 100%); min-height: 100vh; display: flex; align-items: center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .login-card { border: none; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,.4); overflow: hidden; }
    .login-header { background: #1a2236; padding: 32px; text-align: center; }
    .login-header .institute-name { color: #fff; font-size: 16px; font-weight: 700; margin-top: 12px; }
    .login-header .sub { color: rgba(255,255,255,.6); font-size: 13px; }
    .login-body { padding: 32px; background: #fff; }
    .login-body .form-control { border-radius: 8px; padding: 10px 14px; font-size: 14px; border: 1px solid #e2e8f0; }
    .login-body .form-control:focus { border-color: #007bff; box-shadow: 0 0 0 3px rgba(0,123,255,.15); }
    .login-body label { font-size: 13px; font-weight: 600; color: #4a5568; }
    .btn-login { background: #007bff; border: none; border-radius: 8px; padding: 11px; font-size: 15px; font-weight: 600; width: 100%; transition: background .2s; }
    .btn-login:hover { background: #0056b3; }
    .input-icon { position: relative; }
    .input-icon i { position: absolute; top: 50%; transform: translateY(-50%); right: 14px; color: #a0aec0; }
    .input-icon input { padding-right: 38px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5 col-sm-10">

        <div class="login-card">
          <div class="login-header">
            <i class="fa fa-institution fa-3x text-white-50"></i>
            <div class="institute-name">معهد البحر الأحمر للسياحة البحرية</div>
            <div class="sub">Red Sea Yacht Institute — RSYI</div>
          </div>

          <div class="login-body">
            <h5 class="mb-4 text-center font-weight-bold" style="color:#1a2236;">
              <i class="fa fa-sign-in ml-2 text-primary"></i>تسجيل الدخول
            </h5>

            @if($errors->any())
              <div class="alert alert-danger py-2">
                @foreach($errors->all() as $e)
                  <div><i class="fa fa-exclamation-circle ml-1"></i>{{ $e }}</div>
                @endforeach
              </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}">
              @csrf

              <div class="form-group">
                <label>اسم المستخدم أو البريد الإلكتروني</label>
                <div class="input-icon">
                  <i class="fa fa-user"></i>
                  <input type="text"
                         name="login"
                         class="form-control @error('login') is-invalid @enderror"
                         value="{{ old('login') }}"
                         placeholder="أدخل اسم المستخدم أو الإيميل"
                         autofocus>
                </div>
              </div>

              <div class="form-group">
                <label>كلمة المرور</label>
                <div class="input-icon">
                  <i class="fa fa-lock"></i>
                  <input type="password"
                         name="password"
                         class="form-control @error('password') is-invalid @enderror"
                         placeholder="أدخل كلمة المرور">
                </div>
              </div>

              <button type="submit" class="btn btn-primary btn-login text-white">
                <i class="fa fa-sign-in ml-2"></i>دخول
              </button>
            </form>

            <p class="text-center text-muted mt-4 mb-0" style="font-size:12px;">
              النظام الإداري الموحد &copy; {{ date('Y') }} RSYI
            </p>
          </div>
        </div>

      </div>
    </div>
  </div>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
