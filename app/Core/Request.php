<?php
namespace App\Core;

class Request
{
    public $get;
    public $post;
    public $server;

    public function __construct()
    {
        $this->get = $_GET;
        $this->post = $_POST;
        $this->server = $_SERVER;
    }

    /**
     * دریافت مقدار از متغیرهای GET
     */
    public function getQuery($key, $default = null)
    {
        return isset($this->get[$key]) ? $this->get[$key] : $default;
    }

    /**
     * دریافت مقدار از متغیرهای POST
     */
    public function getPost($key = null, $default = null)
    {
        if ($key === null) {
            return $this->post;
        }
        return isset($this->post[$key]) ? $this->post[$key] : $default;
    }

    /**
     * دریافت مقدار از متغیرهای SERVER
     */
    public function getServer($key, $default = null)
    {
        return isset($this->server[$key]) ? $this->server[$key] : $default;
    }

    /**
     * دریافت متد درخواست (GET, POST, و غیره)
     */
    public function getMethod(): string
    {
        // در محیط CLI، REQUEST_METHOD ممکن است وجود نداشته باشد
        return $this->getServer('REQUEST_METHOD', 'GET');
    }

    /**
     * دریافت پارامترهای query string
     */
    public function getQueryParams(): array
    {
        return $this->get;
    }
}
