<?php

namespace Corp\Repositories;

use Corp\Article;
use Corp\Http\Requests\ArticleRequest;
use Gate;
use Image;
use Str;
use Config;

class ArticlesRepository extends Repository
{
    public function __construct(Article $articles)
    {
        $this->model = $articles;
    }

    public function one($alias, $attr = [])
    {
        $article = parent::one($alias, $attr);

        if ($article && !empty($attr)) {
            $article->load('comments');
            $article->comments->load('user');
        }

        return $article;
    }

    public function addArticle(ArticleRequest $request)
    {
        if (Gate::denies('save', $this->model)) {
            abort(403);
        }

        $data = $request->except('_token', 'image');

        if (empty($data)) {
            return ['error' => 'Нет данных'];
        }

        if (empty($data['alias'])) {
            $data['alias'] = $this->transliterate($data['title']);
        }

        if ($this->one($data['alias'], false)) {
            $request->merge(['alias' => $data['alias']]);
            $request->flash();

            return ['error' => 'Данный псевдоним уже используется'];
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            if ($image->isValid()) {
                $str = Str::random(8);

                $obj = new \stdClass;
                $obj->mini = $str . '_mini.jpg';
                $obj->max = $str . '_max.jpg';
                $obj->path = $str . '.jpg';

                $img = Image::make($image);

                $img->fit(Config::get('settings.image')['width'], Config::get('settings.image')['height'])
                    ->save(public_path().'/'.env('THEME') . '/images/articles/' . $obj->path);

                $img->fit(Config::get('settings.articles_img')['max']['width'], Config::get('articles_img.image')['max']['height'])
                    ->save(public_path().'/'.env('THEME') . '/images/articles/' . $obj->max);

                $img->fit(Config::get('settings.articles_img')['mini']['width'], Config::get('articles_img.image')['mini']['height'])
                    ->save(public_path().'/'.env('THEME') . '/images/articles/' . $obj->mini);

                $data['img'] = json_encode($obj);

                $this->model->fill($data);

                if ($request->user()->articles()->save($this->model)) {
                    return ['status' => 'Материал добавлен'];
                }
            }
        }
    }

}
