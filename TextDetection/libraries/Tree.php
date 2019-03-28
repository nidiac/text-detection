<?php

namespace TextDetection\lib;

/**
 * 字典树
 */
class Tree {

    /**
     * 数据类型-树
     * @var array
     */
    private $tree = [];

    public function __construct() {
        $this->tree = [
            'end' => FALSE,
        ];
    }

    /**
     * 获取键值
     *
     * @param string $key 键名
     * @return mixed|null
     */
    public function get($key) {
        if (array_key_exists($key, $this->tree)) {
            return $this->tree[$key];
        }

        return NULL;
    }

    /**
     * 设置键值
     *
     * @param string $key 键名
     * @param string|int|array $value 键值
     * @return $this
     */
    public function set($key, $value) {
        $this->tree[$key] = $value;

        return $this;
    }

    /**
     * 将结点设置为叶子
     *
     * @return $this
     */
    public function setEnd() {
        $this->tree['end'] = TRUE;

        return $this;
    }

    /**
     * 判断该结点是否为叶子
     *
     * @return bool
     */
    public function isEnd() {
        return $this->tree['end'];
    }
}
