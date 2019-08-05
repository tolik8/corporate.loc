<?php

namespace Corp\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Corp\Http\Controllers\Controller;

use Auth;
use Arr;
use Menu; // lavary/laravel-menu

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
        $this->user = Auth::user();

        if (!$this->user) {
            //abort(403);
        }
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
            $menu->add('Статьи', ['route' => 'admin.articles.index']);
            $menu->add('Портфолио', ['route' => 'admin.articles.index']);
            $menu->add('Меню', ['route' => 'admin.articles.index']);
            $menu->add('Пользователи', ['route' => 'admin.articles.index']);
            $menu->add('Привилегии', ['route' => 'admin.articles.index']);
        });
    }

}
