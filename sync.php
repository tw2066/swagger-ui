#!/usr/bin/env php
<?php

$swaggerDir = '~/WWW/Web/swagger-ui';
$mySwaggerDir = '~/WWW/Web/my-swagger-ui';

shell_exec("cd {$swaggerDir} && git checkout master && git pull");
$tags = shell_exec("cd {$swaggerDir} && git tag");
$tagArr = explode(PHP_EOL, $tags);

$tagArr = array_filter($tagArr, function ($value) {
    return startsWith($value, 'v4');
});

$myTags = shell_exec("cd {$mySwaggerDir} && git tag");
$myTagArr = explode(PHP_EOL, $myTags);

$insertTagArr = array_diff($tagArr, $myTagArr);
if (empty($insertTagArr)) {
    var_dump('没有需要打包的tag');
    return;
}
foreach ($insertTagArr as $tag) {
    // checkout
    shell_exec("cd {$swaggerDir} && git checkout {$tag}");
    // delete
    shell_exec("rm -rf {$mySwaggerDir}/dist/*");
    // cp
    shell_exec("cp {$swaggerDir}/dist/* {$mySwaggerDir}/dist/");
    $resul = shell_exec("cd {$mySwaggerDir} && git add . && git commit -m '{$tag}'");
    $resul = shell_exec("cd {$mySwaggerDir} && git tag {$tag}");
    var_dump("tag:{$tag}");
    die();
}

//shell_exec("cd {$mySwaggerDir} && git push --tags");

function startsWith($haystack, $needles)
{
    foreach ((array) $needles as $needle) {
        if ($needle !== '' && substr($haystack, 0, strlen($needle)) === (string) $needle) {
            return true;
        }
    }

    return false;
}
