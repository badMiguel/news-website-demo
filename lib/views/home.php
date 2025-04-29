<h1>this is home</h1>
<?php foreach ($currNewsList as $news): ?>
    <div class='news-card'>
        <h2><?= $news["news_title"] ?></h2>
        <p><?= $news["news_summary"] ?></p>
        <br>
        <?php if ($news["author"]): ?>
            <p>Author: <?= $news["author"] ?></p>
        <?php endif; ?>
        <p>Created: <em><?= $news["created_date"] ?></em></p>
        <p>Edited: <em><?= $news["edited_date"] ?></em></p>
    </div>
    <hr>
<?php endforeach ?>
<?php require VIEWS . "page_number.php" ?>
