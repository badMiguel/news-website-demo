<style>
    th {
        padding: 0.7rem 0;
    }

    td {
        padding: 0.7rem;
    }

    .edit-delete-button {
        background-color: var(--color-2);
        border-radius: 0.5rem;
        margin: 0.5rem 0;
        padding: 0.2rem 0.5rem;
    }
</style>
<h2 style="margin: 0 0 1rem 0;">Admin Dashboard - Manage News</h2>
<?php
if (!isset($_SESSION['privilege']) || $_SESSION['privilege'] < EDITOR) {
    header("Location: /");
    exit;
}
//    <p>Access denied. Only editors can view this page.</p>
?>

<?php if (empty($newsList)): ?>
    <p>No news available.</p>
<?php else: ?>
    <?php require_once VIEWS . "partials/news_amount.php" ?>
    <table style="width: 100%; margin: 2rem 0;" border="1">
        <thead>
            <tr style="text-align: center;">
                <th>Title</th>
                <th>Author</th>
                <th>Role</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($newsList as $news): ?>
                <tr style="text-align: center;">
                    <td style="width: 50%;text-align: left;"><?= htmlspecialchars($news['news_title']) ?></td>
                    <td><?= htmlspecialchars($news['author'] ?? 'Unknown') ?>
                    <td>
                        <?php
                        $role = 'Unknown';
                        if (isset($news['privilege'])) {
                            switch ($news['privilege']) {
                                case 0:
                                    $role = 'User';
                                    break;
                                case 1:
                                    $role = 'Journalist';
                                    break;
                                case 2:
                                    $role = 'Editor';
                                    break;
                            }
                        }
                        echo htmlspecialchars($role);
                        ?>
                    <td>
                        <a href="/news/edit?id=<?= htmlspecialchars($news['news_id']) ?>">
                            <div class="edit-delete-button">
                                Edit
                            </div>
                        </a>
                        <a href="/news/delete?id=<?= htmlspecialchars($news['news_id']) ?>" onclick="return confirm('Are you sure you want to delete this news?')">
                            <div class="edit-delete-button">
                                Delete
                            </div>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php require_once VIEWS . "partials/page_number.php" ?>
<?php endif; ?>
