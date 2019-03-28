<?php

namespace TextDetection\lib;

require_once __DIR__ . '/Tree.php';
require_once __DIR__ . '/Cacher.php';

class TreeHelper {

    /**
     * 字符编码
     */
    const ENCODING = 'utf-8';

    /**
     * 敏感词树缓存ID
     */
    const CACHE_ID = 'cache_tree';

    /**
     * 敏感词字典文件路径
     * @var string
     */
    private $dictFile = __DIR__ . '/../dict/dict.txt';

    /**
     * 敏感词树
     * @var Tree
     */
    private $tree;

    public function __construct() {
        $this->tree = new Tree();
    }

    /**
     * 获取敏感词树(缓存模式)
     *
     * @return tree
     */
    public function getTreeByCache() {
        $cacher = new Cacher();
        if ($cacher->has(self::CACHE_ID)) {
            return $cacher->get(self::CACHE_ID);
        }
        // 生成敏感词树
        $tree = $this->getTreeByFile();
        // 缓存
        $cacher->save(self::CACHE_ID, $tree);

        return $tree;
    }

    /**
     * 构建敏感词树(文件模式)
     *
     * @param string $file 文件
     * @return tree
     */
    public function getTreeByFile($file = NULL) {
        if (is_null($file)) {
            $file = $this->dictFile;
        } elseif (! file_exists($file)) {
            return $this->tree;
        }

        return $this->getTree($this->getFileContent($file));
    }

    /**
     * 构建敏感词树(数组模式)
     *
     * @param array|\Generator $words 敏感词数组
     * @return tree
     */
    public function getTree($words) {
        if (empty($words)) {
            return $this->tree;
        }

        foreach ($words as $word) {
            $this->addWord(trim($word));
        }

        return $this->tree;
    }

    /**
     * 获取文件内容
     *
     * @param string $file
     * @return \Generator
     */
    private function getFileContent($file) {
        $fp = fopen($file, 'r');
        while (! feof($fp)) {
            yield fgets($fp);
        }
        fclose($fp);
    }

    /**
     * 将单词加入树中
     *
     * @param string $word 单词
     */
    private function addWord($word) {
        if (empty($word)) {
            return;
        }

        $tree = $this->tree;
        $len = mb_strlen($word, self::ENCODING);
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($word, $i, 1, self::ENCODING);

            // 获取子节点树结构
            $newTree = $tree->get($char);

            if ($newTree === NULL) {
                // 添加到集合
                $newTree = new tree();
                $tree->set($char, $newTree);
            }
            $tree = $newTree;

            // 到达最后一个节点
            if ($i === $len - 1) {
                $tree->setEnd();
            }
        }

        return;
    }
}
