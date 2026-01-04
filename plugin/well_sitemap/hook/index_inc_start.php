<?php exit;
$txt_len = strpos($_SERVER['REQUEST_URI'], '.txt');
if (FALSE !== strpos($_SERVER['REQUEST_URI'], 'sitemap') && $txt_len) {
    $request_url = mb_substr($_SERVER['REQUEST_URI'], 0, $txt_len, 'UTF-8');
    $request_url = trim($request_url, '/');
    if ($request_url) {
        $request_url = str_replace('/', '-', $request_url);
        $requestlist = explode('-', $request_url);
        if (empty($requestlist) || !is_array($requestlist)) exit('Access Error.');
        $_REQUEST = $requestlist;
        $_REQUEST[0] = 'sitemap';
    }
}
?>