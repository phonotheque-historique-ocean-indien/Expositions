<?php
$article = $this->getVar("article");
$id = $this->getVar("id");
$article["blocs"]=str_replace('\\\n',"",$article["blocs"]);
$blocs=json_decode($article["blocs"],true);
//var_dump($blocs);die();
$content= $blocs[1]["content"];
$content=mb_substr($content,0,119);
if(mb_strlen($content)==119) {
    $content=$content."...";
}
?>
<div class="column is-one-third">
    <div class="card">
        <div class="card-image">
            <figure class="image is-3by2">
                <img src="<?php _p($article["image"]); ?>" alt="image thumbnail">
            </figure>
        </div>
        <div class="card-content">
            <div class="content" style="height:210px">
                <div class="card-details">
                    <p class="author"><?php _p($article["author"]); ?></p>
                    <p class="date pull-right"><?php _p($article["date"]); ?></p>
                </div>
                <h2 class="card-title"><?php _p($article["title"]); ?></h2>
                <h3 class="card-subtitle"><?php _p($article["subtitle"]); ?></h3>
                <p><?php _p($content); ?></p>
            </div>
        </div>
        <footer class="card-footer">
            <a href="./Details/id/<?php _p($id); ?>" class="card-footer-item">Lire l'article </a>
        </footer>
    </div>
</div>