<?php
class Item implements Stringable {
    public string $key;
    public ?string $title;
    public ?array $creators;
    public ?string $date;
    public ?string $url;
    public ?string $abstract;
    public ?string $member_of;
    public ?array $tags;
    public ?array $children;
    function __construct(string $key, ?string $cache_path, ?string $title, ?array $creators, ?string $date, ?string $abstract, ?string $member_of, ?array $tags, ?array $children) {
        $this->key = $key;

        if (!empty($cache_path)) {
            $item_array = json_decode(file_get_contents("$cache_path/item/$key"), true);

            $this->title = $item_array["data"]["title"];
            foreach ($item_array["data"]["creators"] as $creator){
                $this->creators[] = $creator;
            }
            $this->date = $item_array["meta"]["parsedDate"];
            $this->url = $item_array["data"]["url"];
            $this->abstract = $item_array["data"]["abstractNote"];
            switch ($item_array["data"]["itemType"]) {
                case "blogPost":
                    $this->member_of = $item_array["data"]["blogTitle"];
                    break;
                case "forumPost":
                    $this->member_of = $item_array["data"]["forumTitle"];
                    break;
                case "webpage":
                    $this->member_of = $item_array["data"]["websiteTitle"];
                    break;
                case "book":
                    $this->member_of = $item_array["data"]["series"];
                    break;
            }
            foreach ($item_array["data"]["tags"] as $tag) {
                $this->tags[] = $tag;
            }
        }

        if (!empty($title)) $this->title = $title;
        if (!empty($date)) $this->date = $date;

        if (true);
    }
    function __tostring(): string {
        if (!empty($this->title)) return $this->title;
        return "Item@{$this->key}";
    }
}
?>