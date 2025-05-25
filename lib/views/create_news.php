<h1>Create News</h1>

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

<form class="crud-form" action="/news/create/submit" method="POST" enctype="multipart/form-data">
    <div class="crud-form--field">
        <label class="crud-form--label" for="news_title">Title</label>
        <input
            class="crud-form--input"
            type="text"
            name="news_title"
            id="news_title"
            placeholder="Enter news title" />
    </div>

    <div class="crud-form--field">
        <label class="crud-form--label" for="news_subtitle">Subtitle</label>
        <input
            class="crud-form--input"
            type="text"
            name="news_subtitle"
            id="news_subtitle"
            placeholder="Enter news subtitle" />

    </div>

    <div class="crud-form--field">
        <label class="crud-form--label" for="body">Body</label>
        <textarea name="body" cols="40" rows="20" class="crud-form--input" placeholder="Enter news body" ></textarea>
    </div>

    <div class="crud-form--field">
        <p class="crud-form--label">Category</p>
        <div class="crud-form--category-list">
            <?php foreach ($categoryList as $c): ?>
                <label>
                    <input
                        type="checkbox"
                        name="category[]"
                        value="<?= htmlspecialchars(lcfirst($c["category_id"])) ?>" />
                    <?= $c["category"] ?>
                </label>
            <?php endforeach ?>
        </div>
    </div>

    <div class="crud-form--field">
        <label class="crud-form--label" for="image">Image</label>
        <input class="crud-form--input" type="file" name="image" accept="image/*" />
    </div>

    <div class="crud-form--submit--container">
        <button class="crud-form--submit" type="submit">Create News</button>
    </div>
</form>
