<?php

namespace Ttskch;

class AssetResolver
{
    /**
     * @var array
     */
    private $categoryBasedConfig;

    /**
     * @var array
     */
    private $tagBasedConfig;

    const DEFAULT_CSS_PATH = 'css/post/default.css';
    const DEFAULT_JS_PATH = 'js/post/default.js';

    public function __construct(array $config)
    {
        $this->categoryBasedConfig = [];
        $this->tagBasedConfig = [];

        foreach ($config as $key => $value) {
            if (strpos($key, '#') === 0) {
                $this->tagBasedConfig[$key] = $value;
            } else {
                $this->categoryBasedConfig[$key] = $value;
            }
        }

        // deeper category should win.
        krsort($this->categoryBasedConfig, SORT_NATURAL);
    }

    /**
     * @param string $category
     * @param array $tags
     * @return array
     */
    public function getAssetPaths($category, array $tags)
    {
        $assetPaths = [
            'css' => self::DEFAULT_CSS_PATH,
            'js' => self::DEFAULT_JS_PATH,
        ];

        foreach ($this->categoryBasedConfig as $matcher => $paths) {
            if (preg_match(sprintf('#^%s#', $matcher), $category)) {
                $assetPaths = array_merge($assetPaths, $paths);
                break;  // deeper category should match early.
            }
        }

        foreach ($this->tagBasedConfig as $matcher => $paths) {
            if (in_array(substr($matcher, 1), $tags)) {
                $assetPaths = array_merge($assetPaths, $paths);
                break;
            }
        }

        return $assetPaths;
    }
}
