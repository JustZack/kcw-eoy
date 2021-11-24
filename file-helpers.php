<?php

function kcw_eoy_getJSONfromFile($path) {
    return json_decode(file_get_contents($path), true);
}

?>