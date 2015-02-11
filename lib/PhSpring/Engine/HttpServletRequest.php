<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace PhSpring\Engine;

use PhSpring\Engine\Adapter\Request;
use PhSpring\Engine\Adapter\RequestInterface;
use Zend_Mail_Message;
use Zend_Mail_Part;
use Zend_Mime;
use Zend_Mime_Decode;
use Zend_Mime_Part;

/**
 * Description of HttpServletRequest
 *
 * @author lobiferi
 */
class HttpServletRequest extends AbstractAdapter implements RequestInterface {

    const PATH_INFO = 'PATH_INFO';
    const HTTP_X_REQUESTED_WITH = 'HTTP_X_REQUESTED_WITH';
    const REQUEST_METHOD = 'REQUEST_METHOD';
    const HTTPS = 'HTTPS';
    const REQUEST_URI = 'REQUEST_URI';

    protected $defaultAdapter = Request::class;

    public function getParam($key, $default = null) {
        return $this->getAdapter()->getParam($key, $default);
    }

    public function getParams() {
        return $this->getAdapter()->getParams();
    }

    public function getMethod() {
        static $method;
        if (!$method) {
            $method = $this->getAdapter()->getMethod();
            if ($method == 'POST' && empty(filter_input_array(INPUT_POST)) && $this->getAdapter()->getServer('CONTENT_LENGTH')) {
                $method = 'PUT';
                $boundary;
                preg_match('/boundary=(.*)/', $this->getServer('CONTENT_TYPE'), $boundary);
                $boundary = $boundary[1];
                $handle = fopen('php://input', 'rb');
                while ($part = $this->getPart($handle, $boundary)) {
                    $part = new HttpServletRequestPart($part);
                    if ($part->getType() !== null) {
                        $this->addFile($part);
                    } else {
                        $this->setParam($part->getName(), $part->getBody());
                    }
                }
            }
        }
        return $method;
    }

    private function getPart($handle, $boundary) {
        if (feof($handle)) {
            return false;
        }
        $header = '';
        while (trim($line = fgets($handle))) {
            if (trim($line) != '--' . $boundary) {
                $header .= $line;
            }
        }
        $header.="\r\n\r\n";
        Zend_Mime_Decode::splitMessage($header, $headers, $null);
        $body = '';
        while (!feof($handle)) {
            $line = fgets($handle);
            if ($line === false) {
                if (feof($handle)) {
                    break;
                }
                /**
                 * @see Zend_Mail_Exception
                 */
                require_once 'Zend/Mail/Exception.php';
                throw new Zend_Mail_Exception('error reading file');
            }

            if (trim($line) == '--' . $boundary || trim($line) == '--' . $boundary . '--') {
                break;
            }
            $body.=$line;
        }
        return array('header' => $headers, 'body' => trim($body));
    }

    private function addFile($part) {
        $name = explode('.', preg_replace('/\.\./', '.', preg_replace('/[\[\]]/', '.', trim($part->getName(), '"'))));
        $rootName = $name[0];
        $_FILES[$rootName] = empty($_FILES[$rootName]) ? array() : $_FILES[$rootName];
        $key = array_key_exists(1, $name) && $name[1]!==''? $name[1] : null;
        $this->setFilesParam($_FILES[$rootName], 'name', $key, $part->getFileName());
        $this->setFilesParam($_FILES[$rootName], 'type', $key, $part->getType());

        $tmp_dir = ini_get('upload_tmp_dir') ? ini_get('upload_tmp_dir') : sys_get_temp_dir();
        $tempPath = tempnam($tmp_dir, 'PhSpring-PUT-');
        $temp = fopen($tempPath, 'wb');
        fwrite($temp, $part->getBody());
        fclose($temp);

        $this->setFilesParam($_FILES[$rootName], 'tmp_name', $key, $tempPath);
        $this->setFilesParam($_FILES[$rootName], 'error', $key, UPLOAD_ERR_OK);
        $this->setFilesParam($_FILES[$rootName], 'size', $key, filesize($tempPath));
    }

    private function setFilesParam(&$files, $param, $key, $value) {
        $files[$param] = empty($files[$param]) ? array() : $files[$param];
        if ($key === null) {
            $files[$param][] = $value;
        } else {
            $files[$param][$key] = $value;
        }
    }

    public function getServer($key = null, $default = null) {
        return $this->getAdapter()->getServer($key, $default);
    }

    public function isDelete() {
        return $this->getAdapter()->isDelete();
    }

    public function isGet() {
        return $this->getAdapter()->isGet();
    }

    public function isHead() {
        return $this->getAdapter()->isHead();
    }

    public function isOptions() {
        return $this->getAdapter()->isOptions();
    }

    public function isPost() {
        return $this->getAdapter()->isPost();
    }

    public function isPut() {
        return $this->getMethod() === 'PUT';
    }

    public function isSecure() {
        return $this->getAdapter()->isSecure();
    }

    public function isXmlHttpRequest() {
        return $this->getAdapter()->isXmlHttpRequest();
    }

    public function setParam($key, $value) {
        return $this->getAdapter()->setParam($key, $value);
    }

    function parseMime(Zend_Mail_Part $mime) {
        foreach ($mime as $part) {
            if ($part->isMultipart()) {
                $this->parseMime($mime);
            } else {
                var_dump($part->getHeaders());
            }
        }
    }

}

class HttpServletRequestPart {

    private $disposition;
    private $name;
    private $type;
    private $body;
    private $filename;

    public function __construct($part) {
        $this->parseDisposition($part);
        $this->parseType($part);
        $this->parseBody($part);
    }

    private function parseDisposition($part) {
        if (\array_key_exists('content-disposition', $part['header'])) {
            $data = explode(';', $part['header']['content-disposition']);
            foreach ($data as $val) {
                $val = preg_split('/=/', $val);
                if (sizeof($val) === 1) {
                    $this->disposition = trim($val[0]);
                } else {
                    $this->{trim($val[0])} = trim($val[1]);
                }
            }
        }
    }

    private function parseType($part) {
        if (\array_key_exists('content-type', $part['header'])) {
            $data = explode(';', $part['header']['content-type']);
            $this->type = trim($data[0]);
        }
    }

    private function parseBody($part) {
        if (\array_key_exists('body', $part)) {
            $this->body = $part['body'];
        }
    }

    public function getDisposition() {
        return $this->disposition;
    }

    public function getName() {
        return $this->name;
    }

    public function getType() {
        return $this->type;
    }

    public function getBody() {
        return $this->body;
    }

    public function getFileName() {
        return trim($this->filename, '"');
    }

}
