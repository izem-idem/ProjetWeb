<?php
if (!function_exists('pg_fetch_assoc')) {
    function pg_fetch_assoc ($result): array
    {
        return @pg_fetch_array($result, NULL, PGSQL_ASSOC);
    }
}

if (!function_exists('str_starts_with')) {
    function str_starts_with ( $haystack, $needle ): bool
    {
        return strpos( $haystack , $needle ) === 0;
    }
}
?>
