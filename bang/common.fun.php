<?php
function getHtmlImgUrlArr($html){

  $dom = new DOMDocument();
  @$dom->loadHTML($html); // 使用@来抑制加载HTML时的警告信息
  $xpath = new DOMXPath($dom);
  $images = $xpath->query('//img');
  $imageUrls = [];
  foreach ($images as $img) {
      $src = $img->getAttribute('src');
      // 如果需要确保URL是完整的，可以进一步处理（例如，拼接基础URL）
      // $imageUrls[] = 'http://example.com/' . $src; // 示例：添加基础URL（如果有的话）
      $imageUrls[] = $src; // 直接添加src属性值（如果它已经是完整的URL）
  }
  return $imageUrls; // 输出所有图片的URL地址
}


/**
 * 按单词截取字符串
 * @param string $str 输入字符串
 * @param int $length 最大长度
 * @return string 截取后的字符串
 */
function truncateByWord(string $str, int $length): string {
    if (strlen($str) <= $length) {
        return $str;
    }
    
    $truncated = substr($str, 0, $length);
    $lastSpace = strrpos($truncated, ' ');
    
    if ($lastSpace !== false) {
        $truncated = substr($truncated, 0, $lastSpace);
    }
    
    return $truncated;
}