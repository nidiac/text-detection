<?php

namespace TextDetection\lib;

/**
 * 日志类
 */
class Logger {

    /**
     * 日志存放目录
     * @var string
     */
    private $path = __DIR__ . '/../log';

    /**
     * 错误信息记录
     *
     * @param $message
     * @return bool
     */
    public function error($message) {
        if (empty($message)) {
            return FALSE;
        }

        $this->handle($message, 'error');
    }

    /**
     * 详细信息记录
     *
     * @param $message
     * @return bool
     */
    public function info($message) {
        if (empty($message)) {
            return FALSE;
        }

        $this->handle($message, 'info');
    }

    /**
     * 日志处理
     *
     * @param $message
     * @param $level
     */
    private function handle($message, $level) {
        $message = date('Y-m-d H:i:s') . ' [' . $level . '] ' . $message . PHP_EOL;

        $this->write($message);
    }

    /**
     * 日志写入
     *
     * @param $message
     */
    private function write($message) {
        $file_name = date('Y-m-d') . '.log';
        $file_path = $this->path . '/' . $file_name;

        file_put_contents($file_path, $message, FILE_APPEND);
    }
}
