<?php

use Illuminate\Support\Facades\Route;

if (config('twill.enabled.users-management')) {
    Route::get('login', 'LoginController@showLoginForm')->name('login.form');
    Route::post('login', 'LoginController@login')->name('login');
    Route::post('logout', 'LoginController@logout')->name('logout');

    Route::get('password/reset', 'ForgotPasswordController@showLinkRequestForm')->name('password.reset.link');
    Route::post('password/email', 'ForgotPasswordController@sendResetLinkEmail')->name('password.reset.email');
    Route::get('password/reset/{token}', 'ResetPasswordController@showResetForm')->name('password.reset.form');
    Route::get('password/welcome/{token}', 'ResetPasswordController@showWelcomeForm')->name('password.reset.welcome.form');
    Route::post('password/reset', 'ResetPasswordController@reset')->name('password.reset');

    Route::get('users/impersonate/stop', 'ImpersonateController@stopImpersonate')->name('impersonate.stop');
    Route::get('users/impersonate/{id}', 'ImpersonateController@impersonate')->name('impersonate');
}

if (config('twill.enabled.users-2fa')) {
    Route::get('login-2fa', 'LoginController@showLogin2FaForm')->name('login-2fa.form');
    Route::post('login-2fa', 'LoginController@login2Fa')->name('login-2fa');
}

if (config('twill.enabled.users-oauth')) {
    Route::get('login/oauth/redirect/{provider}', 'LoginController@redirectToProvider')->name('login.redirect');
    Route::get('login/oauth/callback/{provider}', 'LoginController@handleProviderCallback')->name('login.callback');
    Route::get('login/oauth/oauth-link', 'LoginController@showPasswordForm')->name('login.oauth.showPasswordForm');
    Route::post('login/oauth/oauth-link', 'LoginController@linkProvider')->name('login.oauth.linkProvider');
}
