<?php
class File {
    public static function getMineType($fileName) {
        $mimeMap = array(
            'jpg'   => 'image/jpeg',
            'gif'   => 'image/gif',
            'png'   => 'image/png',
            'flv'   => 'flv-application/octet-stream',
            'swf'   => 'application/x-shockwave-flash'
        );
        
        $fileExt = strtolower(FileUtils::getExtType($fileName));
        return isset($mimeMap[$fileExt]) ? $mimeMap[$fileExt] : 'unkown';
    }
    
    public static function getExtType($fileName) {
        return substr($fileName, strrpos($fileName, '.') + 1);
    }
    
    public static function makeDir($dirName) {
        $dirName = str_replace("\\","/", $dirName);
        $dirNames = explode('/', $dirName);
        $total = count($dirNames) ;
        $temp = '';
        for($i=0; $i<$total; $i++) {
            $temp .= $dirNames[$i].'/';
            if (!is_dir($temp)) {
                $oldmask = umask(0);
                if (!mkdir($temp, 0777)) exit("不能建立目录 $temp"); 
                umask($oldmask);
            }
        }
        return true;
    }
    
    /**
     * 
     * @param type $url
     * @param string $savePath
     * @return string
     */
    public static function download($url, $savePath, $fileName = '') {
        FileUtils::makeDir($savePath);
        
        if(substr($savePath, -1) != DS) {
            $savePath .= DS;
        }
        
        $newFileName    = '';
        if($fileName == '') {
            $newFileName = $savePath . basename($url);
        }
        else {
            $newFileName = $savePath . $fileName;
        }
        
        $srcFileHandle  = fopen($url, "rb");
        
        if ($srcFileHandle) {
            $newFileHandle   = fopen ($newFileName, "wb");
            
            if ($newFileHandle) {
                while(!feof($srcFileHandle)) {
                    if(fwrite($newFileHandle, fread($srcFileHandle, 1024), 1024) === false) {
                        return false;
                    }
                }
            }
        }
        
        if ($srcFileHandle) fclose($srcFileHandle);
        if ($newFileHandle) fclose($newFileHandle);
        
        return $newFileName;
    }
    
    public static function getUrlInfo($url) {
        $fileInfos  = array('exist' => false, 'size' => 0, 'fileType' => '');
        $urlAry     = parse_url($url);
        $fp         = @fsockopen($urlAry['host'], empty($urlAry['port']) ? 80 : $urlAry['port'], $error);
        if($fp) {
            fputs($fp, "GET " . (empty($urlAry['path']) ? '/' : $urlAry['path']) . " HTTP/1.1\r\n");
            fputs($fp, "Host: $urlAry[host]\r\n\r\n");
            while(!feof($fp)) {
                $tmp = fgets($fp);
                
                if(trim($tmp) == '') {
                    break;
                }
                else if(preg_match('/Content-Length:(.*)/si', $tmp, $arr)) {
                    $fileInfos['size'] = intval(trim($arr[1]));
                }
                else if(preg_match('|HTTP\/[0-9]\.[0-9] (\d+)|si', $tmp, $arr)) {
                    $fileInfos['exist'] = $arr[1] == '200' ? true : false;
                }
                else if(preg_match('/Content-Type:(.*)/si', $tmp, $arr)) {
                    $fileInfos['fileType'] = trim($arr[1]);
                }
            }
        }
        
        return $fileInfos;
    }
}
?>
