<?php
/**
 * Creative Elements - Elementor based PageBuilder
 *
 * @author    WebshopWorks, Elementor
 * @copyright 2019-2020 WebshopWorks.com & Elementor.com
 * @license   https://www.gnu.org/licenses/gpl-3.0.html
 */

namespace CE;

defined('_PS_VERSION_') or die;

class PostCssFile
{
    const FILE_BASE_DIR = 'modules/creativeelements/views/css/ce';
    // %s: Base folder; %s: file prefix; %d: post_id
    const FILE_NAME_PATTERN = '%s/%s%s.css';
    const FILE_PREFIX = '';

    const CSS_STATUS_FILE = 'file';
    const CSS_STATUS_INLINE = 'inline';
    const CSS_STATUS_EMPTY = 'empty';

    const META_KEY_CSS = '_elementor_css';

    /*
     * @var int
     */
    protected $post_id;

    protected $is_built_with_elementor = true;

    protected $path;

    protected $url;

    protected $css = '';

    protected $fonts = array();

    /**
     * @var Stylesheet
     */
    protected $stylesheet_obj;

    protected $_columns_width;

    public static function addControlRules(Stylesheet $stylesheet, array $control, array $controls_stack, callable $value_callback, array $placeholders, array $replacements)
    {
        $value = call_user_func($value_callback, $control);

        if (null === $value) {
            return;
        }

        foreach ($control['selectors'] as $selector => $css_property) {
            try {
                $output_css_property = preg_replace_callback(
                    '/\{\{(?:([^.}]+)\.)?([^}]*)}}/',
                    function ($matches) use ($control, $value_callback, $controls_stack, $value, $css_property) {
                        $parser_control = $control;

                        $value_to_insert = $value;

                        if (!empty($matches[1])) {
                            $parser_control = $controls_stack[$matches[1]];

                            $value_to_insert = call_user_func($value_callback, $parser_control);
                        }

                        $control_obj = Plugin::instance()->controls_manager->getControl($parser_control['type']);

                        $parsed_value = $control_obj->getStyleValue(\Tools::strtolower($matches[2]), $value_to_insert);

                        if ('' === $parsed_value) {
                            throw new \Exception();
                        }

                        return $parsed_value;
                    },
                    $css_property
                );
            } catch (\Exception $e) {
                return;
            }

            if (!$output_css_property) {
                continue;
            }

            $device_pattern = '/^\(([^\)]+)\)/';

            preg_match($device_pattern, $selector, $device_rule);

            if ($device_rule) {
                $selector = preg_replace($device_pattern, '', $selector);

                $device_rule = $device_rule[1];
            }

            $parsed_selector = str_replace($placeholders, $replacements, $selector);

            $device = $device_rule;

            if (!$device) {
                $device = !empty($control['responsive']) ? $control['responsive'] : ElementBase::RESPONSIVE_DESKTOP;
            }

            $stylesheet->addRules($parsed_selector, $output_css_property, $device);
        }
    }

    public function __construct($post_id)
    {
        $this->post_id = $post_id;

        // Check if it's an Elementor post
        $db = Plugin::instance()->db;

        $data = $db->getPlainEditor($post_id);
        $edit_mode = $db->getEditMode($post_id);

        $this->is_built_with_elementor = (!empty($data) && 'builder' === $edit_mode);

        if (!$this->is_built_with_elementor) {
            return;
        }

        $this->setPathAndUrl();
        $this->initStylesheet();
    }

    public function update()
    {
        if (!$this->isBuiltWithElementor()) {
            return;
        }

        $this->parseElementsCss();

        $meta = array(
            'time' => time(),
            'fonts' => array_unique($this->fonts),
        );

        if (empty($this->css)) {
            $this->delete();

            $meta['status'] = self::CSS_STATUS_EMPTY;
            $meta['css'] = '';
        } else {
            $file_created = false;

            if (is_writable(dirname($this->path))) {
                $file_created = file_put_contents($this->path, $this->css);
            }

            if ($file_created) {
                $meta['status'] = self::CSS_STATUS_FILE;
            } else {
                $meta['status'] = self::CSS_STATUS_INLINE;
                $meta['css'] = $this->css;
            }
        }

        $this->updateMeta($meta);
    }

    public function delete()
    {
        if (file_exists($this->path)) {
            unlink($this->path);
        }
    }

    public function enqueue()
    {
        if (!$this->isBuiltWithElementor()) {
            return;
        }

        $meta = $this->getMeta();
        $frontend = Plugin::instance()->frontend;

        if (self::CSS_STATUS_EMPTY === $meta['status']) {
            return;
        }

        // First time after clear cache and etc.
        if ('' === $meta['status']) {
            $this->update();

            $meta = $this->getMeta();
        }

        if (self::CSS_STATUS_INLINE === $meta['status']) {
            $css = \CESmarty::sprintf(_CE_TEMPLATES_ . 'admin/admin.tpl', 'ce_style', $meta['css']);
        } else {
            $css = \CESmarty::sprintf(_CE_TEMPLATES_ . 'admin/admin.tpl', 'ce_stylesheet', esc_attr("{$this->url}?{$meta['time']}"), 'all');
        }

        Helper::$enqueue_css[] = $css;

        // Handle fonts
        if (!empty($meta['fonts'])) {
            foreach ($meta['fonts'] as $font) {
                $frontend->addEnqueueFont($font);
            }
        }
    }

