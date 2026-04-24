<?php

function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function isEmpty($field) {
    return empty($field);
}