<?php

namespace Jomo\Utils;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WPHelpers
{

    public static function getACF(string $slug)
    {
        $response = Http::content()->timeout(3)->get("/wp-json/wp/v2/pages", [
            "slug" => $slug,
            "acf_format" => "standard"
        ]);

        if (!$response->successful()) {
            return abort(404);
        }

        $data = $response->json();

        if (!is_array($data) || empty($data)) {
            return abort(404);
        }

        return Cache::tags(["content", config("content.tags.pages")])->remember("page_content_$slug", 3600,  function () use ($data) {
            return $data[0]["acf"];
        });
    }

    public static function getOptions(string $slug)
    {
        return Cache::tags(["content", config("content.tags.options")])->remember("options_$slug", 300,  function () use ($slug) {
            $response = Http::content()->timeout(3)->get("/wp-json/acf/v3/options/$slug");

            if (!$response->successful()) {
                return abort(404);
            }

            $data = $response->json();

            if (!is_array($data) || empty($data)) {
                return abort(404);
            }


            return $data["acf"];
        });
    }

    public static function getMenu(string $slug)
    {
        return Cache::tags(["content", config("content.tags.menus")])->remember("menu_$slug", 300,  function () use ($slug) {
            $response = Http::content()->timeout(3)->get("/wp-json/menus/v1/menus/$slug");

            if (!$response->successful()) {
                return abort(404);
            }

            $data = $response->json();

            if (!is_array($data) || empty($data)) {
                return abort(404);
            }

            $items = collect($data["items"]);

            return $items->map(function ($item) {
                $instance = collect($item);

                return [
                    "id" => $instance->get("ID"),
                    "label" => $instance->get("title") ?: $instance->get("name") ?: $instance->get("link_text"),
                    "url" => $instance->get("object") === "page" ? "/" . Str::replace("home", "", $instance->get("slug")) : $instance->get("url"),
                ];
            });
        });
    }
}
