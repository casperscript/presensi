<?php

/**
 * frameduzPHP v4
 *
 * @Author  	: M. Hanif Afiatna <hanif.softdev@gmail.com>
 * @Since   	: version 4.1.0
 * @Date		: 10 April 2017
 * @package 	: core system
 * @Description : 
 */

namespace system;

class Files {

    private $arrMimes = array(
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpe' => 'image/jpeg',
        'gif' => 'image/gif',
        'png' => 'image/png',
        'bmp' => 'image/bmp',
        'tif' => 'image/tiff',
        'tiff' => 'image/tiff',
        'ico' => 'image/x-icon',
        'asf' => 'video/asf',
        'asx' => 'video/asf',
        'wax' => 'video/asf',
        'wmv' => 'video/asf',
        'wmx' => 'video/asf',
        'avi' => 'video/avi',
        'divx' => 'video/divx',
        'flv' => 'video/x-flv',
        'mov' => 'video/quicktime',
        'qt' => 'video/quicktime',
        'mpeg' => 'video/mpeg',
        'mpg' => 'video/mpeg',
        'mpe' => 'video/mpeg',
        'txt' => 'text/plain',
        'asc' => 'text/plain',
        'c' => 'text/plain',
        'cc' => 'text/plain',
        'h' => 'text/plain',
        'csv' => 'text/csv',
        'tsv' => 'text/tab-separated-values',
        'rtx' => 'text/richtext',
        'css' => 'text/css',
        'htm' => 'text/html',
        'html' => 'text/html',
        'mp3' => 'audio/mpeg',
        'm4a' => 'audio/mpeg',
        'm4b' => 'audio/mpeg',
        'mp4' => 'video/mp4',
        'm4v' => 'video/mp4',
        'ra' => 'audio/x-realaudio',
        'ram' => 'audio/x-realaudio',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'oga' => 'audio/ogg',
        'ogv' => 'video/ogg',
        'mid' => 'audio/midi',
        'midi' => 'audio/midi',
        'wma' => 'audio/wma',
        'mka' => 'audio/x-matroska',
        'mkv' => 'video/x-matroska',
        'rtf' => 'application/rtf',
        'js' => 'application/javascript',
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/msword',
        'pot' => 'application/vnd.ms-powerpoint',
        'pps' => 'application/vnd.ms-powerpoint',
        'ppt' => 'application/vnd.ms-powerpoint',
        'pptx' => 'application/vnd.ms-powerpoint',
        'ppam' => 'application/vnd.ms-powerpoint',
        'pptm' => 'application/vnd.ms-powerpoint',
        'sldm' => 'application/vnd.ms-powerpoint',
        'ppsm' => 'application/vnd.ms-powerpoint',
        'potm' => 'application/vnd.ms-powerpoint',
        'wri' => 'application/vnd.ms-write',
        'xla' => 'application/vnd.ms-excel',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.ms-excel',
        'xlt' => 'application/vnd.ms-excel',
        'xlw' => 'application/vnd.ms-excel',
        'xlam' => 'application/vnd.ms-excel',
        'xlsb' => 'application/vnd.ms-excel',
        'xlsm' => 'application/vnd.ms-excel',
        'xltm' => 'application/vnd.ms-excel',
        'mdb' => 'application/vnd.ms-access',
        'mpp' => 'application/vnd.ms-project',
        'docm' => 'application/vnd.ms-word',
        'dotm' => 'application/vnd.ms-word',
        'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml',
        'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml',
        'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml',
        'potx' => 'application/vnd.openxmlformats-officedocument.presentationml',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml',
        'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml',
        'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml',
        'onetoc' => 'application/onenote',
        'onetoc2' => 'application/onenote',
        'onetmp' => 'application/onenote',
        'onepkg' => 'application/onenote',
        'swf' => 'application/x-shockwave-flash',
        'class' => 'application/java',
        'tar' => 'application/x-tar',
        'zip' => 'application/zip',
        'gz' => 'application/x-gzip',
        'gzip' => 'application/x-gzip',
        'exe' => 'application/x-msdownload',
        // openoffice formats
        'odt' => 'application/vnd.oasis.opendocument.text',
        'odp' => 'application/vnd.oasis.opendocument.presentation',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'odg' => 'application/vnd.oasis.opendocument.graphics',
        'odc' => 'application/vnd.oasis.opendocument.chart',
        'odb' => 'application/vnd.oasis.opendocument.database',
        'odf' => 'application/vnd.oasis.opendocument.formula',
        // wordperfect formats
        'wp' => 'application/wordperfect',
        'wpd' => 'application/wordperfect',
        // php formats
        'php' => 'application/x-httpd-php',
        'php4' => 'application/x-httpd-php',
        'php3' => 'application/x-httpd-php',
        'phtml' => 'application/x-httpd-php',
        'phps' => 'application/x-httpd-php-source',
    );

