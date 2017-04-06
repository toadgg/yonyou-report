<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="renderer" content="webkit" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset("css/main.css") }}" rel="stylesheet">
</head>

<body class="login">
<div>
    <div class="login_wrapper">
        <div class="animate form login_form">
            <section class="login_content">
                <form method="post" action="{{ url('/login') }}">
                    {!! csrf_field() !!}
                    
                    <h1>登录</h1>
                    <div class="form-group has-feedback {{ $errors->has('username') ? ' has-error' : '' }}">
                        <input type="text" class="form-control" name="username" value="{{ old('username') }}" placeholder="用户名">
                        <span class="fa  fa-user form-control-feedback"></span>
                        @if ($errors->has('username'))
                            <span class="help-block">
                      <strong>{{ $errors->first('username') }}</strong>
                </span>
                        @endif
                    </div>
                    
                    <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                        <input type="password" class="form-control" placeholder="密码" name="password">
                        <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                        @if ($errors->has('password'))
                            <span class="help-block">
                  <strong>{{ $errors->first('password') }}</strong>
                </span>
                        @endif
                    
                    </div>
                    <div>
                        <input type="submit" class="btn btn-default submit" value="登录">
                        {{--<a class="reset_pass" href="{{  url('/password/reset') }}">Lost your password?</a>--}}
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <div class="separator">

                        
                        <div>
                            <h1><i class="fa fa-paw"></i> 天一控股信息查询平台!</h1>
                            <p>©1996-2017 天一控股 tianyi holdings All Rights Reserved</p>
                        </div>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
</body>
</html>