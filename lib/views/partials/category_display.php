<div class="category--container">
    <?php foreach ($newsDetails['category'] as $category): ?>
        <p class="category--name">
            <a href="/<?= htmlspecialchars(lcfirst($category)) ?>">
                <?php echo htmlspecialchars($category); ?>
            </a>
        </p>
    <?php endforeach ?>
</div>
