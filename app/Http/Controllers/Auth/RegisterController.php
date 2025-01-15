<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
  /**
   * 登録フォームを表示する
   *
   * @return \Illuminate\View\View
   */
  public function showRegistrationForm()
  {
    return view('auth.register');
  }

  /**
   * 新しいユーザーインスタンスを作成し、保存する
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function register(Request $request)
  {
    // 1. リクエストのデータを検証する
    $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
      'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    // 2. 新しいユーザーを作成する
    $user = User::create([
      'name' => $request->name,
      'email' => $request->email,
      'password' => Hash::make($request->password),
    ]);

    // 3. ユーザー登録イベントを発行する
    event(new Registered($user));

    // 4. ユーザーをログインさせる
    Auth::login($user);

    // 5. ダッシュボードページにリダイレクトする
    return redirect()->route('dashboard');
  }
}
