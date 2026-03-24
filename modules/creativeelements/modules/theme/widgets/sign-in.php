<?php
/**
 * Creative Elements - live Theme & Page Builder
 *
 * @author    WebshopWorks
 * @copyright 2019-2025 WebshopWorks.com
 * @license   One domain support license
 */
namespace CE;

if (!defined('_PS_VERSION_')) {
    exit;
}

use CE\ModulesXThemeXWidgetsXTraitsXNav as NavTrait;

class ModulesXThemeXWidgetsXSignIn extends WidgetBase
{
    use NavTrait;

    const HELP_URL = 'https://docs.webshopworks.com/creative-elements/88-widgets/site-widgets/361-user-menu-widget';

    const REMOTE_RENDER = true;

    public function getName()
    {
        return 'sign-in';
    }

    public function getTitle()
    {
        return __('User Menu');
    }

    public function getIcon()
    {
        return 'eicon-lock-user';
    }

    public function getCategories()
    {
        return ['theme-elements'];
    }

    public function getKeywords()
    {
        return ['login', 'sign', 'account', 'logout', 'user menu'];
    }

    public function getVisitorOptions()
    {
        return [
            'authentication' => __('Sign in', 'Shop.Theme.Actions'),
            'password' => __('Forgot your password', 'Shop.Navigation'),
            'register' => __('Create an account', 'Shop.Theme.Customeraccount'),
            'guest-tracking' => __('Guest tracking', 'Shop.Navigation'),
            'custom' => __('Custom URL'),
        ];
    }

    public function getCustomerOptions()
    {
        $pages = [
            'my-account' => __('My account', 'Shop.Navigation'),
            'identity' => __('Personal Information', 'Shop.Theme.Checkout'),
            'addresses' => __('Addresses', 'Shop.Navigation'),
            'address' => __('New address', 'Shop.Theme.Customeraccount'),
        ];
        if (!\Configuration::get('PS_CATALOG_MODE')) {
            $pages['history'] = __('Order history', 'Shop.Navigation');
            $pages['order-slip'] = __('Credit slip', 'Shop.Navigation');
            \Configuration::get('PS_CART_RULE_FEATURE_ACTIVE')
                && $pages['discount'] = __('Vouchers', 'Shop.Theme.Customeraccount');
            \Configuration::get('PS_ORDER_RETURN')
                && $pages['order-follow'] = __('Merchandise returns', 'Shop.Theme.Customeraccount');
        }
        $pages['logout'] = __('Sign out', 'Shop.Theme.Actions');
        $pages['custom'] = __('Custom URL');

        return $pages;
    }

