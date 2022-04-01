<?php

namespace App\Http\Controllers;

session_start();

use Illuminate\Http\Request;

use App\Helpers\Mysql;
use App\Models\Article;

class HomeController extends Controller
{
  public function home(Request $request)
  {
    $articles = Article::orderBy('created_at', 'DESC')->simplePaginate(10);

    return view('welcome', ['articles' => $articles]);
  }

  public function article(Request $request, $article)
  {
    $token = md5(uniqid());
    $_SESSION['token'] = $token;

    $article = \App\Models\Article::find($article);
    return view('article', ['article' => $article, 'token' => $token]);
  }

  public function search(Request $request)
  {
      if ($request->token == $_SESSION['token']) {
          $mysql = new Mysql;

          $articles = $mysql->like('articles', '*', ['title' => $request->search]);

          if(!$articles) $articles = [];
      }

    return view('search', [
      'articles' => $articles,
      'search' => htmlspecialchars($request->search)
    ]);
  }

  public function addComment(Request $request)
  {
    if ($request->token == $_SESSION['token']) {
        $mysql = new Mysql;

        $mysql->insert('comments', [
            'author' => htmlspecialchars($request->author),
            'message' => htmlspecialchars($request->message),
            'article_id' => htmlspecialchars($request->article_id),
        ]);
    }

    return redirect()->route('home.article', $request->article_id);
  }
}
