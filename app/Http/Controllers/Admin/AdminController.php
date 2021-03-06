<?php

namespace Corp\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Corp\Http\Controllers\Controller;

use Auth;
use Arr;
use Menu; // lavary/laravel-menu
use Gate;

class AdminController extends \Corp\Http\Controllers\Controller
{
    //

    protected $p_rep;
    protected $a_rep;
    protected $user;
    protected $template;
    protected $content = false;
    protected $title;
    protected $vars;

    public function __construct() {

        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();

            if (!$this->user) {
                abort(403);
            }

            return $next($request);
        });


    }

    public function renderOutput() {
        $this->vars = Arr::add($this->vars, 'title', $this->title);

        $menu = $this->getMenu();

        $navigation = view(env('THEME').'.admin.navigation')->with('menu', $menu)->render();
        $this->vars = Arr::add($this->vars, 'navigation', $navigation);

        if ($this->content) {
            $this->vars = Arr::add($this->vars, 'content', $this->content);
        }

        $footer = view(env('THEME').'.admin.footer')->render();
        $this->vars = Arr::add($this->vars, 'footer', $footer);

        return view($this->template)->with($this->vars);
    }

    public function getMenu()
    {
        return Menu::make('adminMenu', function($menu) {

            if (Gate::allows('VIEW_ADMIN_ARTICLES')) {
                $menu->add('Статьи', ['route' => 'admin.articles.index']);
            }

            $menu->add('Портфолио', ['route' => 'admin.articles.index']);
            $menu->add('Меню', ['route' => 'admin.menus.index']);
            $menu->add('Пользователи', ['route' => 'admin.users.index']);
            $menu->add('Привилегии', ['route' => 'admin.permissions.index']);
        });
    }

}
