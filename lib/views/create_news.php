<h1>this is create news page</h1>

<?php
session_start();
if (isset($_SESSION["newsCreateStatus"]) && $_SESSION["newsCreateStatus"] === false) {
    echo "<p><strong>Error submitting news</strong></p>";
} else if (isset($_SESSION["newsCreateStatus"]) && $_SESSION["newsCreateStatus"] === true) {
    echo "<p><strong>Successfully Submitted</strong></p>";
}
unset($_SESSION["newsCreateStatus"]);
session_write_close();
?>

<form action="/news/create/submit" method="POST" enctype="multipart/form-data">
    <label for="news_title">News Title:</label>
    <input type="text" name="news_title" id="news_title" />

    <label for="news_subtitle">News Subtitle:</label>
    <input type="text" name="news_subtitle" id="news_subtitle" />

    <label for="body">News Body:</label>
    <textarea name="body" id="body"></textarea>

    <label for="image">Select Image:</label>
    <input type="file" name="image" accept="image/*" />

    <p>Category:</p>
    <?php foreach ($categoryList as $c): ?>
        <label>
            <input
                type="checkbox"
                name="category[]"
                value="<?= htmlspecialchars(lcfirst($c["category_id"])) ?>" />
            <?= $c["category"] ?>
        </label>
    <?php endforeach ?>

    <button type="submit">Submit</button>
</form>
