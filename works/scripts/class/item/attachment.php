<?php
class Attachment extends Item {
    public string $parent_item;

    function __construct(
        string $cache_path,
        string $key
    ) {
        $this->key = $key;
        $item_array = json_decode(file_get_contents("$cache_path/items/$key.txt"), true);

        $this->title = $item_array["data"]["title"];
        $this->parent_item = $item_array["data"]["parentItem"];

        $link_mode = $item_array["data"]["linkMode"];
        if ($link_mode == "linked_url") $this->url = $item_array["data"]["url"];
        else if ($link_mode == "imported_file") $this->url = "{$item_array["links"]["alternate"]["href"]}/reader";
    }
    
    function __tostring(): string {
        if (empty($this->url)) return $this->title;
        return "<a href='{$this->url}'>{$this->title}</a>";
    }
}
?>