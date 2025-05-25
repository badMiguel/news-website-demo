<?php

$add_S = function (int $time): string {
    if ($time > 1) {
        return "s";
    }
    return "";
};

$from = new DateTime($news["created_date"]);
$now = new DateTime();
$diff = $from->diff($now);

if ($diff->y > 0) {
    echo htmlspecialchars($diff->y . " year" . $add_S($diff->y) . " ago");
} else if ($diff->m > 0) {
    echo htmlspecialchars($diff->m . " month" . $add_S($diff->m) . " ago");
} else if ($diff->d > 0) {
    echo htmlspecialchars($diff->d . " day" . $add_S($diff->d) . " ago");
} else if ($diff->h > 0) {
    echo htmlspecialchars($diff->h . " hour" . $add_S($diff->h) . " ago");
} else if ($diff->i > 0) {
    echo htmlspecialchars($diff->i . " minute" . $add_S($diff->i) . " ago");
} else if ($diff->s > 0) {
    echo htmlspecialchars($diff->s . " second" . $add_S($diff->s) . " ago");
}
