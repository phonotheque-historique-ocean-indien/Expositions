<?php
$article = $this->getVar("article");
?>
<div class="exposition-phoi">
    <nav class="breadcrumb has-succeeds-separator" aria-label="breadcrumbs">
        <div class="container">
            <ul class="ariane">
                <li><a href="/">Accueil</a></li>
                <li><a href="./Expositions">Expositions</a></li>
                <li class="is-active"><a href="#" aria-current="page"><?php _p($article["title"] . " " . $article["subtitle"]); ?></a>
                </li>
            </ul>
        </div>
    </nav>
    <section class="section" id="buttons" style="padding-top: 0;padding-bottom: 0;">
        <div class="container">
            <a href="/index.php/Contribuer/Pages/Form/template/article">
                <button class="button action-btn add-new is-uppercase has-text-centered">
                    <span class="icon"><i class="mdi mdi-plus"></i></span>&nbsp; Nouveau
                </button>
            </a>
            <a href="/index.php/Contribuer/Pages/EditForm/template/article/id/1">
                <button class="button action-btn add-new is-uppercase has-text-centered">
                    <span class="icon"><i class="mdi mdi-lead-pencil"></i></span>&nbsp; Modifier
                </button>
            </a>
        </div>
    </section>

    <section class="section" id="article">
        <div class="container">

            <div class="article-header level">
                <div class="level-left">
                    <h1 class="title"><?php _p($article["title"]); ?></h1>
                    <h1 class="subtitle"><?php _p($article["subtitle"]); ?></h1>
                </div>
                <div class="level-right">
                    <p class="published-by">publié par</p>
                    <p class="publisher"><?php _p($article["author"]); ?></p>
                    <p class="date"><?php _p($article["date"]); ?></p>
                </div>
            </div>
            <div>
                <img src="<?php _p($article["image"]); ?>" alt="image 1">
            </div>
            <?php
            $blocs = json_decode($article["blocs"], true);

            foreach ($blocs as $bloc):
                $bloc["content"] = str_replace("\\n", "", $bloc["content"]);
                switch ($bloc["type"]):
                    case "lead-dropcap":
                        ?>

                        <article class="article-content">
                            <div class="lead-dropcap"><?php _p($bloc["content"]); ?></div>
                        </article>

                        <?php break;
                    case "paragraph": ?>

                        <article class="article-content">
                            <?php _p($bloc["content"]); ?>
                        </article>

                        <?php break;
                    case "image-is-fullsize":
                        ?>

                        <figure class="image-full">
                            <img src="<?php print $bloc["image"]; ?>" alt="Image 2 fullwidth">
                            <figcaption><?php print $bloc["figcaption"]; ?></figcaption>
                        </figure>

                        <?php break;
                    case "two-images":
                        ?>

                        <div class="columns image-row two-images">
                            <figure class="column">
                                <img src="<?php print $bloc["image1"]; ?>" alt="Image 3">
                                <figcaption><?php print $bloc["figcaption1"]; ?></figcaption>
                            </figure>
                            <figure class="column">
                                <img src="<?php print $bloc["image2"]; ?>" alt="Image 4">
                                <figcaption><?php print $bloc["figcaption2"]; ?></figcaption>
                            </figure>
                        </div>

                        <?php break;
                    case "image-with-text":
                        ?>

                        <article class="article-content">
                            <div class="columns image-with-text">
                                <div class="column">
                                    <img src="<?php print $bloc["image"]; ?>" alt="image 5">
                                </div>
                                <div class="column">
                                    <?php print str_replace("&quo;", '"', $bloc["content"]); ?>
                                </div>
                            </div>
                        </article>

                        <?php break;
                    case "video": ?>

                        <article class="article-content">
                            <video controls width="auto" preload="metadata">
                                <source src="<?php _p($bloc["video-track"]); ?>"
                                        type="video/<?php _p($bloc["format"]); ?>">
                                Sorry, your browser doesn't support embedded videos.
                            </video>
                        </article>

                        <?php break;
                    case "references":
                        print "<div class=\"article-content footnotes\">";
                        if ($bloc["footnote1"]) print "<h4>Références</h4><ol>";
                        if ($bloc["footnote1"]) print "<li id=\"footnote1\">{$bloc["footnote1"]}</li>";
                        if ($bloc["footnote2"]) print "<li id=\"footnote1\">{$bloc["footnote2"]}</li>";
                        if ($bloc["footnote3"]) print "<li id=\"footnote1\">{$bloc["footnote3"]}</li>";
                        if ($bloc["footnote4"]) print "<li id=\"footnote1\">{$bloc["footnote4"]}</li>";
                        if ($bloc["footnote5"]) print "<li id=\"footnote1\">{$bloc["footnote5"]}</li>";
                        if ($bloc["footnote6"]) print "<li id=\"footnote1\">{$bloc["footnote6"]}</li>";
                        if ($bloc["footnote1"]) print "</ol>";
                        print "<h4>Pour en savoir plus</h4>";
                        print $bloc["content"];
                        print "</div>";
                        break;
                    default:
                        print "<div style='border:1px solid black; padding:50px;margin:20px 0;>Type JSON inconnu : {$bloc["type"]}</div>";

                        break;
                endswitch;
            endforeach; ?>

        </div>
    </section>

    <section class="section" id="related-playlist">
        <h1>Playlists associées</h1>
    </section>

    <section class="section" id="now-playing">
        <h1>À l’écoute</h1>
    </section>

</div>
