<?php
class Note extends Item {
    public string $parent_item = "";
    public string $note = "";
    
    function __construct(
        string $key,
        ?string $cache_path = "",
        ?int $version = 0,
        ?string $note = "",
        ?string $parent_item = ""
    ) {
        $this->key = $key;

        if (!empty($cache_path) and file_exists("$cache_path/items/notes/$key.json")) {
            $item_array = json_decode(file_get_contents("$cache_path/items/notes/$key.json"), true);

            if ($item_array["version"] != null) $this->version = $item_array["version"];
            else $this->version = 0;
            if (!empty($item_array["parent_item"])) $this->parent_item = $item_array["parent_item"];
            else $this->parent_item = "";
            if (!empty($item_array["note"])) $this->note = $item_array["note"];
        }

        if ($version != null) $this->version = $version;
        if (!empty($parent_item)) $this->parent_item = $parent_item;
        if (!empty($note)) $this->note = $note;
    }

    function __tostring(): string {
        if (empty($this->note)) return "";
        return "<tr><th scope='row'>Note</th><td>{$this->note}</td></tr>";
    }

    public function store(?string $cache_path) {
        if (empty($this->key) or empty($this->parent_item)) return;

        if (empty($cache_path)) {
            if (empty($this->cache_path)) return;
            $cache_path = $this->cache_path;
        }

        if (!file_exists("$cache_path")) mkdir("$cache_path");
        if (!file_exists("$cache_path/items")) mkdir("$cache_path/items");
        if (!file_exists("$cache_path/items/notes")) mkdir("$cache_path/items/notes");

        file_put_contents("$cache_path/items/notes/{$this->key}.json", json_encode($this));
        return "Stored item at: $cache_path/items/notes/{$this->key}.json";
    }
}
?>