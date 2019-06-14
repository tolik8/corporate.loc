<?php

namespace Corp\Http\Controllers;

use Illuminate\Http\Request;

use Corp\Repositories\MenusRepository;
use Menu;
use Arr;

class SiteController extends Controller
{
    protected $p_rep;
    protected $s_rep;
    protected $a_rep;
    protected $m_rep;
    protected $keywords;
    protected $meta_desc;
    protected $title;
    protected $template;
    protected $vars = [];
    protected $contentRightBar = false;
    protected $contentLeftBar = false;
    protected $bar = 'no';

    public function __construct(MenusRepository $m_rep)
    {
        $this->m_rep = $m_rep;
    }

    protected function renderOutput()
    {
        $menu = $this->getMenu();

        $navigation = view(env('THEME') . '.navigation')->with('menu', $menu)->render();
        $this->vars = Arr::add($this->vars, 'navigation', $navigation);

        if ($this->contentRightBar) {
            $rightBar = view(env('THEME').'.rightBar')->with('contentRightBar', $this->contentRightBar)->render();
            $this->vars = Arr::add($this->vars, 'rightBar', $rightBar);
        }
        $this->vars = Arr::add($this->vars, 'bar', $this->bar);

        $this->vars = Arr::add($this->vars, 'keywords', $this->keywords);
        $this->vars = Arr::add($this->vars, 'meta_desc', $this->meta_desc);
        $this->vars = Arr::add($this->vars, 'title', $this->title);

        $footer = view(env('THEME') . '.footer')->render();
        $this->vars = Arr::add($this->vars, 'footer', $footer);

        return view($this->template)->with($this->vars);
    }

    protected function getMenu()
    {
        $menu = $this->m_rep->get();

        $mBuilder = Menu::make('MyNav', function($m) use ($menu) {
            foreach ($menu as $item) {
                if ($item->parent === 0) {
                    $m->add($item->title, $item->path)->id($item->id);
                } else {
                    if ($m->find($item->parent)) {
                        $m->find($item->parent)->add($item->title, $item->path)->id($item->id);
                    }
                }
            }
        });

        return $mBuilder;
    }

}
