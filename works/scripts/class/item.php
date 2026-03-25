<?php
class Item implements Stringable {
    public string $key = "";
    public string $type = "unknown";
    public int $version = 0;
    public string $cache_path = "";
    public ?string $title;
    public ?string $creators;
    public ?string $date;
    public ?string $url;
    public ?string $abstract;
    public ?string $member_of;
    public ?array $tags = array();
    public ?array $attachments = array();
    public ?array $notes = array();

    function __construct(
        string $key,
        ?string $type = "",
        ?int $version = 0,
        ?string $cache_path = "",
        ?string $title = "",
        ?string $creators = "",
        ?string $date = "",
        ?string $url = "",
        ?string $abstract = "",
        ?string $member_of = "",
        ?array $tags = array(),
        ?array $attachments = array(),
        ?array $notes = array()
    ) {
        $this->key = $key;

        if (!empty($cache_path) and file_exists("$cache_path/items/$key.json")) {
            $item_array = json_decode(file_get_contents("$cache_path/items/$key.json"), true);

            if (!empty($item_array["type"]))
                $this->type = $item_array["type"];

            if ($item_array["version"] != null)
                $this->version = $item_array["version"];

            if (!empty($item_array["cache_path"]))
                $this->cache_path = $item_array["cache_path"];

            if (!empty($item_array["title"]))
                $this->title = $item_array["title"];

            if (!empty($item_array["creators"]))
                $this->creators = $item_array["creators"];

            if (!empty($item_array["date"]))
                $this->date = $item_array["date"];

            if (!empty($item_array["url"]))
                $this->url = $item_array["url"];

            if (!empty($item_array["abstract"]))
                $this->abstract = $item_array["abstract"];

            if (!empty($item_array["member_of"]))
                $this->member_of = $item_array["member_of"];

            if (array_key_exists("tags", $item_array) and $item_array["tags"] != null and count($item_array["tags"]) > 0)
                $this->tags = $item_array["tags"];

            if ($item_array["attachments"] != null and count($item_array["attachments"]) > 0)
                $this->attachments = $item_array["attachments"];

            if ($item_array["notes"] != null and count($item_array["notes"]) > 0)
                $this->notes = $item_array["notes"];
        }

        if (!empty($type))
            $this->type = $type;

        if ($version != null)
            $this->version = $version;

        if (!empty($cache_path))
            $this->cache_path = $cache_path;

        if (!empty($title))
            $this->title = $title;

        if (!empty($creators))
            $this->creators = $creators;

        if (!empty($date))
            $this->date = $date;

        if (!empty($url))
            $this->url = $url;

        if (!empty($abstract))
            $this->abstract = $abstract;

        if (!empty($member_of))
            $this->member_of = $member_of;

        if ($tags != null and count($tags) > 0) 
            $this->tags = $tags;

        if ($attachments != null and count($attachments) > 0)
            $this->attachments = $attachments;

        if ($notes != null and count($notes) > 0)
            $this->notes = $notes;
    }
    function __tostring(): string {
        if (empty($this->key) or empty($this->title)) return "";

        $item_string = "<div class='work-summary'>";

        if(!empty($this->title)) $item_string .= "<h4>{$this->title}</h4>";
        $item_string .= "<table>";

        if(!empty($this->creators)) $item_string .= "<tr><th scope='row'>Creator(s)</th><td>{$this->creators}</td></tr>";
        if(!empty($this->date)) $item_string .= "<tr><th scope='row'>Date</th><td>{$this->date}</td></tr>";

        if(!empty($this->member_of)) {
            switch ($this->type) {
                case "book":
                    $group_name = "Series";
                    break;
                case "forumPost":
                    $group_name = "Forum";
                    break;
                case "blogPost":
                    $group_name = "Blog Title";
                    break;
                case "webpage":
                    $group_name = "websiteTitle";
                    break;
                default:
                    $group_name = "Member Of";
            }
            $item_string .= "<tr><th scope='row'>$group_name</th><td>{$this->member_of}</td></tr>";
        }

        if(!empty($this->abstract)) $item_string .= "<tr><th scope='row'>Summary</th><td>{$this->abstract}</td></tr>";

        if(!empty($this->cache_path) and $this->notes != null and count($this->notes) > 0) {
            foreach ($this->notes as $note_key) {
                $note = new Note(key: $note_key, cache_path: $this->cache_path);
                $item_string .= $note;
            }
        }

        if(!empty($this->url)) $item_string .= "<tr><th scope='row'>URL</th><td><a href='{$this->url}'>{$this->url}</a></td></tr>";

        if(!empty($this->cache_path and $this->attachments != null and count($this->attachments) > 0)) {
            foreach ($this->attachments as $att_key) {
                $att = new Attachment(key: $att_key, cache_path: $this->cache_path);
                $item_string .= $att;
            }
        }

        if($this->tags != null and count($this->tags) > 0) {
            $item_string .= "<tr><th scope='row'>Tag(s)</th><td>";
            foreach ($this->tags as $tag) $item_string .= "{$tag['tag']}, ";
            $item_string = trim($item_string, ", ");
            $item_string .= "</td></tr>";
        }

        $item_string .= "</table></div>";
        return $item_string;
    }

    public function store(?string $cache_path) {
        if (empty($this->key)) return;

        if (empty($cache_path)) {
            if (empty($this->cache_path)) return;
            $cache_path = $this->cache_path;
        }

        if (!file_exists("$cache_path")) mkdir("$cache_path");
        if (!file_exists("$cache_path/items")) mkdir("$cache_path/items");

        file_put_contents("$cache_path/items/{$this->key}.json", json_encode($this));
        return "Stored item at: $cache_path/items/{$this->key}.json";
    }
    public function addChildren(?string $cache_path) {
        if (empty($this->key)) return;

        if (empty($cache_path)) {
            if (empty($this->cache_path)) return;
            $cache_path = $this->cache_path;
        }

        if ($this->attachments = null) $this->attachments = array();
        if ($this->notes = null) $this->notes = array();

        $atts = new DirectoryIterator("$cache_path/items/attachments");
        foreach ($atts as $att_file) {
            $att = new Attachment(key: substr($att_file->getFilename(), 0, -5), cache_path: $cache_path);
            if (
                $att->parent_item == $this->key and
                !(
                    $this->attachments != null and count($this->attachments) < 1
                    and (array_search($att->parent_item, $this->attachments))
                )
            )
                $this->attachments[] = $att->key;
        }

        $notes = new DirectoryIterator("$cache_path/items/notes");
        foreach ($notes as $note_file) {
            $note = new Note(key: substr($note_file->getFilename(), 0, -5), cache_path: $cache_path);
            if (
                $note->parent_item == $this->key and
                !(
                    $this->notes != null and count($this->notes) < 1
                    and (array_search($note->parent_item, $this->notes))
                )
            )
                $this->notes[] = $note->key;
        }
    }
}
?>