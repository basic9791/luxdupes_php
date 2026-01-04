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
    case 'getActiveUser':
        // 获取一条未禁用的用户数据
        $conn = getDBConnection();
        $sql = "SELECT * FROM users WHERE status = 0 LIMIT 1";
        $result = $conn->query($sql);
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'user' => $user
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '未找到未禁用的用户'
            ]);
        }
        break;
    case 'get':
        $id = $_GET['id'];
        $conn = getDBConnection();
        $result = $conn->query("SELECT * FROM users WHERE id=$id");
        echo json_encode($result->fetch_assoc());
        break;
        
    case 'list':
        $conn = getDBConnection();
        $keyword = isset($_GET['keyword']) ? $_GET['keyword'] : '';
        $status = isset($_GET['status']) ? $_GET['status'] : '';
        $page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
        $perPage = isset($_GET['per_page']) ? max(1, intval($_GET['per_page'])) : 25;
        
        // 计算总记录数
        $countSql = "SELECT COUNT(*) as total FROM users";
        $where = [];
        if (!empty($keyword)) {
            $where[] = "username LIKE '%" . $conn->real_escape_string($keyword) . "%'";
        }
        if ($status !== '') {
            $where[] = "status = " . intval($status);
        }
        if (!empty($where)) {
            $countSql .= " WHERE " . implode(" AND ", $where);
        }
        $countResult = $conn->query($countSql);
        $total = $countResult->fetch_assoc()['total'];
        $totalPages = ceil($total / $perPage);
        
        // 获取当前页数据
        $sql = "SELECT * FROM users";
        $where = [];
        if (!empty($keyword)) {
            $where[] = "username LIKE '%" . $conn->real_escape_string($keyword) . "%'";
        }
        if ($status !== '') {
            $where[] = "status = " . intval($status);
        }
        if (!empty($where)) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
        $sql .= " LIMIT " . (($page - 1) * $perPage) . ", $perPage";
        $result = $conn->query($sql);
        
        $users = [];
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        
        echo json_encode([
            'users' => $users,
            'pagination' => [
                'total' => $total,
                'total_pages' => $totalPages,
                'current_page' => $page,
                'per_page' => $perPage
            ]
        ]);
        break;
        $users = [];
        while($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode($users);
        break;
    case 'add':
        $data = json_decode(file_get_contents('php://input'), true);
        $conn = getDBConnection();
        $stmt = $conn->prepare("INSERT INTO users (
            username, email, slogan, avatar, status,
            company_name, job_title, job_start_date, job_end_date,
            school_name, major, minor, degree_type, graduation_year,
            region, region_start_date, region_end_date
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        $stmt->bind_param("ssssissssssssssss", 
            $data['username'], $data['email'], $data['slogan'], $data['avatar'], $data['status'],
            $data['company_name'], $data['job_title'], $data['job_start_date'], $data['job_end_date'],
            $data['school_name'], $data['major'], $data['minor'], $data['degree_type'], $data['graduation_year'],
            $data['region'], $data['region_start_date'], $data['region_end_date']
        );
        if($stmt->execute()) {
            echo json_encode(['success' => true, 'id' => $conn->insert_id]);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
        break;
    case 'edit':
        $data = json_decode(file_get_contents('php://input'), true);
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE users SET 
            username=?, email=?, slogan=?, avatar=?, status=?,
            company_name=?, job_title=?, job_start_date=?, job_end_date=?,
            school_name=?, major=?, minor=?, degree_type=?, graduation_year=?,
            region=?, region_start_date=?, region_end_date=? 
            WHERE id=?");
            
        $stmt->bind_param("ssssissssssssssssi",
            $data['username'], $data['email'], $data['slogan'], $data['avatar'], $data['status'],
            $data['company_name'], $data['job_title'], $data['job_start_date'], $data['job_end_date'],
            $data['school_name'], $data['major'], $data['minor'], $data['degree_type'], $data['graduation_year'],
            $data['region'], $data['region_start_date'], $data['region_end_date'], $data['id']
        );
        if($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
        break;
    case 'ban':
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => '缺少用户ID']);
            break;
        }
        
        $conn = getDBConnection();
        $stmt = $conn->prepare("UPDATE users SET status = 1, banned_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => '用户已成功禁用']);
            } else {
                echo json_encode(['success' => false, 'message' => '未找到指定用户或用户已被禁用']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => '禁用用户失败: ' . $conn->error]);
        }
        break;
        
    case 'delete':
        $id = $_GET['id'];
        $conn = getDBConnection();
        if($conn->query("DELETE FROM users WHERE id=$id")) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => $conn->error]);
        }
        break;
    case 'import':
        if ($_FILES['csv']['error'] === UPLOAD_ERR_OK) {
            $conn = getDBConnection();
            $file = $_FILES['csv']['tmp_name'];
            $handle = fopen($file, 'r');
            
            // 跳过表头
            // fgetcsv($handle);
            
            // 获取表头映射
            $headers = fgetcsv($handle);
            $headerMap = [
                '用户名' => 'username',
                '邮箱' => 'email',
                '个人口号' => 'slogan',
                '头像' => 'avatar',
                '状态' => 'status',
                '公司名称' => 'company_name',
                '职位' => 'job_title',
                '入职日期' => 'job_start_date',
                '结束日期' => 'job_end_date',
                '学校名称' => 'school_name',
                '专业' => 'major',
                '辅修专业' => 'minor',
                '学位类型' => 'degree_type',
                '毕业年份' => 'graduation_year',
                '所在地区' => 'region',
                '开始日期' => 'region_start_date',
                '结束日期' => 'region_end_date'
            ];

            // 创建字段映射
            $fieldMap = [];
            foreach ($headers as $index => $header) {
                $header = trim($header);
                if (isset($headerMap[$header])) {
                    $fieldMap[$headerMap[$header]] = $index;
                }
            }
            // print_r($fieldMap);
            // exit();
            // 检查必要字段
            if (!isset($fieldMap['username']) || !isset($fieldMap['email'])) {
                echo json_encode(['error' => 'CSV文件必须包含用户名和邮箱字段']);
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
                        if (isset($data[$index]) && trim($data[$index]) !== '') {
                            $fields[] = $field;
                            $values[] = trim($data[$index]);
                            $types .= 's';
                        }
                    }

                    if (count($fields) < 2) continue; // 至少需要用户名和邮箱
                    
                    $placeholders = implode(',', array_fill(0, count($fields), '?'));
                    $sql = "INSERT INTO users (" . implode(',', $fields) . ") VALUES ($placeholders)";
                    $stmt = $conn->prepare($sql);
                    
                    if ($stmt === false) {
                        throw new Exception($conn->error);
                    }
                    
                    $stmt->bind_param($types, ...$values);
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
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="users.csv"');
        // 添加BOM头解决Excel中文乱码
        echo "\xEF\xBB\xBF";
        
        $conn = getDBConnection();
        $result = $conn->query("SELECT * FROM users");
        
        $out = fopen('php://output', 'w');
        
        // 输出中文表头
        $chineseHeaders = [
            'id' => 'ID',
            'username' => '用户名',
            'email' => '邮箱',
            'slogan' => '个人口号',
            'avatar' => '头像',
            'created_at' => '创建时间',
            'banned_at' => '禁用时间',
            'status' => '状态',
            'company_name' => '公司名称',
            'job_title' => '职位',
            'job_start_date' => '入职日期',
            'job_end_date' => '结束日期',
            'school_name' => '学校名称',
            'major' => '专业',
            'minor' => '辅修专业',
            'degree_type' => '学位类型',
            'graduation_year' => '毕业年份',
            'region' => '所在地区',
            'region_start_date' => '开始日期',
            'region_end_date' => '结束日期'
        ];
        
        // 获取字段名并映射为中文
        $fields = [];
        while ($field = $result->fetch_field()) {
            $fields[] = $chineseHeaders[$field->name] ?? $field->name;
        }
        fputcsv($out, $fields);
        
        // 输出数据
        while ($row = $result->fetch_assoc()) {
            fputcsv($out, $row);
        }
        
        fclose($out);
        exit;
        break;
    default:
        echo json_encode(['error' => '无效操作']);
}
?>