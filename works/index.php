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
        <p>Here is a library of some things written by and about Minecraft and alterhumanity. These are pulled automatically from the <a href="https://www.zotero.org/groups/6093877/nyliumnetwork/library">Zotero library</a>. At the moment, they're not sorted.</p>
        <p>Coming soon: sorting and searching! For now, you can use <code>Ctrl+F</code> or your browser's &ldquo;find&rdquo; feature to search for keywords.</p>
        <p>Also check out our <a href="https://nyliumnetwork.tumblr.com/">tumblr blog</a>!</p>
        <div class="works-list">
        <?php
            error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
            require "items_array.php";
            echo new Items_array(json_decode(file_get_contents("library.json"), true));
        ?>
        </div>
    </main>
    <footer>
        <p>Site by <a href="https://voxel.gay">Voxel(s)</a>.</p>
        <p>We are neither affiliated with, nor endorsed by, Microsoft or Mojang.</p>
    </footer>
</body>
</html>