    public function __construct() {
        $this->fileName = '';
        $this->fileType = '';
        $this->fileDownload = '';
        $this->folder = '';
        $this->maxSize = 0;
        $this->session = false;
        $this->fileInfo = array();
        $this->getUrl = new Url();
        $this->project = $this->getUrl->ProjectName;
        $this->sessionName = $this->getUrl->mainConfig['project'][$this->project]['session'];
    }

    public function getFileInfo() {
        return $this->fileInfo;
    }

    public function getMimeTypes($file_type = '') {
        $mimes = array();
        if (empty($file_type)) :
            return array();
        endif;
        $exts = explode('|', $file_type);
        foreach ($exts as $ext) {
            array_push($mimes, $this->arrMimes[$ext]);
        }
        return implode(',', $mimes);
    }

    public function upload($file, $opt) {
        foreach ($opt as $key => $value) :
            $this->$key = $value;
        endforeach;
        $dir_upload = $this->folder;
        $dst_name = $this->fileName;
        $file_dst = $dir_upload . '/' . $dst_name;
        $this->fileInfo['OriginalFile'] = $file['name'];
        $this->fileInfo['UploadFile'] = $dst_name;
        $this->fileInfo['errorMsg'] = 'File telah diupload';
        $this->fileInfo['status'] = 'error';

        if (!is_dir($dir_upload)) {
            $this->fileInfo['errorMsg'] = 'Folder tujuan tidak ditemukan';
        } else if (!preg_match('!\.(' . $this->fileType . ')$!i', $file['name'])) {
            $this->fileInfo['errorMsg'] = 'Format File tidak diijinkan';
        } else if ($this->maxSize > 0 && $file['size'] > $this->maxSize) {
            $this->fileInfo['errorMsg'] = 'Ukuran file terlalu besar';
        } else if ($this->session == true) {
            if (isset($_SESSION[$this->sessionName])) {
                if (move_uploaded_file($file['tmp_name'], $file_dst)) {
                    $this->fileInfo['status'] = 'success';
                }
            } else {
                $this->fileInfo['errorMsg'] = 'Upload file tidak diijinkan';
            }
        } else if ($this->session === false) {
            if (move_uploaded_file($file['tmp_name'], $file_dst)) {
                $this->fileInfo['status'] = 'success';
            }
        }

        return $this->fileInfo;
    }

    public function download($opt) {
        foreach ($opt as $key => $value) :
            $this->$key = $value;
        endforeach;
        $dir_upload = $this->folder;
        $dst_name = $this->fileName;
        $file_dst = $dir_upload . '/' . $dst_name;
        $file_ext = explode('.', $this->fileName)[1];
        $file_mime = $this->arrMimes[$file_ext];
        header('content-disposition: attachment; filename=' . $this->fileDownload);
        header('content-type: ' . $file_mime);
        header('content-length: ' . filesize($file_dst));
        readfile($file_dst);
    }

    public function delete($opt) {
        foreach ($opt as $key => $value) :
            $this->$key = $value;
        endforeach;
        $dir_upload = $this->folder;
        $dst_name = $this->fileName;
        $file_dst = $dir_upload . '/' . $dst_name;
        if (file_exists($file_dst)) {
            unlink($file_dst);
            return true;
        } else {
            return false;
        }
    }

}