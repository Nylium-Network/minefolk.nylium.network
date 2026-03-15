<?php
class Item {
    // all
    public $key; 
    public $itemType;
    public $title;
    public $url;
    public $tags;

    // $itemType != "attachment"
    public $date;
    public $shortTitle;
    public $creators;
    public $abstractNote;
    public $attachments;

    // $itemType == "attachment"
    public $parentItem; 

    // $itemType == "blogPost"
    public $blogTitle; 

    // $itemType == "website"
    public $websiteTitle; 

    // $itemType == "forumPost"
    public $forumTitle;

    // $itemType == "book"
    public $series;
    public $seriesNumber;

    function __construct(
        /* required: */ string $key, string $itemType, ?array $children,
        /* all: */ ?string $title, ?string $url, ?array $tags, 
        /* !attachment: */ ?string $date, ?string $shortTitle, ?array $creators, ?string $abstractNote, 
        /* attachment: */ ?string $parentItem,
        /* blogPost: */ ?string $blogTitle,
        /* website: */ ?string $websiteTitle,
        /* forumPost: */ ?string $forumTitle,
        /* book: */ ?string $series, ?string $seriesNumber
    ) {
        $this->key = $key; //
        $this->itemType = $itemType; //

        if (!empty($title)) $this->title = $title; //
        if (!empty($url)) $this->url = $url; //

        if ($itemType == "attachment" and !empty($parentItem)) $this->parentItem = $parentItem;

        if ($itemType != "attachment") {
            if ($tags != null and count($tags) > 0) $this->tags = $tags; //

            if (!empty($date)) $this->date = $date; //
            if (!empty($shortTitle)) $this->shortTitle = $shortTitle; //
            if (!empty($abstractNote)) $this->abstractNote = $abstractNote; //
            if ($creators != null and count($creators) > 0) $this->creators = $creators; //

            if (!empty($blogTitle)) $this->blogTitle = $blogTitle; //
            if (!empty($websiteTitle)) $this->websiteTitle = $websiteTitle; //
            if (!empty($forumTitle)) $this->forumTitle = $forumTitle; //

            if (!empty($series)) $this->series = $series; //
            if (!empty($seriesNumber)) $this->seriesNumber = $seriesNumber; //

            foreach ($children as $i) {
                if ($i->parentItem == $key) {
                    $new_attachment = array();
                    if (!empty($i->title)) $new_attachment['title'] = $i->title;
                    if (!empty($i->url)) $new_attachment['url'] = $i->url;
                    if ($new_attachment != null and count($new_attachment) > 0) $this->attachments[] = $new_attachment;
                    array_splice($children, array_search($i, $children), 1);
                }
            
            }
        }
    }

    private function url_handler(string $input_url) {
            $url_str = preg_replace("/https?:\/\//iU", "", $input_url);
            return "<a href='{$input_url}'>{$url_str}</a>";
        }

    function __tostring() {
        if ($this->itemType == "attachment") return "Item is of type attachment. Please do not print.";

        $return = "";

        if (!empty($this->shortTitle)) {
            $return .=
            "<div class='work-summary'>
                <h3>{$this->shortTitle}</h3>
                <table>";
            if (!empty($this->title)) {
                $return .= "<tr><th scope='row'>Full Title</th><td>{$this->title}</td></tr>";
            }
        }
        else if (!empty($this->title)) {
            $return .=
            "<div class='work-summary'>
                <h3>{$this->title}</h3>
                <table>";
        }

        if (!empty($this->series)) {
            $return .= "<tr><th scope='row'>Series</th><td>";
            if (!empty($this->seriesNumber)) $return .= "#{$this->seriesNumber} of {$this->series}</td></tr>";
            else $return .= "{$this->series}</td></tr>";
        }

        if ($this->creators !=null and count($this->creators) > 0) {
            $return .= "<tr><th scope='row'>Author(s)</th><td>";
            foreach ($this->creators as $c) {
                $return .= "{$c['name']}, ";
            }
            $return = substr($return, 0, -2);
            $return .= "</td></tr>";
        }

        if (!empty($this->date)) $return .= "<tr><th scope='row'>Date</th><td>{$this->date}</td></tr>";

        $platform = "";
        if (!empty($this->websiteTitle)) $platform = $this->websiteTitle;
        else if (!empty($this->forumTitle)) $platform = $this->forumTitle;
        else if (!empty($this->blogTitle)) $platform = $this->blogTitle;
        if (!empty($platform)) $return .= "<tr><th scope='row'>Website/Forum/Blog</th><td>$platform</td></tr>";

        if (!empty($this->abstractNote)) $return .= "<tr><th scope='row'>Summary</th><td>{$this->abstractNote}</td></tr>";

        if (!empty($this->url)) $return .= "<tr><th scope='row'>Link</th><td>{$this->url_handler($this->url)}</td></tr>";

        if ($this->attachments != null and count($this->attachments) > 0) {
            $attachments = "";
            foreach ($this->attachments as $a) {
                if (!empty($a['title'])) $attachments .= "<tr><th scope='row'>{$a['title']}</th>";
                else $attachments .= "<tr><th scope='row'>Attachment</th>";
                if (!empty($a['url'])) $attachments .= "<td>{$this->url_handler($a['url'])}</td>";
                else $attachments .= "<td></td>";
                $attachments .= "</tr>";
            }
            $return .= $attachments;
        }

        if ($this->tags != null and count($this->tags) > 0) {
            $return .= "<tr><th scope='row'>Tags</th><td>";
            foreach ($this->tags as $t) {
                $return .= "{$t['tag']}; ";
            }
            $return = substr($return, 0, -2);
            $return .= "</td></tr>";
        }

        if (!empty($return)) $return .= "</table></div>";

        return $return;
    }
}
?>