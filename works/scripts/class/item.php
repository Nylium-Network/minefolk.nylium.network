<?php
class Item implements Stringable {
    public string $key;
    public string $type;
    public int $version;
    public ?string $title;
    public ?string $creators;
    public ?string $date;
    public ?string $url;
    public ?string $abstract;
    public ?string $member_of;
    public ?array $tags;
    public ?array $children;

    function __construct(string $key, ?string $type, ?int $version, ?string $cache_path, ?string $title, ?array $creators, ?string $date, ?string $url, ?string $abstract, ?string $member_of, ?array $tags, ?array $children) {
        $this->key = $key;

        if (!empty($cache_path) and file_exists("$cache_path/items/$key.json")) {
            $item_array = json_decode(file_get_contents("$cache_path/item/$key.json"), true);

            if (!empty($item_array["type"])) $this->type = $item_array["type"];
            if (!empty($item_array["version"])) $this->version = $item_array["version"];
            if (!empty($item_array["title"])) $this->title = $item_array["title"];
            if (!empty($item_array["creators"])) $this->creators = $item_array["creators"];
            if (!empty($item_array["date"])) $this->date = $item_array["date"];
            if (!empty($item_array["url"])) $this->url = $item_array["url"];
            if (!empty($item_array["abstract"])) $this->abstract = $item_array["abstract"];
            if (!empty($item_array["member_of"])) $this->member_of = $item_array["member_of"];
            if (!empty($item_array["tags"])) $this->tags = $item_array["tags"];
            if (!empty($item_array["children"])) $this->children = $item_array["children"];
        }

        if (!empty($type)) $this->type = $type;
        if (!empty(($version))) $this->version = $version;
        if (!empty($title)) $this->title = $title;
        if (!empty(($creators))) $this->creators = $creators;
        if (!empty($date)) $this->date = $date;
        if (!empty(($url))) $this->url = $url;
        if (!empty(($abstract))) $this->abstract = $abstract;
        if (!empty(($member_of))) $this->member_of = $member_of;
        if (!empty(($tags))) $this->tags = $tags;
        if (!empty(($children))) $this->children = $children;
    }
    function __tostring(): string {
        if (!empty($this->title)) return $this->title;
        return "Item@{$this->key}";
    }

    public function store(string $cache_path) {
        if (!file_exists("$cache_path")) mkdir("$cache_path");
        if (!file_exists("$cache_path/items")) mkdir("$cache_path/items");

        file_put_contents("$cache_path/items/{$this->key}.json", json_encode($this));
        return "Stored item at: $cache_path/items/{$this->key}.json";
    }
}
?>