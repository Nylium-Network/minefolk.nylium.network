<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Works (Minefolk) - Nylium Network</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Voxel(s)">

    <meta property="og:type" content="website">
    <meta property="og:title" content="Works (Minefolk) - Nylium Network" />
    <meta property="og:description" content="A list of the works catalogued here" />
    <meta property="og:url" content="https://minefolk.nylium.network/works" />
    <meta property="og:image" content="https://minefolk.nylium.network/image/nylium-block.svg" />
    
    <link rel="stylesheet" type="text/css" href="/style.css">
    <link rel="stylesheet" type="text/css" href="/font/otf_Minecraft.css">
    <link rel="icon" type="image" href="/image/nylium-block.svg">
</head>
<body>
    <header>
        <a href="https://www.nylium.network"><img title="Nylium Network Home" src="/image/nylium-block.svg" alt="An oblique cube. Its top face is dark red, its left face is bright red, and its right face is teal. Its edges are gray."></a>
        <h1>Works Library</h1>
    </header>
    <nav>
        <ul>
            <li><a href="/">Home</a></li>
            <li><a href="#">Works</a></li>
            <li><a href="https://map.nylium.network">SMP Map</a></li>
            <li><a href="/about">About</a></li>
        </ul>
    </nav>
    <main>
        <p>Here is a library of some things written by and about Minecraft and alterhumanity. These are pulled automatically from the <a href="https://www.zotero.org/groups/6093877/nyliumnetwork/library">Zotero library</a>. At the moment, they're mostly sorted in reverse chronological order.</p>
        <p>Coming soon: searching and better sorting! For now, you can use <code>Ctrl+F</code> or your browser's &ldquo;find&rdquo; feature to search for keywords.</p>
        <p>Also check out our <a href="https://nyliumnetwork.tumblr.com/">tumblr blog</a>!</p>
        <?php
           require "scripts/sync_library.php";
            sync_library(prefix: "groups/6093877", cache_path: "cache");

            $items = array();
            $items_misc_date = array();
            $items_undated = array();

            $item_dir = new DirectoryIterator("cache/items");

            foreach ($item_dir as $item_key) {
                $new_item = new Item(key: substr($item_key, 0, -5), cache_path: "cache");
                if (!empty($new_item->__tostring())) {
                    if (empty($new_item->date)) $items_undated[] = $new_item->__tostring();
                    else if (!preg_match("/\d{4}[-]\d{2}[-]\d{2}/", $new_item->date)) $items_misc_date[] = $new_item->__tostring();
                    else $items[(int)substr($new_item->date, 0, 4)][(int)substr($new_item->date, 5, 2)][(int)substr($new_item->date, 8, 2)][] = $new_item->__tostring();
                }
            }

            $items_display = "";
            $months_list = "<p>Click/tap a month to jump to it.</p><div class='overflow'><table>";

            $months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
            $years = array_keys($items);
            rsort($years);

            foreach ($years as $y) {
                $months_list .= "<tr><th scope='row'>$y</th>";
                if (array_key_exists($y, $items) and count($items[$y]) > 0) {
                    $items_display .= "<section><h2>$y</h2>";
                    for ($m = 12; $m >= 1; $m--) {
                        if (array_key_exists($m, $items[$y]) and count($items[$y][$m]) > 0) {
                            $items_display .= "<section id='$y-$m'><h3>{$months[$m-1]}</h2><div class='works-list'>";
                            $months_list .= "<td><a href='#$y-$m'>{$months[$m-1]}</a></td>";
                            for ($d = 31; $d >= 1; $d--) {
                                if (is_array($items[$y][$m]) and array_key_exists($d, $items[$y][$m]) and count($items[$y][$m][$d]) > 0) {
                                    foreach ($items[$y][$m][$d] as $item) $items_display .= $item;
                                }
                            }
                            $items_display .= "</div></section>";
                        }
                    }
                    $items_display .= "<p><a href='#'>Back to Top</a></p></section>";
                    $months_list .= "</tr>";
                }
            }

            $items_display .= "<section id='other-dates'><h2>Other Dates</h2><div class='works-list'>";
            foreach ($items_misc_date as $item)
                $items_display .= $item;
            $items_display .= "</div></section><section id='undated'><h2>Undated</h2><div class='works-list'>";
            foreach ($items_undated as $item)
                $items_display .= $item;
            $items_display .= "</div></section>";
            $months_list .= "<tr><th scope='row'>Other</th><td><a href='#other-dates'>Other Dates</a></td><td><a href='#undated'>Undated</a></td></tr></table></div>";

            echo $months_list;
            echo $items_display;
        ?>
    </main>
    <footer>
        <p>Site by <a href="https://voxel.gay">Voxel(s)</a>.</p>
        <p>We are neither affiliated with, nor endorsed by, Microsoft or Mojang.</p>
    </footer>
</body>
</html>