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

<form action="/news/create/submit" method="POST">
    <label for="news_title">News Title:</label>
    <input type="text" name="news_title" id="news_title" />

    <label for="news_summary">News Summary:</label>
    <input type="text" name="news_summary" id="news_summary" />

    <label for="body">News Body:</label>
    <textarea name="body" id="body"></textarea>

    <button type="submit">Submit</button>
</form>
