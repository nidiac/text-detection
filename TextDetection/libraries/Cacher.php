<?php

namespace TextDetection\lib;

/**
 * 缓存文件类
 */
class Cacher {

    /**
     * 缓存存放目录
     * @var string
     */
    private $path = __DIR__ . '/../cache/';

    /**
     * 保存缓存数据
     *
     * @param string $id Cache ID
     * @param mixed $data 待保存的数据
     * @return bool
     */
    public function save($id, $data) {
        return file_put_contents($this->file($id), serialize($data));
    }

    /**
     * 获取指定缓存数据
     *
     * @param string $id Cache ID
     * @return mixed Data on success, FALSE on failure
     */
    public function get($id) {
        if (! $this->has($id)) {
            return FALSE;
        }

        return unserialize(file_get_contents($this->file($id)));
    }

    /**
     * 检测是否存在指定的缓存数据
     *
     * @param string $id Cache ID
     * @return bool
     */
    public function has($id) {
        return file_exists($this->file($id));
    }

    /**
     * 删除缓存数据
     *
     * @param string $id Cache ID
     * @return bool
     */
    public function delete($id) {
        if (! $this->has($id)) {
            return TRUE;
        }

        return unlink($this->file($id));
    }

    /**
     * 获取缓存文件路径
     *
     * @param string $id 缓存ID
     * @return string
     */
    private function file($id) {
        return $this->path . $id;
    }
}
