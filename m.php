<?php
// 图片或视频文件的路径
$filePath = $_GET['f'];
//http://wscp.com/mi.php?filePath=20230401
$filePath = 'https://xcimg.szwego.com/' . $filePath;
$fileType = substr($filePath, -4);
if ($fileType == '.mp4') {
  header('Content-Type: video/mp4'); // 对于JPG图片；对于视频，使用 'video/mp4'
} else {
  // 设置正确的内容类型头
  header('Content-Type: image/jpeg'); // 对于JPG图片；对于视频，使用 'video/mp4'
}
// 读取并输出文件内容
readfile($filePath);
exit;
