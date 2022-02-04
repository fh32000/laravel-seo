<?php

namespace RalphJSmit\Laravel\SEO;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TagManager implements Renderable
{
    public Model $model;
    public TagCollection $tags;

    public function __construct()
    {
        $this->tags = TagCollection::initialize();
    }

    public function for(Model $model): static
    {
        $this->model = $model;

        // The tags collection is already initialized when constructing the manager. Here, we'll
        // initialize the collection again, but this time we pass the model to the initializer.
        // The initializes will pass the generated SEOData to all underlying initializers, ensuring that
        // the tags are always fully up-to-date and no remnants from previous initializations are present.
        $this->tags = TagCollection::initialize($model->seo->prepareForUsage());

        return $this;
    }

    public function render(): string
    {
        return $this->tags->reduce(function (string $carry, Renderable $item) {
            return $carry .= Str::of($item->render())->trim()->trim(PHP_EOL);
        }, '');
    }

    public function __toString(): string
    {
        return $this->render();
    }
}