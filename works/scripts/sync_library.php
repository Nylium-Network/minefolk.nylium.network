<?php
    require "class/item.php";
    require "class/item/attachment.php";
    require "class/item/note.php";
    function sync_library(string $prefix, string $cache_path, /* bool $bypass_timer = false */) {
        //check for cache directories and create if nonexistent
        if (!file_exists($cache_path)) mkdir($cache_path);

        $metadata = array (
            "version" => 0, //latest item version in the cache
            "last_sync" => 0 //unix timestamp of last library sync
        );
        if (!(file_exists("$cache_path/metadata.json"))) file_put_contents("$cache_path/metadata.json", json_encode($metadata)); //create metadata file if none exists and initialize with zeros
        else $metadata = json_decode(file_get_contents("$cache_path/metadata.json"), true); //otherwise, set metadata

        //if it has been at least 24 hours since the last sync...
        /* if ( $bypass_timer or ((time() - $metadata["last_sync"]) >= 86400) ) { */
            //update last sync time to now
            $metadata["last_sync"] = time();

            $library_url = "https://api.zotero.org/$prefix/items?v=3"; //base URL for remote library
        
            //initialize and set options for curl process
            $ch = curl_init();
            curl_setopt_array(
                $ch, 
                array(
                    CURLOPT_URL => "$library_url&since={$metadata["version"]}&format=versions&includeTrashed=1", //downloads list of items in library along with versions
                    CURLOPT_HEADER => true, //use response header as well
                    CURLOPT_RETURNTRANSFER => true //download as string
                )
            );

            //download item list + headers and separate
            $item_list_response = explode("{", curl_exec($ch), 2);

            //convert headers to array
            $item_list_headers_simple = explode(PHP_EOL, $item_list_response[0]); //separate headers into 
            $item_list_headers = array ();
            foreach ($item_list_headers_simple as $num => $header) {
                list($key, $value) = explode(': ', $header);
                $item_list_headers[$key] = $value;
            }
            //read version from headers
            $new_version = (int)$item_list_headers["last-modified-version"];

            //if remote library is newer than cache...
            if ($new_version > $metadata["version"]) {
                //update version number
                $metadata["version"] = $new_version;

                //convert items to array
                $item_list = json_decode("{ $item_list_response[1]", true);
                //evaluate item list for download
                //items that either do not exist in the cache yet, or are older in the cache than their remotes, should be downloaded
                $items_to_download = array(); //initialize array
                foreach ($item_list as $key => $version) { //for each item in the list
                    $stored_item = null;
                    if (file_exists("$cache_path/items/$key.json")) $stored_item = json_decode(file_get_contents("$cache_path/items/$key.json"), true);
                    else if (file_exists("$cache_path/items/attachments/$key.json")) $stored_item = json_decode(file_get_contents("$cache_path/items/attachments/$key.json"), true);
                    else if (file_exists("$cache_path/items/notes/$key.json")) $stored_item = json_decode(file_get_contents("$cache_path/items/notes/$key.json"), true);
                    if ($stored_item != null and count($stored_item) > 0 and $version > $stored_item["version"]) $items_to_download[] = $key;
                    else $items_to_download[] = $key;
                }

                //download items, max 50 at a time, and write to files
                while (count($items_to_download) > 0) { //until there are no more items marked for download...
                    $queued_items = ""; //initialize queue string
                    for ($i = 0; $i < 50; $i++) { //queue max 50 items at a time
                        $item_to_queue = array_shift($items_to_download); //take the next item marked for download, removing it from the array
                        $queued_items .= "$item_to_queue,"; //add it to the queue string
                    }
                    $queued_items = trim($queued_items, ","); //queue is full; remove the final comma

                    //change URL to use the queue and don't use response header
                    curl_setopt_array(
                        $ch, 
                        array(
                            CURLOPT_URL => "$library_url&itemKey=$queued_items&includeTrashed=1",
                            CURLOPT_HEADER => false,
                        )
                    );
                    $item_response = json_decode(curl_exec($ch), true); //download items in queue and convert to array
                    $new_items = array();
                    foreach ($item_response as $item) { //for each newly downloaded item
                        //if item is note, create new note
                        if ($item["data"]["itemType"] == "note") {
                            if ($item["data"]["deleted"] == 1) file_put_contents("$cache_path/items/notes/{$item["key"]}.json", "");
                            else $new_item = new Note(key: $item["key"], cache_path: $cache_path, version: $item["version"], note: $item["data"]["note"], parent_item: $item["data"]["parentItem"]);
                        }

                        else if ($item["data"]["itemType"] == "attachment") { //if item is an attachment
                            if ($item["data"]["deleted"] == 1) file_put_contents("$cache_path/items/attachments/{$item["key"]}.json", "");
                            else {
                                switch ($item["data"]["linkMode"]) { //check the link mode
                                    case "linked_url": //if it's a url
                                        $url = $item["data"]["url"]; //write it directly
                                        break;
                                    case "imported_file": //if it's a file
                                        $url = "{$item["links"]["alternate"]["href"]}/reader"; //use the file link
                                        break;
                                    default:
                                        $url = "";
                                }
                                //create a new attachment
                                $new_item = new Attachment(key: $item["key"], version: $item["version"], cache_path: $cache_path, url: $url, parent_item: $item["data"]["parentItem"], title: $item["data"]["title"]);
                            }
                        }
                        else { //otherwise
                            if ($item["data"]["deleted"] == 1) file_put_contents("$cache_path/items/notes/{$item["key"]}.json", "");
                            else {
                                switch ($item["data"]["itemType"]) { //set $member_of to the item's type's equivalent
                                    case "blogPost":
                                        $member_of = $item["data"]["blogTitle"];
                                        break;
                                    case "forumPost":
                                        $member_of = $item["data"]["forumTitle"];
                                        break;
                                    case "webpage":
                                        $member_of = $item["data"]["websiteTitle"];
                                        break;
                                    case "book":
                                        $member_of = $item["data"]["series"];
                                        break;
                                    default:
                                        $member_of = "";
                                }
                                //create a new item
                                $new_item = new Item(key: $item["key"], type: $item["data"]["itemType"], version: $item["version"], cache_path: $cache_path, title: $item["data"]["title"], creators: $item["meta"]["creatorSummary"], date: $item["meta"]["parsedDate"], url: $item["data"]["url"], abstract: $item["data"]["abstractNote"], member_of: $member_of, tags: $item["data"]["tags"]);
                            }
                        }
                        $new_items[] = $new_item; //add item to array
                    }
                    //write items in array to file
                    foreach ($new_items as $item) { 
                        $item->Store($cache_path);
                    }
                }
                
                $items_top = new DirectoryIterator("$cache_path/items");
                foreach ($items_top as $item_file) {
                    $item = new Item(key: substr($item_file->getFilename(), 0, -5), cache_path: $cache_path);
                    $item->addChildren($cache_path);
                    $item->store($cache_path);
                }
            }

            //update metadata file
            file_put_contents("$cache_path/metadata.json", json_encode($metadata));
        /* }
        else echo "library updates once per 24hr!"; */
    }
?>