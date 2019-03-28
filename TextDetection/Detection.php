<?php
namespace TextDetection;

require_once __DIR__ . '/libraries/Logger.php';
require_once __DIR__ . '/libraries/TreeHelper.php';

class Detection {

    /**
     * 字符编码
     */
    const ENCODING = 'utf-8';

    /**
     * 日志类
     * @var lib\Logger
     */
    private $logger;

    /**
     * 敏感词树
     * @var lib\Tree
     */
    private $tree;

    public function __construct() {
        $this->logger = new lib\Logger();
        $helper = new lib\TreeHelper();
        $this->tree = $helper->getTreeByCache();
    }

    /**
     * 检测文字中的敏感词
     *
     * @param string $content 待检测内容
     * @param int $type 敏感词命中规则 (0=命中完整的敏感词, 1=命中敏感词中一个字符, 2=命中敏感词中两个字符......)
     * @param int $limit 返回数量限制
     * @param string $filter 是否过滤特殊字符
     * @return array
     */
    public function getHitWords($content, $type = 0, $limit = NULL, $filter = TRUE) {
        if ($filter) {
            // 过滤特殊字符
            $content = preg_replace("/[^0-9a-zA-Z\x{4e00}-\x{9fa5}]/u", '', $content);
        }
        $hits = [];
        if (empty($content)) {
            return $hits;
        }
        $len = mb_strlen($content, self::ENCODING);

        for ($pos = 0; $pos < $len; $pos++) {
            $tree = $this->tree;
            // 字符命中次数
            $hitCharNum = 0;
            // 命中字符串
            $hitWord = '';
            // 命中标志
            $isHit = FALSE;

            for ($start = $pos; $start < $len; $start++) {
                $char = mb_substr($content, $start, 1, self::ENCODING);
                // 获取指定节点树
                $tree = $tree->get($char);
                // 不存在节点树，直接返回
                if ($tree === NULL) {
                    break;
                }
                // 字符命中数+1
                $hitCharNum++;
                // 组织命中字符串
                $hitWord .= $char;
                // 命中规则判断
                if (($type != 0 && $type == $hitCharNum) || $tree->isEnd()) {
                    array_push($hits, $hitWord);
                    // 设置命中标志
                    $isHit = TRUE;
                    break;
                }
            }

            if ($limit !== NULL && count($hits) === $limit) {
                break;
            }

            if ($isHit) {
                // 需匹配内容标志位往后移
                $pos = $pos + $hitCharNum - 1;
            }
        }
        if (! empty($hits)) {
            // 记录日志
            $this->logger->info("内容：{$content}");
            $this->logger->info("敏感词：" . json_encode($hits, JSON_UNESCAPED_UNICODE));
            $this->logger->info('-----------------------------------------------------');
        }

        return $hits;
    }

    /**
     * 替换敏感字字符
     *
     * @param string $content 文本内容
     * @param string $replace 替换字符(当为空格的时候等于删除敏感字符)
     * @param int $type 敏感词命中规则 (0=命中完整的敏感词, 1=命中敏感词中一个字符, 2=命中敏感词中两个字符......)
     * @param string $filter 是否过滤特殊字符
     * @return string
     */
    public function replace($content, $replace = '', $type = 0, $filter = TRUE) {
        if (empty($content)) {
            return $content;
        }

        $hits = $this->getHitWords($content, $type, NULL, $filter);
        // 未检测到敏感词，直接返回
        if (empty($hits)) {
            return $content;
        }

        foreach ($hits as $word) {
            if ($replace !== '') {
                // 构造等长的替换字符串
                $replace = str_pad($replace, mb_strlen($word, self::ENCODING), $replace);
            }
            $content = str_replace($word, $replace, $content);
        }

        return $content;
    }

    /**
     * 被检测内容是否合法
     *
     * @param string $content 文本内容
     * @param int $type 敏感词命中规则 (0=命中完整的敏感词, 1=命中敏感词中一个字符, 2=命中敏感词中两个字符......)
     * @param string $filter 是否过滤特殊字符
     * @return bool
     */
    public function isValid($content, $type = 0, $filter = TRUE) {
        if (empty($content)) {
            return TRUE;
        }
        $hits = $this->getHitWords($content, $type, 1, $filter);

        return empty($hits);
    }
}
