<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Works - Nylium Network</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="author" content="Voxel(s)">

    <meta property="og:type" content="website">
    <meta property="og:title" content="Works - Nylium Network" />
    <meta property="og:description" content="A list of the works catalogued here" />
    <meta property="og:url" content="https://nylium.network/works" />
    <meta property="og:image" content="https://nylium.network/image/nylium-block.svg" />
    
    <link rel="stylesheet" type="text/css" href="/style.css">
    <link rel="stylesheet" type="text/css" href="/font/otf_Minecraft.css">
    <link rel="icon" type="image" href="/image/nylium-block.svg">
</head>
<body>
    <header>
        <h1>nylium<img title="Nylium Cube" class="intext-image" src="/image/nylium-block_symbols.svg" alt="A cube with three visible faces. The top face is dark red with a light teal alt key symbol. The viewer's left face is red with a dark teal inkpot from the fictionkin symbol. The viewer's right face is teal with a red quill from the fictionkin symbol.">network</h1>
        <h2>A list of the works catalogued here</h2>
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
        <p>Not affiliated with or endorsed by Mojang or Microsoft, yada yada. You know the deal!</p>
    </footer>
</body>
</html>