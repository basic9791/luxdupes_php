<?php
require_once 'config.php';


$action = $_GET['action'] ?? '';
// 统一权限检查
checkLogin($token);

header('Content-Type: application/json');
// Allow cross-origin requests from any domain
header("Access-Control-Allow-Origin: *");

// Specify allowed HTTP methods
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Specify allowed headers
header("Access-Control-Allow-Headers: Content-Type, Authorization");


// Allow credentials
header("Access-Control-Allow-Credentials: true");



switch($action) {
    case 'get':
        // 获取单个内容详情
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0) {
            echo json_encode(['error' => '无效的ID']);
            exit;
        }
        
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT * FROM contents WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode($result->fetch_assoc());
        } else {
            echo json_encode(['error' => '内容不存在']);
        }
        break;
        
    case 'list':
        // 列出内容，支持分页和筛选
        $conn = getDBConnection();
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $platform = isset($_GET['platform']) ? $_GET['platform'] : '';
        $userEmail = isset($_GET['user_email']) ? $_GET['user_email'] : '';
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 25;
        
        // 计算总记录数
        $countSql = "SELECT COUNT(*) as total FROM contents";
        $where = [];
        if (!empty($keyword)) {
            $keyword = $conn->real_escape_string($keyword);
            $where[] = "(title LIKE '%$keyword%' OR keywords LIKE '%$keyword%')";
        }
        if ($status !== '') {
            $where[] = "status = " . intval($status);
        }
        if (!empty($platform)) {
            $platform = $conn->real_escape_string($platform);
            $where[] = "platform = '$platform'";
        }
        if (!empty($userEmail)) {
            $userEmail = $conn->real_escape_string($userEmail);
            $where[] = "user_email = '$userEmail'";
        }
        
        if (!empty($where)) {
            $countSql .= " WHERE " . implode(" AND ", $where);
        }
        $countResult = $conn->query($countSql);
        $total = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($total / $perPage);
        
        // 获取当前页数据
        $sql = "SELECT * FROM contents";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY created_at DESC LIMIT " . (($page - 1) * $perPage) . ", $perPage";
        $result = $conn->query($sql);
        
        $contents = [];
        while($row = $result->fetch_assoc()) {
            $contents[] = $row;
        }
        
        echo json_encode([
            'contents' => $contents,
            'pagination' => [
                'total' => $total,
                'total_pages' => $totalPages,
                'current_page' => $page,
                'per_page' => $perPage
            ]
        ]);
        break;
        
    case 'add':
        // 添加新内容
        $data = json_decode(file_get_contents('php://input'), true);
        
        // 验证必填字段
        if (empty($data['title'])) {
            echo json_encode(['error' => '标题不能为空']);
            exit;
        }
        
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO contents (
            title, keywords, content, status, platform, user_email, published_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        // 处理发布时间
        $published_at = null;
        if ($data['status'] == 1 && empty($data['published_at'])) {
            // 如果状态是已发布且未指定发布时间，则使用当前时间
            $published_at = date('Y-m-d H:i:s');
        } else if (!empty($data['published_at'])) {
            $published_at = $data['published_at'];
        }
        
        // 准备变量以便通过引用传递
        $title = $data['title'];
        $keywords = $data['keywords'] ?? '';
        $content = $data['content'] ?? '';
        $status = $data['status'] ?? 0;
        $platform = $data['platform'] ?? '';
        $userEmail = $data['user_email'] ?? '';
        
        $stmt->bind_param("sssisss", 
            $title, 
            $keywords, 
            $content, 
            $status, 
            $platform, 
            $userEmail,
            $published_at
        );
        
        if($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
        break;
        
    case 'edit':
        // 编辑内容
        $data = json_decode(file_get_contents('php://input'), true);
        
        // 验证必填字段
        if (empty($data['id']) || empty($data['title'])) {
            echo json_encode(['error' => 'ID和标题不能为空']);
            exit;
        }
        
        $conn = getDBConnection();
        
        // 检查内容是否存在
        $checkStmt = $conn->prepare("SELECT status FROM contents WHERE id = ?");
        $checkStmt->bind_param("i", $data['id']);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows == 0) {
            echo json_encode(['error' => '内容不存在']);
            exit;
        }
        
        $currentStatus = $checkResult->fetch_assoc()['status'];
        
        // 处理发布时间
        $publishedAtSql = "";
        if ($data['status'] == 1 && $currentStatus != 1) {
            // 如果当前修改为已发布状态且之前不是已发布状态，则更新发布时间
            $publishedAtSql = ", published_at = NOW()";
        } else if (isset($data['published_at'])) {
            $publishedAtSql = ", published_at = '" . $conn->real_escape_string($data['published_at']) . "'";
        }
        
        $sql = "UPDATE contents SET 
            title = ?, 
            keywords = ?, 
            content = ?, 
            status = ?, 
            platform = ?, 
            user_email = ?
            $publishedAtSql
            WHERE id = ?";
            
        $stmt = $conn->prepare($sql);
        // 准备变量以便通过引用传递
        $title = $data['title'];
        $keywords = $data['keywords'] ?? '';
        $content = $data['content'] ?? '';
        $status = $data['status'] ?? 0;
        $platform = $data['platform'] ?? '';
        $userEmail = $data['user_email'] ?? '';
        $id = $data['id'];
        
        $stmt->bind_param("sssissi",
            $title,
            $keywords,
            $content,
            $status,
            $platform,
            $userEmail,
            $id
        );
        
        if($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
        break;
        
    case 'delete':
        // 删除内容
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0) {
            echo json_encode(['error' => '无效的ID']);
            exit;
        }
        
        $conn = getDBConnection();
        $stmt = $conn->prepare("DELETE FROM contents WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
        break;
        
    case 'import':
        // 导入CSV
        if ($_FILES['csv']['error'] === UPLOAD_ERR_OK) {
            $conn = getDBConnection();
            $file = $_FILES['csv']['tmp_name'];
            $handle = fopen($file, 'r');
            
            // 获取表头映射
            $headers = fgetcsv($handle);
            $headerMap = [
                'ID' => 'id',
                '标题' => 'title',
                '关键字' => 'keywords',
                '内容' => 'content',
                '状态' => 'status',
                '平台' => 'platform',
                '使用者' => 'user_email',
                '内容创建时间' => 'created_at',
                '发布时间' => 'published_at'
            ];

            // 创建字段映射
            $fieldMap = [];
            foreach ($headers as $index => $header) {
                $header = trim($header);
                if (isset($headerMap[$header])) {
                    $fieldMap[$headerMap[$header]] = $index;
                }
            }
            
            // 检查必要字段
            if (!isset($fieldMap['title'])) {
                echo json_encode(['error' => 'CSV文件必须包含标题字段']);
                exit;
            }

            $importCount = 0;
            $errorCount = 0;
            $errors = [];
            
            // 导入数据
            while (($data = fgetcsv($handle)) !== FALSE) {
                try {
                    $fields = [];
                    $values = [];
                    $types = '';
                    
                    // 准备插入语句参数
                    foreach ($fieldMap as $field => $index) {
                        if ($field === 'id') continue; // 跳过ID字段，使用自增
                        
                        if (isset($data[$index]) && trim($data[$index]) !== '') {
                            $fields[] = $field;
                            // 特殊处理状态字段
                            if ($field === 'status') {
                                $statusValue = trim($data[$index]);
                                if(is_numeric($statusValue)) {
                                    $values[] = intval($statusValue);
                                } else {
                                    // 中文状态转换
                                    $statusMap = [
                                        '草稿' => 0,
                                        '已发布' => 1, 
                                        '已下线' => 2
                                    ];
                                    $values[] = $statusMap[$statusValue] ?? 0;
                                }
                                $types .= 'i';
                            } else {
                                $values[] = trim($data[$index]);
                                $types .= 's';
                            }
                        }
                    }

                    if (count($fields) < 1 || !in_array('title', $fields)) continue; // 至少需要标题
                    
                    $placeholders = implode(',', array_fill(0, count($fields), '?'));
                    $sql = "INSERT INTO contents (" . implode(',', $fields) . ") VALUES ($placeholders)";
                    $stmt = $conn->prepare($sql);
                    
                    if ($stmt === false) {
                        throw new Exception($conn->error);
                    }
                    
                    // 创建引用数组来正确绑定参数
                    $bindParams = array($types);
                    foreach ($values as &$value) {
                        $bindParams[] = &$value;
                    }
                    call_user_func_array([$stmt, 'bind_param'], $bindParams);
                    if ($stmt->execute()) {
                        $importCount++;
                    } else {
                        throw new Exception($stmt->error);
                    }
                } catch (Exception $e) {
                    $errorCount++;
                    $errors[] = '第'.($importCount + $errorCount + 1).'行: '.$e->getMessage();
                    continue;
                }
            }

            $result = [
                'success' => true,
                'imported' => $importCount,
                'failed' => $errorCount
            ];
            
            if ($errorCount > 0) {
                $result['errors'] = array_slice($errors, 0, 5); // 最多返回5条错误
            }
            
            echo json_encode($result);
            
            fclose($handle);
        } else {
            echo json_encode(['error' => '文件上传失败']);
        }
        break;
        
    case 'export':
        // 导出CSV
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="contents.csv"');
        // 添加BOM头解决Excel中文乱码
        echo "\xEF\xBB\xBF";
        
        $conn = getDBConnection();
        
        // 支持筛选条件
        $where = [];
        if (!empty($_GET['keyword'])) {
            $keyword = $conn->real_escape_string($_GET['keyword']);
            $where[] = "(title LIKE '%$keyword%' OR keywords LIKE '%$keyword%')";
        }
        if (isset($_GET['status']) && $_GET['status'] !== '') {
            $where[] = "status = " . intval($_GET['status']);
        }
        if (!empty($_GET['platform'])) {
            $platform = $conn->real_escape_string($_GET['platform']);
            $where[] = "platform = '$platform'";
        }
        if (!empty($_GET['user_email'])) {
            $userEmail = $conn->real_escape_string($_GET['user_email']);
            $where[] = "user_email = '$userEmail'";
        }
        
        $sql = "SELECT * FROM contents";
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " ORDER BY created_at DESC";
        
        $result = $conn->query($sql);
        
        $out = fopen('php://output', 'w');
        
        // 输出中文表头
        $chineseHeaders = [
            'id' => 'ID',
            'title' => '标题',
            'keywords' => '关键字',
            'content' => '内容',
            'status' => '状态',
            'platform' => '平台',
            'user_email' => '使用者',
            'created_at' => '内容创建时间',
            'published_at' => '发布时间'
        ];
        
        // 获取字段名并映射为中文
        $fields = [];
        while ($field = $result->fetch_field()) {
            $fields[] = $chineseHeaders[$field->name] ?? $field->name;
        }
        fputcsv($out, $fields);
        
        // 输出数据
        while ($row = $result->fetch_assoc()) {
            // 状态转换为可读文本
            if (isset($row['status'])) {
                switch ($row['status']) {
                    case 0:
                        $row['status'] = '草稿';
                        break;
                    case 1:
                        $row['status'] = '已发布';
                        break;
                    case 2:
                        $row['status'] = '已下线';
                        break;
                }
            }
            
            fputcsv($out, $row);
        }
        
        fclose($out);
        exit;
        break;
        
    case 'get_unpublished':
        // 获取一条未发布的内容（草稿状态）
        $conn = getDBConnection();
        $sql = "SELECT * FROM contents WHERE status = 0 ORDER BY RAND() LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result->num_rows > 0) {
            echo json_encode([
                'success' => true,
                'content' => $result->fetch_assoc()
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '没有找到未发布的内容'
            ]);
        }
        break;
        
    case 'publish':
        // 将指定ID的内容状态改为已发布
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id <= 0) {
            echo json_encode(['error' => '无效的ID']);
            exit;
        }
        
        $conn = getDBConnection();
        
        // 检查内容是否存在
        $checkStmt = $conn->prepare("SELECT id FROM contents WHERE id = ?");
        $checkStmt->bind_param("i", $id);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows == 0) {
            echo json_encode(['error' => '内容不存在']);
            exit;
        }
        
        // 更新状态为已发布，并设置发布时间
        $stmt = $conn->prepare("UPDATE contents SET status = 1, published_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => '内容已成功发布'
            ]);
        } else {
            echo json_encode([
                'error' => '发布失败: ' . $conn->error
            ]);
        }
        break;
        
    default:
        echo json_encode(['error' => '无效操作']);
}
?>