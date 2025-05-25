<h2>Edit News</h2>
<?php
session_start();
if (isset($_SESSION['newsEditStatus'])) {
    if ($_SESSION['newsEditStatus']) {
        echo "<p style='color: green;'>News updated successfully!</p>";
    } else {
        echo "<p style='color: red;'>Please fill in.</p>";
    }
    unset($_SESSION['newsEditStatus']);
}
session_write_close();
?>

<form class="crud-form" method="POST" action="/news/edit/submit" enctype="multipart/form-data">
    <input type="hidden" name="news_id" value="<?php echo htmlspecialchars($newsDetails[0]['news_id']); ?>">

    <div class="crud-form--field">
        <label class="crud-form--label" for="news_title">Title</label>
        <input class="crud-form--input" type="text" name="news_title" value="<?php echo htmlspecialchars($newsDetails[0]['news_title']); ?>" required>
    </div>

    <div class="crud-form--field">
        <label class="crud-form--label" for="news_subtitle">Summary</label>
        <textarea class="crud-form--input" name="news_subtitle" required rows="5"><?php echo htmlspecialchars($newsDetails[0]['news_subtitle']); ?></textarea>
    </div>

    <div class="crud-form--field">
        <label class="crud-form--label" for="body">Body</label>
        <textarea class="crud-form--input" name="body" required rows="20"><?php echo htmlspecialchars($newsDetails[0]['body']); ?></textarea>
    </div>

    <div class="crud-form--field">
        <p class="crud-form--label">Category</p>
        <div class="crud-form--category-list">
            <?php foreach ($categoryList as $c): ?>
                <label>
                    <input
                        type="checkbox"
                        name="category[]"
                        <?php if (in_array($c["category"], $newsDetails[0]['category'])): ?>
                        checked
                        <?php endif ?>
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
    <!-- <?php if (htmlspecialchars($newsDetails[0]["image_path"])): ?> -->
    <!--     <img style="width: 100%;" src="/../images/<?= htmlspecialchars($newsDetails[0]['image_path']) ?>" /> -->
    <!-- <?php endif ?> -->

    <div class="crud-form--submit--container">
    <button class="crud-form--submit" type="submit">Update News</button>
    </div>
</form>
