<?php

namespace App\Http\Controllers;

session_start();

use Illuminate\Http\Request;

use App\Helpers\Mysql;
use App\Models\Article;

class AdminController extends Controller
{
  public function index(Request $request)
  {
    $token = md5(uniqid());
    $_SESSION['token'] = $token;

    $articles = Article::all();
    return view('admin.index', ['articles' => $articles, 'token' => $token]);
  }

  public function addArticle(Request $request)
  {
      if ($request->token == $_SESSION['token']) {
          $article = new Article;
          $article->content = $request->content;
          $article->title = $request->title;
          $article->save();
      }

      return redirect()->route('home');
  }
}
