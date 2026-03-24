<?php
class Note extends Item {
    public string $parent_item;
    public string $note;
    
    function __construct(string $key, ?string $cache_path, ?int $version, ?string $note, ?string $parent_item) {
        $this->key = $key;

        if (!empty($cache_path) and file_exists("$cache_path/items/notes/$key.json")) {
            $item_array = json_decode(file_get_contents("$cache_path/items/notes/$key.json"), true);

            if (!empty($item_array["version"])) $this->version = $item_array["version"];
            if (!empty($item_array["parent_item"])) $this->parent_item = $item_array["parent_item"];
            if (!empty($item_array["note"])) $this->note = $item_array["note"];
        }

        if (!empty($version)) $this->version = $version;
        if (!empty($parent_item)) $this->parent_item = $parent_item;
        if (!empty($note)) $this->note = $note;
    }

    function __tostring(): string {
        return $this->note;
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