    protected function registerControls()
    {
        $this->startControlsSection(
            'section_selector',
            [
                'label' => __('User Menu'),
            ]
        );

        $this->startControlsTabs('tabs_menu');

        $this->startControlsTab(
            'tab_menu_visitor',
            [
                'label' => __('Visitor'),
            ]
        );

        $this->addControl(
            'selected_icon',
            [
                'label' => __('Icon'),
                'label_block' => false,
                'type' => ControlsManager::ICONS,
                'skin' => 'inline',
                'fa4compatibility' => 'icon',
                'default' => [
                    'value' => 'fas fa-user',
                    'library' => 'fa-solid',
                ],
                'recommended' => [
                    'ce-icons' => [
                        'user',
                        'user-o',
                        'user-circle',
                        'user-circle-o',
                        'user-simple',
                        'user-minimal',
                    ],
                    'fa-regular' => [
                        'user',
                        'circle-user',
                    ],
                    'fa-solid' => [
                        'user',
                        'circle-user',
                        'user-large',
                    ],
                ],
            ]
        );

        $this->addControl(
            'label',
            [
                'label' => __('Label'),
                'type' => ControlsManager::TEXT,
                'default' => Helper::$translator->trans('Sign in', [], 'Shop.Theme.Actions'),
            ]
        );

        $repeater = new Repeater();

        $repeater->addControl(
            'link_to',
            [
                'label' => __('Link'),
                'type' => ControlsManager::SELECT,
                'options' => _CE_ADMIN_ ? $this->getVisitorOptions() : [],
                'default' => 'register',
            ]
        );

        $repeater->addControl(
            'link',
            [
                'label_block' => true,
                'type' => ControlsManager::URL,
                'options' => false,
                'dynamic' => [
                    'active' => true,
                ],
                'condition' => [
                    'link_to' => 'custom',
                ],
            ]
        );

        $repeater->addControl(
            'text',
            [
                'label' => __('Text'),
                'type' => ControlsManager::TEXT,
            ]
        );

        $repeater->addControl(
            'selected_icon',
            [
                'label' => __('Icon'),
                'label_block' => false,
                'type' => ControlsManager::ICONS,
                'skin' => 'inline',
            ]
        );

        $this->addControl(
            'dropdown',
            [
                'label' => __('Dropdown'),
                'type' => ControlsManager::REPEATER,
                'prevent_empty' => false,
                'fields' => $repeater->getControls(),
                'title_field' => '<i class="{{ selected_icon.value }}" aria-hidden="true"></i>
                    {{{ text || elementor.panel.currentView.currentPageView.model.get("settings").controls.dropdown.fields.link_to.options[link_to] }}}',
            ]
        );

        $this->endControlsTab();

        $this->startControlsTab(
            'tab_menu_customer',
            [
                'label' => __('Customer'),
            ]
        );

        $this->addControl(
            'account',
            [
                'label' => __('Label'),
                'label_block' => true,
                'type' => ControlsManager::SELECT2,
                'default' => ['icon', 'firstname'],
                'multiple' => true,
                'options' => [
                    'icon' => __('Icon'),
                    'before' => __('Before'),
                    'firstname' => __('First Name', 'Shop.Forms.Labels'),
                    'lastname' => __('Last Name', 'Shop.Forms.Labels'),
                    'after' => __('After'),
                ],
            ]
        );

        $this->addControl(
            'before',
            [
                'label' => __('Before'),
                'type' => ControlsManager::TEXT,
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'account',
                            'operator' => 'contains',
                            'value' => 'before',
                        ],
                    ],
                ],
            ]
        );

        $this->addControl(
            'after',
            [
                'label' => __('After'),
                'type' => ControlsManager::TEXT,
                'conditions' => [
                    'terms' => [
                        [
                            'name' => 'account',
                            'operator' => 'contains',
                            'value' => 'after',
                        ],
                    ],
                ],
            ]
        );

        $repeater->updateControl('link_to', [
            'options' => _CE_ADMIN_ ? $this->getCustomerOptions() : [],
            'default' => 'identity',
        ]);

        $repeater->updateControl('selected_icon', [
            'fa4compatibility' => 'icon',
        ]);

        $this->addControl(
            'usermenu',
            [
                'label' => __('Dropdown'),
                'type' => ControlsManager::REPEATER,
                'fields' => $repeater->getControls(),
                'default' => [
                    [
                        'link_to' => 'my-account',
                        'selected_icon' => [
                            'value' => 'far fa-user',
                            'library' => 'fa-regular',
                        ],
                    ],
                    [
                        'link_to' => 'addresses',
                        'selected_icon' => [
                            'value' => 'far fa-address-book',
                            'library' => 'fa-regular',
                        ],
                    ],
                    [
                        'link_to' => 'history',
                        'selected_icon' => [
                            'value' => 'fas fa-list',
                            'library' => 'fa-solid',
                        ],
                    ],
                    [
                        'link_to' => 'logout',
                        'selected_icon' => [
                            'value' => 'fas fa-right-from-bracket',
                            'library' => 'fa-solid',
                        ],
                    ],
                ],
                'title_field' => '<#
                    var controls = elementor.panel.currentView.currentPageView.model.get("settings").controls,
                        migrated = "undefined" !== typeof __fa4_migrated,
                        icon = "undefined" !== typeof icon ? icon : false; #>
                    <i class="{{ icon && !migrated ? icon : selected_icon.value }}" aria-hidden="true"></i>
                    {{{ text || controls.usermenu.fields.link_to.options[link_to] }}}',
            ]
        );

        $this->endControlsTab();

        $this->endControlsTabs();

        $this->registerNavContentControls();

        $this->endControlsSection();

        $this->registerNavStyleSection([
            'show_icon' => true,
            'active_condition' => [
                'hide!' => '',
            ],
            'space_between_condition' => [
                'hide!' => '',
            ],
        ]);

        $this->registerDropdownStyleSection([
            'active_condition' => [
                'hide!' => '',
            ],
        ]);
    }

    protected function getHtmlWrapperClass()
    {
        return parent::getHtmlWrapperClass() . ' elementor-widget-nav-menu';
    }

    protected function render()
    {
        $pages = &$GLOBALS['smarty']->tpl_vars['urls']->value['pages'];
        $getUrl = function (&$item) use (&$pages) {
            if ('custom' === $link_to = $item['link_to']) {
                return $item['link']['url'];
            }
            if ('logout' === $link_to) {
                return \Tools::url($pages['index'], 'mylogout');
            }

            return $pages[str_replace('-', '_', $link_to)];
        };
        $settings = $this->getSettingsForDisplay();
        $icon = IconsManager::getBcIcon($settings, 'icon');
        $this->indicator = isset($settings['indicator']) && !isset($settings['__fa4_migrated']['submenu_icon'])
            ? $settings['indicator']
            : $settings['submenu_icon']['value'];

        if ($GLOBALS['customer']->isLogged()) {
            $options = $this->getCustomerOptions();
            $menu = [
                [
                    'id' => 0,
                    'icon' => in_array('icon', $settings['account']) ? $icon : '',
                    'label' => call_user_func(function () use (&$settings) {
                        $label = '';
                        $account = &$settings['account'];
                        in_array('before', $account) && $label .= $settings['before'];
                        in_array('firstname', $account) && $label .= " {$GLOBALS['customer']->firstname}";
                        in_array('lastname', $account) && $label .= " {$GLOBALS['customer']->lastname}";
                        in_array('after', $account) && $label .= $settings['after'];

                        return trim($label);
                    }),
                    'url' => $pages['my_account'],
                    'children' => [],
                ],
            ];
            foreach ($settings['usermenu'] as $i => &$item) {
                $menu[0]['children'][] = [
                    'id' => $i + 1,
                    'icon' => IconsManager::getBcIcon($item, 'icon'),
                'label' => $item['text'] ?: $options[$item['link_to']],
                    'url' => $getUrl($item),
                ];
            }
        } else {
            $settings['dropdown'] && $options = $this->getVisitorOptions();
            $menu = [
                [
                    'id' => 0,
                    'icon' => $icon,
                    'label' => $settings['label'],
                    'url' => $pages['my_account'],
                    'children' => [],
                ],
            ];
            foreach ($settings['dropdown'] as $i => &$item) {
                ob_start();
                IconsManager::renderIcon($item['selected_icon'], ['aria-hidden' => 'true']);
                $menu[0]['children'][] = [
                    'id' => $i + 1,
                    'icon' => ob_get_clean(),
                    'label' => $item['text'] ?: $options[$item['link_to']],
                    'url' => $getUrl($item),
                ];
            }
        }

        $this->addRenderAttribute('main-menu', 'class', [
            'ce-user-menu',
            'elementor-nav--main',
            'elementor-nav__container',
            'elementor-nav--layout-horizontal',
        ]);

        if ('none' !== $settings['pointer']) {
            $animation_type = self::getPointerAnimationType($settings['pointer']);

            $this->addRenderAttribute('main-menu', 'class', [
                'e--pointer-' . $settings['pointer'],
                'e--animation-' . $settings[$animation_type],
            ]);
        } ?>
        <nav <?php $this->printRenderAttributeString('main-menu'); ?> aria-label="<?php esc_attr_e('User Menu'); ?>"><?php $this->accountList($menu, 0, 'elementor-nav'); ?></nav>
        <?php
    }

    protected function accountList(array &$nodes, $depth = 0, $ul_class = '')
    {
        ?>
        <ul class="<?php echo $depth ? 'sub-menu elementor-nav--dropdown' : esc_attr($ul_class) . '" id="usermenu-' . esc_attr($this->getId()) . '" role="none'; ?>">
        <?php foreach ($nodes as &$node) { ?>
            <li class="menu-item menu-item-type-account menu-item-account-<?php echo (int) $node['id'], !empty($node['children']) ? ' menu-item-has-children' : ''; ?>">
                <a class="<?php echo $depth ? 'elementor-sub-item' : 'elementor-item'; ?>" href="<?php escape($node['url']); ?>"<?php
                    $depth || $node['label'] || print ' aria-label="' . esc_attr__('My account', 'Shop.Navigation') . '"'; ?>>
                    <?php echo $node['icon']; ?>
                <?php if ($node['label']) { ?>
                    <span><?php echo $node['label']; ?></span>
                <?php } ?>
                <?php if ($this->indicator && !empty($node['children'])) { ?>
                    <span class="sub-arrow <?php escape($this->indicator); ?>" aria-hidden="true"></span>
                <?php } ?>
                </a>
                <?php empty($node['children']) || $this->accountList($node['children'], $depth + 1); ?>
            </li>
        <?php } ?>
        </ul>
        <?php
    }
}
