<?php
function c_uuid($sDelim = '')
{
  // The field names refer to RFC 4122 section 4.1.2
  return sprintf('%04x%04x%s%04x%s%03x4%s%04x%s%04x%04x%04x',
    mt_rand(0, 65535), mt_rand(0, 65535), // 32 bits for "time_low"
    $sDelim,
    mt_rand(0, 65535), // 16 bits for "time_mid"
    $sDelim,
    mt_rand(0, 4095),  // 12 bits before the 0100 of (version) 4 for "time_hi_and_version"
    $sDelim,
    bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '01', 6, 2)),
    $sDelim,
    // 8 bits, the last two of which (positions 6 and 7) are 01, for "clk_seq_hi_res"
    // (hence, the 2nd hex digit after the 3rd hyphen can only be 1, 5, 9 or d)
    // 8 bits for "clk_seq_low"
    mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535) // 48 bits for "node"
  );
} // end of c_uuid

//echo "uuid = [".c_uuid()."]\n";
?>
