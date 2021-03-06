<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;    // 追加

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
    // getでtasks/にアクセスされた場合の「一覧表示処理」
    public function index()
    {
        // タスク一覧をidの降順で取得
        //$tasks = Task::orderBy('id', 'desc')->paginate(10);


        $data = [];
        if (\Auth::check()) { // 認証済みの場合
            // 認証済みユーザを取得
            $user = \Auth::user();
            // ユーザのタスク一覧を作成日時の降順で取得
            $tasks = $user->tasks()->orderBy('created_at', 'desc')->paginate(10);

            $data = [
                'user' => $user,
                'tasks' => $tasks,
            ];
        }
        
        // task一覧ビューでそれを表示
        return view('tasks.index', $data);
    }




    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    // getでtasks/createにアクセスされた場合の「新規登録画面表示処理」
    public function create()
    {
        $task = new Task;

        // タスク作成ビューを表示
       return view('tasks.create', [
            'task' => $task,
        ]);
    }




    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // postでtasks/にアクセスされた場合の「新規登録処理」
    public function store(Request $request)
    {
        
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:255',
        ]);
        
        // taskを作成
        //$task = new Task;
        //$task->status = $request->status;    // 追加
        //$task->content = $request->content;
        //$task->save();

        
        // 認証済みユーザ（閲覧者）の投稿として作成（リクエストされた値をもとに作成）
        $request->user()->tasks()->create([
            'content' => $request->content,
            'status' => $request->status,
        ]);

        // 前のURLへリダイレクトさせる
        //return back();　前のURL＝tasks.create(タスク作成ページ)なので何も動作が内容に見えた。２手間なので/にリダイレクトする。
        
        // トップページへリダイレクトさせる
        return redirect('/');
    }




    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // getでtasks/idにアクセスされた場合の「取得表示処理」
    public function show($id)
    {
        // idの値でtaskを検索して取得
        $task = Task::findOrFail($id);
            
                    
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、
        if (\Auth::id() === $task->user_id) {
            
            // task詳細ビューでそれを表示
            return view('tasks.show', [
                'task' => $task,
            ]);
            
        }
        
        // トップページへリダイレクトさせる
        return redirect('/');
    }




    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // getでtasks/id/editにアクセスされた場合の「更新画面表示処理」
    public function edit($id)
    {
        // idの値でタスクを検索して取得
        $task = Task::findOrFail($id);

        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、
        if (\Auth::id() === $task->user_id) {
            
            // タスク編集ビューでそれを表示
            return view('tasks.edit', [
                'task' => $task,
            ]);
            
        }
        
        // トップページへリダイレクトさせる
        return redirect('/');
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // putまたはpatchでtasks/idにアクセスされた場合の「更新処理」
    public function update(Request $request, $id)
    {
        
        // バリデーション
        $request->validate([
            'status' => 'required|max:10',   // 追加
            'content' => 'required|max:255',
        ]);
        
            
            // idの値でtaskを検索して取得
            $task = Task::findOrFail($id);
            
        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、
        if (\Auth::id() === $task->user_id) {
            
            // taskを更新
            $task->status = $request->status;    // 追加
            $task->content = $request->content;
            $task->save();
                
        }

        // トップページへリダイレクトさせる
        return redirect('/');
    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // deleteでtasks/idにアクセスされた場合の「削除処理」
    public function destroy($id)
    {
        // idの値でtaskを検索して取得
        $task = Task::findOrFail($id);
        // taskを削除
        //$task->delete();

        

        // 認証済みユーザ（閲覧者）がその投稿の所有者である場合は、投稿を削除
        if (\Auth::id() === $task->user_id) {
            $task->delete();
        }

        // 前のURLへリダイレクトさせる
        //return back();　前のURL＝削除したタスク詳細ページなのでページが見つからずエラーになる。
        
        // トップページへリダイレクトさせる
        return redirect('/');
    }
}
