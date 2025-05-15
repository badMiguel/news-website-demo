<h2>Edit News</h2>
<?php if (isset($_SESSION['newsEditStatus'])): ?>
    <?php if ($_SESSION['newsEditStatus']): ?>
        <p style="color: green;">News updated successfully!</p>
    <?php else: ?>
        <p style="color: red;">Please fill in.</p>
    <?php endif; ?>
    <?php unset($_SESSION['newsEditStatus']); ?>
<?php endif; ?>

<form method="POST" action="/news/edit/submit">
    <input type="hidden" name="news_id" value="<?php echo htmlspecialchars($newsDetails[0]['news_id']); ?>">
    <label>Title: <input type="text" name="news_title" value="<?php echo htmlspecialchars($newsDetails[0]['news_title']); ?>" required></label><br>
    <label>Summary: <textarea name="news_subtitle" required><?php echo htmlspecialchars($newsDetails[0]['news_subtitle']); ?></textarea></label><br>
    <label>Body: <textarea name="body" required><?php echo htmlspecialchars($newsDetails[0]['body']); ?></textarea></label><br>

    <p>Category:</p>
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

    <button type="submit">Update News</button>
</form>
