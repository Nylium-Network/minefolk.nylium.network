<?php
    require "item.php";

    class Items_array {
        public $items;
        public $attachments;

        function __construct(array $input_items) {
            $this->items = array();
            $this->attachments = array();
            foreach ($input_items as $i) {
                if ($i['data']['itemType'] == "attachment")
                    $this->attachments[] = new Item(key: $i["data"]['key'], itemType: $i['data']['itemType'], children: $this->attachments, title: $i['data']['title'], url: $i['data']['url'], parentItem: $i['data']['parentItem'], tags: $i['data']['tags'], date: $i['data']['date'], shortTitle: $i['data']['shortTitle'], creators: $i['data']['creators'], abstractNote: $i['data']['abstractNote'], blogTitle: $i['data']['blogTitle'], websiteTitle: $i['data']['websiteTitle'], forumTitle: $i['data']['forumTitle'], series: $i['data']['series'], seriesNumber: $i['data']['seriesNumber']);
            }
            foreach ($input_items as $i) {
                if ($i['data']['itemType'] != "attachment")
                    $this->items[] = new Item(key: $i["data"]['key'], itemType: $i['data']['itemType'], children: $this->attachments, title: $i['data']['title'], url: $i['data']['url'], parentItem: $i['data']['parentItem'], tags: $i['data']['tags'], date: $i['data']['date'], shortTitle: $i['data']['shortTitle'], creators: $i['data']['creators'], abstractNote: $i['data']['abstractNote'], blogTitle: $i['data']['blogTitle'], websiteTitle: $i['data']['websiteTitle'], forumTitle: $i['data']['forumTitle'], series: $i['data']['series'], seriesNumber: $i['data']['seriesNumber']);
            }
        }

        /* private static function date_comp(Work $a, Work $b) {
            return $a->date <=> $b->date;
        }

        function sort_by_date(bool $asc = false) {
            $array_to_sort = $this->works;
            usort($array_to_sort, array('Works_array','date_comp'));
            if (!$asc) return array_reverse($array_to_sort);
            return $array_to_sort;
        } */

        function __tostring() {
            $return = "";
            foreach ($this->items as $i) {
                $return .= "$i";
            }

            return $return;
        }
    }
?>