    public function isBuiltWithElementor()
    {
        return $this->is_built_with_elementor;
    }

    /**
     * @return int
     */
    public function getPostId()
    {
        return $this->post_id;
    }

    public function getElementUniqueSelector(ElementBase $element)
    {
        return '.elementor-' . $this->post_id . ' .elementor-element' . $element->getUniqueSelector();
    }

    public function getCss()
    {
        if (empty($this->css)) {
            $this->parseElementsCss();
        }

        echo $this->css;
    }

    protected function initStylesheet()
    {
        $this->stylesheet_obj = new Stylesheet();

        $breakpoints = Responsive::getBreakpoints();

        $this->stylesheet_obj
            ->addDevice('mobile', $breakpoints['md'] - 1)
            ->addDevice('tablet', $breakpoints['lg'] - 1);
    }

    protected function setPathAndUrl()
    {
        $relative_path = sprintf(self::FILE_NAME_PATTERN, self::FILE_BASE_DIR, self::FILE_PREFIX, $this->post_id);
        $this->path = _PS_ROOT_DIR_ . '/' . $relative_path;
        $this->url = __PS_BASE_URI__ . $relative_path;
    }

    protected function getMeta()
    {
        $meta = get_post_meta($this->post_id, self::META_KEY_CSS, true);

        $defaults = array(
            'status' => '',
        );

        $meta = wp_parse_args($meta, $defaults);

        return $meta;
    }

    protected function updateMeta($meta)
    {
        return update_post_meta($this->post_id, self::META_KEY_CSS, $meta);
    }

    protected function parseElementsCss()
    {
        if (!$this->isBuiltWithElementor()) {
            return;
        }

        $data = Plugin::instance()->db->getPlainEditor($this->post_id);

        $css = '';

        foreach ($data as $element_data) {
            $element = Plugin::instance()->elements_manager->createElementInstance($element_data);
            $this->renderStyles($element);
        }

        $css .= $this->stylesheet_obj;

        if (!empty($this->_columns_width)) {
            $css .= '@media (min-width: 768px) {';
            foreach ($this->_columns_width as $column_width) {
                $css .= $column_width;
            }
            $css .= '}';
        }

        $this->css = $css;
    }

    public function getStylesheet()
    {
        return $this->stylesheet_obj;
    }

    private function addElementStyleRules(ElementBase $element, $controls, $values, $placeholders, $replacements)
    {
        foreach ($controls as $control) {
            if (!empty($control['style_fields'])) {
                foreach ($values[$control['name']] as $field_value) {
                    $this->addElementStyleRules(
                        $element,
                        $control['style_fields'],
                        $field_value,
                        array_merge($placeholders, array('{{CURRENT_ITEM}}')),
                        array_merge($replacements, array('.elementor-repeater-item-' . $field_value['_id']))
                    );
                }
            }

            if (!$element->isControlVisible($control, $values) || empty($control['selectors'])) {
                continue;
            }

            $this->addControlStyleRules($control, $values, $element->getControls(), $placeholders, $replacements);
        }

        foreach ($element->getChildren() as $child_element) {
            $this->renderStyles($child_element);
        }
    }

    private function addControlStyleRules($control, $values, $controls_stack, $placeholders, $replacements)
    {
        self::addControlRules($this->stylesheet_obj, $control, $controls_stack, function ($control) use ($values) {
            $value = ${'this'}->getStyleControlValue($control, $values);

            if (ControlsManager::FONT === $control['type']) {
                ${'this'}->fonts[] = $value;
            }

            return $value;
        }, $placeholders, $replacements);
    }

    private function addPostCss(ElementBase $element, $custom_css)
    {
        $css = trim($custom_css);

        if (empty($css)) {
            return;
        }
        $css = str_replace('selector', $this->getElementUniqueSelector($element), $css);

        // Add a css comment
        $css = sprintf('/* Start custom CSS for %s, class: %s */', $element->getName(), $element->getUniqueSelector()) . $css . '/* End custom CSS */';

        $this->stylesheet_obj->addRawCss($css);
    }

    private function getStyleControlValue($control, $values)
    {
        $value = $values[$control['name']];

        // fix for background image
        if (substr_compare($control['name'], '_image', -6) === 0 && !empty($value['url'])) {
            $value['url'] = Helper::getMediaLink($value['url']);
        }

        if (isset($control['selectors_dictionary'][$value])) {
            $value = $control['selectors_dictionary'][$value];
        }

        if (!is_numeric($value) && !is_float($value) && empty($value)) {
            return null;
        }

        return $value;
    }

    private function renderStyles(ElementBase $element)
    {
        $element_settings = $element->getSettings();

        $this->addElementStyleRules($element, $element->getStyleControls(), $element_settings, array('{{WRAPPER}}'), array($this->getElementUniqueSelector($element)));

        if ('column' === $element->getName()) {
            if (!empty($element_settings['_inline_size'])) {
                $this->_columns_width[] = $this->getElementUniqueSelector($element) . '{width:' . $element_settings['_inline_size'] . '%;}';
            }
        }
        empty($element_settings['custom_css']) or $this->addPostCss($element, $element_settings['custom_css']);

        do_action('elementor/element/parse_css', $this, $element);
    }
}
