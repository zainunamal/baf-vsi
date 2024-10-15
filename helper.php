<?php
function nullable_htmlspecialchar($value, $flags = ENT_QUOTES) {
    if (is_null($value)) {
        $value = '';
    }
    return htmlentities($value, $flags);
}
?>