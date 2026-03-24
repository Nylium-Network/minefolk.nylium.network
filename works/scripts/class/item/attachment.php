<?php
class Attachment extends Item {
    public string $parent_item;

    function __construct(
        string $key,
        ?int $version,
        ?string $cache_path,
        ?string $url,
        ?string $parent_item
    ) {
        $this->key = $key;

        if (!empty($cache_path) and file_exists("$cache_path/items/attachments/$key.json")) {
            $item_array = json_decode(file_get_contents("$cache_path/items/$key.json"), true);

            if (!empty($item_array["version"])) $this->url = $item_array["version"];
            if (!empty($item_array["title"])) $this->title = $item_array["title"];
            if (!empty($item_array["url"])) $this->url = $item_array["url"];
            if (!empty($item_array["parent_item"])) $this->parent_item = $item_array["parent_item"];
        }

        if (!empty($title)) $this->title = $title;
        if (!empty($url)) $this->url = $url;
        if (!empty($parent_item)) $this->parent_item = $parent_item;
        if (!empty($version)) $this->version = $version;
    }
    
    function __tostring(): string {
        if (empty($this->url)) return $this->title;
        return "<a href='{$this->url}'>{$this->title}</a>";
    }

    public function store(string $cache_path) {
        if (!file_exists("$cache_path")) mkdir("$cache_path");
        if (!file_exists("$cache_path/items")) mkdir("$cache_path/items");
        if (!file_exists("$cache_path/items/{$this->parent_item}")) mkdir("$cache_path/items/{$this->parent_item}");

        file_put_contents("$cache_path/items/{$this->parent_item}/{$this->key}.json", json_encode($this));
        return "Stored item at: $cache_path/items/{$this->parent_item}/{$this->key}.json";
    }
}
?>