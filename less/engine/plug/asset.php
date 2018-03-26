<?php

Asset::_('.less', function($value, $key, $attr) {
    extract($value);
    $state = Extend::state('asset.less');
    $state_asset = Extend::state('asset');
    if ($path !== false) {
        $minify = !empty($state['less']['compress']);
        $path_css = str_replace([
            DS . 'less' . DS,
            DS . basename($path) . X,
            X
        ], [
            DS . 'css' . DS,
            DS . Path::N($path) . ($minify ? '.min' : "") . '.css',
            ""
        ], $path . X);
        $t_less = filemtime($path);
        $t_css = file_exists($path_css) ? filemtime($path_css) : 0;
        if ($t_less > $t_css) {
            $less = new Less_Parser($state['less']);
            $css = $less->parseFile($path)->getCss();
            if ($minify && Extend::exist('minify')) {
                $css = Minify::css($css); // Optimize where possible!
            }
            File::write($css)->saveTo($path_css);
        }
        return HTML::unite('link', false, Anemon::extend($attr, [
            'href' => __replace__($state_asset['url'], [To::url($path_css), $t_css ?: $_SERVER['REQUEST_TIME']]),
            'rel' => 'stylesheet'
        ]));
    }
    return '<!-- ' . $key . ' -->';
});