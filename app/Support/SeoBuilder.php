<?php

namespace App\Support;

use App\Contracts\Seoble;
use Illuminate\Contracts\Support\Arrayable;

class SeoBuilder implements Arrayable
{
    private ?string $title = null;
    private ?string $description = null;
    private ?string $image = null;
    private ?string $url = null;
    private ?string $type = 'website';

    public function __construct(string|Seoble|array|null $data = null)
    {
        if ($data instanceof Seoble) {
            $this->fromSeoble($data);
        }

        if (is_array($data)) {
            $this->fromArray($data);
        }

        if (is_string($data)) {
            $this->title = $data;
        }
    }

    public function fromArray(array $data): self
    {
        if (isset($data['title'])) {
            $this->title = $data['title'];
        }

        if (isset($data['description'])) {
            $this->description = $data['description'];
        }

        if (isset($data['image'])) {
            $this->image = $data['image'];
        }

        if (isset($data['url'])) {
            $this->url = $data['url'];
        }

        if (isset($data['type'])) {
            $this->type = $data['type'];
        }

        return $this;
    }

    public function fromSeoble(Seoble $seoble): self
    {
        $this->title = $seoble->seo()->title;
        $this->description = $seoble->seo()->description;
        $this->image = $seoble->seo()->image;
        $this->url = $seoble->seo()->url;
        $this->type = $seoble->seo()->type;

        return $this;
    }

    public function title(string $title, bool $withSite = true): self
    {
        $this->title = $title;

        if ($withSite) {
            $this->title .= ' | ' . config('seo.site_name');
        }

        return $this;
    }

    public function description(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function image(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function url(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function type(?string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function toArray(): array
    {
        $title = $this->title ?? config('seo.title');
        $description = $this->description ?? config('seo.description');
        $image = $this->image ?? asset('favicon.svg');
        $url = $this->url ?? url()->current();

        return [
            'title' => $title,
            'description' => $description,
            'openGraph' => [
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'url' => $url,
                'type' => $this->type,
                'site_name' => config('seo.site'),
            ],
            'twitterCard' => [
                'card' => 'summary_large_image',
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'site' => config('seo.twitter_handle'),
            ],
        ];
    }
}