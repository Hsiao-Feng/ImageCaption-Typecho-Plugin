<?php
/**
 * 自动添加图片注解
 * 
 * @package Image Caption
 * @author Hsiao Feng
 * @version 1.0.0
 * @link https://hsiaofeng.com
 */

class ImgCaption_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        Typecho_Plugin::factory('Widget_Abstract_Contents')->contentEx = array('ImgCaption_Plugin', 'render');
        Typecho_Plugin::factory('Widget_Archive')->header = array('ImgCaption_Plugin', 'output');
    }

    public static function deactivate()
    {
        return _t('插件已禁用');
    }

    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $customCss = new Typecho_Widget_Helper_Form_Element_Textarea('customCss', null, '', _t('自定义图片注释样式'), _t('在这里输入自定义的 CSS 样式，将应用于图片注释样式。'));
        $form->addInput($customCss);
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form)
    {
    }

    public static function render($text, $widget, $lastResult)
    {
        // 在图片标签下方添加带有用户定义或默认样式的 caption
        $text = preg_replace_callback('/<img(.*?)alt=[\'"]([^\'"]+)[\'"](.*?)>/i', function($match) use ($captionStyle) {
            $altText = htmlspecialchars($match[2]);
            $imageTag = '<img' . $match[1] . $match[3] . '>';
            $captionTag = '<p class="image-caption">' . $altText . '</p>';
            return $imageTag . $captionTag;
        }, $text);

        return $text;
    }

    public static function output()
    {
        $customCss = Typecho_Widget::widget('Widget_Options')->plugin('ImgCaption')->customCss;
        
        // 默认样式
        $defaultCss = 'font-style: italic; text-align: center; color: #999;';

        // 如果用户定义了自定义样式，则使用用户定义的样式，否则使用默认样式
        $captionStyle = !empty($customCss) ? $customCss : $defaultCss;
        echo '<style>.image-caption{' . $captionStyle . '}</style>';
    }
}
