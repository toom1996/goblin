<?php


namespace toom1996;


use toom1996\base\Module;

/**
 * Class BaseYii
 *
 * @author TOOM <1023150697@qq.com>
 * 
 */
class BaseYiiS extends Module
{

    public function coreComponents()
    {
        return [
            'log'          => ['class' => 'yii\log\Dispatcher'],
            'view'         => ['class' => 'yii\web\View'],
            'formatter'    => ['class' => 'yii\i18n\Formatter'],
            'i18n'         => ['class' => 'yii\i18n\I18N'],
            'mailer'       => ['class' => 'yii\swiftmailer\Mailer'],
            'urlManager'   => ['class' => 'yii\web\UrlManager'],
            'assetManager' => ['class' => 'yii\web\AssetManager'],
            'security'     => ['class' => 'yii\base\Security'],
        ];
    }

    public static function getAlias($alias)
    {
        if (strpos($alias, '@') !== 0) {
            // not an alias
            return $alias;
        }

        $pos = strpos($alias, '/');
        $root = $pos === false ? $alias : substr($alias, 0, $pos);

        if (isset(static::$aliases[$root])) {
            if (is_string(static::$aliases[$root])) {
                return $pos === false ? static::$aliases[$root] : static::$aliases[$root] . substr($alias, $pos);
            }

            foreach (static::$aliases[$root] as $name => $path) {
                if (strpos($alias . '/', $name . '/') === 0) {
                    return $path . substr($alias, strlen($name));
                }
            }
        }

        if ($throwException) {
            throw new InvalidArgumentException("Invalid path alias: $alias");
        }

        return false;
    }
    
}