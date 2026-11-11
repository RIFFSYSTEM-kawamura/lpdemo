<?php
/**
 * RIFF LP Theme Functions - 完全修正版
 * 
 * @package RIFF_Theme
 * @version 1.2.2
 */

// 基本テーマセットアップ
function riff_theme_setup() {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ));
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'riff-theme'),
        'footer'  => __('Footer Menu', 'riff-theme'),
    ));
}
add_action('after_setup_theme', 'riff_theme_setup');

// スクリプトとスタイルの読み込み
function riff_theme_scripts() {
    // メインスタイル
    wp_enqueue_style('riff-style', get_stylesheet_uri(), array(), '1.2.2');
    
    // Font Awesome
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css', array(), '6.7.2');
    
    // jQuery（WordPressデフォルト）
    wp_enqueue_script('jquery');
    
    // メインテーマJS
    wp_enqueue_script('riff-theme-js', get_template_directory_uri() . '/js/theme.js', array('jquery'), '1.2.2', true);
    
    // Features Slider JS
    wp_enqueue_script('features-slider-js', get_template_directory_uri() . '/js/features-slider.js', array('jquery'), '1.2.2', true);
    
    // Testimonials Slider JS を追加
    wp_enqueue_script('testimonials-slider-js', get_template_directory_uri() . '/js/testimonials-slider.js', array('jquery'), '1.2.2', true);
}
add_action('wp_enqueue_scripts', 'riff_theme_scripts');

// 管理画面用スクリプト
function riff_admin_scripts($hook) {
    if (strpos($hook, 'riff') !== false || $hook === 'post.php' || $hook === 'post-new.php') {
        wp_enqueue_media();
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_script('jquery-ui-sortable');
        
        wp_enqueue_code_editor(array('type' => 'text/css'));
        wp_enqueue_script('wp-theme-plugin-editor');
        wp_enqueue_style('wp-codemirror');
        
        wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css', array(), '6.7.2');
        wp_enqueue_style('riff-admin-style', get_template_directory_uri() . '/admin/admin-style.css', array(), '1.2.2');
        wp_enqueue_script('riff-admin-js', get_template_directory_uri() . '/admin/admin-script.js', array('jquery', 'jquery-ui-sortable', 'wp-color-picker'), '1.2.2', true);
    }
}
add_action('admin_enqueue_scripts', 'riff_admin_scripts');

// ショートコードサポート
function riff_shortcode_support() {
    add_filter('the_content', 'do_shortcode');
}
add_action('init', 'riff_shortcode_support');

// ==============================================
// カスタムCSS機能
// ==============================================

function riff_add_custom_css_meta_box() {
    add_meta_box(
        'riff_custom_css',
        '<span class="dashicons dashicons-admin-customizer"></span> カスタムCSS',
        'riff_custom_css_meta_box_callback',
        'page',
        'advanced',
        'high'
    );
}
add_action('add_meta_boxes', 'riff_add_custom_css_meta_box');

function riff_show_custom_css_meta_box($hidden, $screen) {
    if ('page' === $screen->id) {
        $hidden = array_diff($hidden, array('riff_custom_css'));
    }
    return $hidden;
}
add_filter('default_hidden_meta_boxes', 'riff_show_custom_css_meta_box', 10, 2);

function riff_custom_css_metabox_always_open() {
    global $post;
    if (get_post_type($post) === 'page') {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            if ($('#riff_custom_css').length > 0) {
                $('#riff_custom_css').removeClass('closed');
            }
        });
        </script>
        <?php
    }
}
add_action('admin_footer-post.php', 'riff_custom_css_metabox_always_open');
add_action('admin_footer-post-new.php', 'riff_custom_css_metabox_always_open');

function riff_custom_css_meta_box_callback($post) {
    wp_nonce_field('riff_save_custom_css', 'riff_custom_css_nonce');
    
    $custom_css = get_post_meta($post->ID, '_riff_custom_css', true);
    $css_enabled = get_post_meta($post->ID, '_riff_custom_css_enabled', true);
    ?>
    <div class="riff-custom-css-wrapper">
        <div class="riff-custom-css-header">
            <label class="riff-custom-css-toggle">
                <input type="checkbox" name="riff_custom_css_enabled" value="1" <?php checked($css_enabled, '1'); ?>>
                <span>このページでカスタムCSSを有効にする</span>
            </label>
            <p class="description">このページ専用のカスタムCSSを記述できます。他のページには影響しません。</p>
        </div>
        
        <div class="riff-custom-css-editor">
            <textarea id="riff_custom_css" name="riff_custom_css" rows="20" class="large-text code"><?php echo esc_textarea($custom_css); ?></textarea>
        </div>
        
        <div class="riff-custom-css-tips">
            <h4>💡 使い方のヒント</h4>
            <ul>
                <li><strong>セレクタの指定:</strong> 通常のCSSと同じように記述できます</li>
                <li><strong>例:</strong> <code>.section-title { color: #ff0000; }</code></li>
                <li><strong>メディアクエリ:</strong> レスポンシブ対応も可能です</li>
                <li><strong>例:</strong> <code>@media (max-width: 768px) { .section-title { font-size: 24px; } }</code></li>
                <li><strong>!important:</strong> 既存のスタイルを上書きする場合に使用できます</li>
            </ul>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        if (typeof wp.codeEditor !== 'undefined') {
            var editorSettings = wp.codeEditor.defaultSettings ? _.clone(wp.codeEditor.defaultSettings) : {};
            editorSettings.codemirror = _.extend(
                {},
                editorSettings.codemirror,
                {
                    mode: 'css',
                    lineNumbers: true,
                    lineWrapping: true,
                    indentUnit: 2,
                    tabSize: 2,
                    indentWithTabs: false,
                    autoCloseBrackets: true,
                    matchBrackets: true,
                    lint: true,
                    gutters: ['CodeMirror-lint-markers'],
                    styleActiveLine: true,
                    theme: 'default'
                }
            );
            
            var editor = wp.codeEditor.initialize($('#riff_custom_css'), editorSettings);
            
            if (editor && editor.codemirror) {
                editor.codemirror.setSize(null, 400);
            }
        }
    });
    </script>
    <?php
}

function riff_save_custom_css($post_id) {
    if (!isset($_POST['riff_custom_css_nonce']) || 
        !wp_verify_nonce($_POST['riff_custom_css_nonce'], 'riff_save_custom_css')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    if (isset($_POST['riff_custom_css'])) {
        $custom_css = wp_strip_all_tags($_POST['riff_custom_css']);
        update_post_meta($post_id, '_riff_custom_css', $custom_css);
    } else {
        delete_post_meta($post_id, '_riff_custom_css');
    }
    
    if (isset($_POST['riff_custom_css_enabled'])) {
        update_post_meta($post_id, '_riff_custom_css_enabled', '1');
    } else {
        delete_post_meta($post_id, '_riff_custom_css_enabled');
    }
}
add_action('save_post', 'riff_save_custom_css');

function riff_output_custom_css() {
    if (!is_page()) {
        return;
    }
    
    $post_id = get_the_ID();
    $css_enabled = get_post_meta($post_id, '_riff_custom_css_enabled', true);
    
    if ($css_enabled !== '1') {
        return;
    }
    
    $custom_css = get_post_meta($post_id, '_riff_custom_css', true);
    
    if (empty($custom_css)) {
        return;
    }
    
    echo '<style id="riff-page-custom-css-' . esc_attr($post_id) . '" type="text/css">' . "\n";
    echo '/* カスタムCSS - ページID: ' . esc_html($post_id) . ' */' . "\n";
    echo wp_strip_all_tags($custom_css) . "\n";
    echo '</style>' . "\n";
}
add_action('wp_head', 'riff_output_custom_css', 100);

// ==============================================
// レスポンシブ改行ショートコード
// ==============================================

function riff_br_pc_shortcode() {
    return '<br class="pc">';
}
add_shortcode('br_pc', 'riff_br_pc_shortcode');

function riff_br_sp_shortcode() {
    return '<br class="sp">';
}
add_shortcode('br_sp', 'riff_br_sp_shortcode');

// br.pc / br.sp を許可するサニタイザー
function riff_kses_br($text) {
    return wp_kses($text, array('br' => array('class' => array())));
}

function riff_allow_br_class($tags, $context) {
    if ($context === 'post') {
        $tags['br'] = array(
            'class' => true,
        );
    }
    return $tags;
}
add_filter('wp_kses_allowed_html', 'riff_allow_br_class', 10, 2);

// ==============================================
// カスタムショートコード
// ==============================================

function riff_section_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'id'    => '',
        'class' => '',
        'bg'    => '#ffffff',
    ), $atts);
    
    $id_attr = !empty($atts['id']) ? 'id="' . esc_attr($atts['id']) . '"' : '';
    $class_attr = 'riff-section ' . esc_attr($atts['class']);
    $style_attr = 'background-color: ' . esc_attr($atts['bg']) . ';';
    
    return '<section ' . $id_attr . ' class="' . $class_attr . '" style="' . $style_attr . '">' . do_shortcode($content) . '</section>';
}
add_shortcode('riff_section', 'riff_section_shortcode');

function riff_container_shortcode($atts, $content = null) {
    return '<div class="container">' . do_shortcode($content) . '</div>';
}
add_shortcode('riff_container', 'riff_container_shortcode');

function riff_heading_shortcode($atts, $content = null) {
    $atts = shortcode_atts(array(
        'level' => '2',
        'align' => 'center',
    ), $atts);
    
    $level = absint($atts['level']);
    $level = min(max($level, 1), 6);
    
    return '<h' . $level . ' style="text-align: ' . esc_attr($atts['align']) . '; margin-bottom: 40px; font-size: 36px; font-weight: 700;">' . do_shortcode($content) . '</h' . $level . '>';
}
add_shortcode('riff_heading', 'riff_heading_shortcode');

// ウィジェットエリア
function riff_widgets_init() {
    register_sidebar(array(
        'name'          => __('Footer Widget Area', 'riff-theme'),
        'id'            => 'footer-1',
        'description'   => __('フッターのウィジェットエリア', 'riff-theme'),
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    ));
}
add_action('widgets_init', 'riff_widgets_init');

// ==============================================
// SNS ユーティリティ
// ==============================================

function riff_get_sns_platforms() {
    return array(
        'line'      => array('label' => 'LINE',        'icon' => 'fa-brands fa-line',      'color' => '#06C755'),
        'x'         => array('label' => 'X (Twitter)', 'icon' => 'fa-brands fa-x-twitter', 'color' => '#000000'),
        'instagram' => array('label' => 'Instagram',   'icon' => 'fa-brands fa-instagram', 'color' => '#C13584'),
        'threads'   => array('label' => 'Threads',     'icon' => 'fa-brands fa-threads',   'color' => '#000000'),
        'tiktok'    => array('label' => 'TikTok',      'icon' => 'fa-brands fa-tiktok',    'color' => '#000000'),
        'facebook'  => array('label' => 'Facebook',    'icon' => 'fa-brands fa-facebook-f','color' => '#1877F2'),
    );
}

function riff_sanitize_sns_links($field_name) {
    $platforms  = array_keys(riff_get_sns_platforms());
    $posted     = isset($_POST[$field_name]) && is_array($_POST[$field_name]) ? $_POST[$field_name] : array();
    $result     = array();
    foreach ($platforms as $key) {
        $data = isset($posted[$key]) && is_array($posted[$key]) ? $posted[$key] : array();
        $result[$key] = array(
            'enabled' => !empty($data['enabled']),
            'url'     => esc_url_raw(isset($data['url']) ? $data['url'] : ''),
        );
    }
    return $result;
}

function riff_render_sns_admin_fields($prefix, $sns_data) {
    $platforms = riff_get_sns_platforms();
    foreach ($platforms as $key => $platform) {
        $enabled = !empty($sns_data[$key]['enabled']);
        $url     = isset($sns_data[$key]['url']) ? $sns_data[$key]['url'] : '';
        ?>
        <tr>
            <th style="width:160px;">
                <i class="<?php echo esc_attr($platform['icon']); ?>" style="color:<?php echo esc_attr($platform['color']); ?>;font-size:18px;width:22px;text-align:center;"></i>
                <span style="margin-left:6px;"><?php echo esc_html($platform['label']); ?></span>
            </th>
            <td>
                <label style="display:inline-flex;align-items:center;gap:6px;margin-right:20px;cursor:pointer;">
                    <input type="checkbox"
                           name="<?php echo esc_attr($prefix); ?>[<?php echo esc_attr($key); ?>][enabled]"
                           value="1"
                           <?php checked($enabled, true); ?>>
                    <span>表示する</span>
                </label>
                <input type="url"
                       name="<?php echo esc_attr($prefix); ?>[<?php echo esc_attr($key); ?>][url]"
                       value="<?php echo esc_url($url); ?>"
                       class="large-text"
                       placeholder="https://"
                       style="max-width:400px;">
            </td>
        </tr>
        <?php
    }
}

// ==============================================
// テーマ設定の取得・保存
// ==============================================

function riff_get_default_settings() {
    return array(
        'header' => array(
            'logo_type' => 'text',
            'logo_text' => 'RIFF',
            'logo_image' => '',
            'logo_width' => '200',
            'logo_height' => '60',
            'text_color' => '#ffffff',
            'text_color_scrolled' => '#333333',
            'cta_text' => 'お問い合わせ',
            'cta_url' => '#contact',
            'sns_links' => array(
                'line'      => array('enabled' => false, 'url' => ''),
                'x'         => array('enabled' => false, 'url' => ''),
                'instagram' => array('enabled' => false, 'url' => ''),
                'threads'   => array('enabled' => false, 'url' => ''),
                'tiktok'    => array('enabled' => false, 'url' => ''),
                'facebook'  => array('enabled' => false, 'url' => ''),
            ),
        ),
        'hero' => array(
            'slides' => array(
                array(
                    'background' => '',
                    'subtitle' => 'Welcome to Our Site',
                    'title' => 'あなたのビジネスを<br class="pc">次のレベルへ',
                    'description' => 'プロフェッショナルなソリューションで、あなたのビジネスの成長をサポートします。',
                    'button1_text' => '今すぐ始める',
                    'button1_url' => '#contact',
                    'button2_text' => '詳しく見る',
                    'button2_url' => '#about',
                    'content_image' => '',
                    'layout' => 'normal',
                    'text_animation' => 'fadeInLeft',
                    'image_animation' => 'fadeIn',
                    'content_image_animation' => 'fadeInRight',
                )
            ),
            'autoplay' => true,
            'autoplay_speed' => 5000,
            'transition_speed' => 1000,
        ),
        'footer' => array(
            'company' => 'Company Name',
            'description' => 'プロフェッショナルなサービスを提供しています。',
            'address' => '東京都渋谷区〇〇 1-2-3',
            'phone' => '03-1234-5678',
            'email' => 'info@example.com',
            'copyright' => '© 2025 Company Name. All Rights Reserved.',
            'social' => array(
                'facebook' => '',
                'twitter' => '',
                'instagram' => '',
                'linkedin' => '',
                'youtube' => '',
            ),
            'sns_links' => array(
                'line'      => array('enabled' => false, 'url' => ''),
                'x'         => array('enabled' => false, 'url' => ''),
                'instagram' => array('enabled' => false, 'url' => ''),
                'threads'   => array('enabled' => false, 'url' => ''),
                'tiktok'    => array('enabled' => false, 'url' => ''),
                'facebook'  => array('enabled' => false, 'url' => ''),
            ),
        ),
    );
}

function riff_get_settings($section = null) {
    $defaults = riff_get_default_settings();
    $settings = get_option('riff_theme_settings', $defaults);
    
    if ($section) {
        return isset($settings[$section]) ? $settings[$section] : $defaults[$section];
    }
    
    return $settings;
}

function riff_save_settings($section, $data) {
    $settings = get_option('riff_theme_settings', riff_get_default_settings());
    $settings[$section] = $data;
    update_option('riff_theme_settings', $settings);
}

// ==============================================
// 管理画面メニュー
// ==============================================

function riff_theme_admin_menu() {
    add_menu_page(
        'RIFFテーマ設定',
        'RIFFテーマ',
        'manage_options',
        'riff-theme-settings',
        'riff_theme_settings_page',
        'dashicons-admin-customizer',
        30
    );
    
    add_submenu_page(
        'riff-theme-settings',
        'ヘッダー設定',
        'ヘッダー設定',
        'manage_options',
        'riff-header-settings',
        'riff_header_settings_page'
    );
    
    add_submenu_page(
        'riff-theme-settings',
        'ヒーローライダー',
        'ヒーローライダー',
        'manage_options',
        'riff-hero-settings',
        'riff_hero_settings_page'
    );
    
    add_submenu_page(
        'riff-theme-settings',
        'フッター設定',
        'フッター設定',
        'manage_options',
        'riff-footer-settings',
        'riff_footer_settings_page'
    );
}
add_action('admin_menu', 'riff_theme_admin_menu');

// ==============================================
// メインダッシュボードページ
// ==============================================

function riff_theme_settings_page() {
    ?>
    <div class="wrap riff-admin-wrap">
        <h1><i class="dashicons dashicons-admin-customizer"></i> RIFFテーマ設定</h1>
        
        <div class="riff-dashboard">
            <div class="riff-card">
                <h2>🎨 テーマのカスタマイズ</h2>
                <p>RIFFテーマへようこそ!左側のメニューから各セクションを編集できます。</p>
                
                <div class="riff-quick-links">
                    <a href="<?php echo admin_url('admin.php?page=riff-header-settings'); ?>" class="riff-quick-link">
                        <i class="dashicons dashicons-editor-alignleft"></i>
                        <h3>ヘッダー設定</h3>
                        <p>ロゴとメニューの設定</p>
                    </a>
                    
                    <a href="<?php echo admin_url('admin.php?page=riff-hero-settings'); ?>" class="riff-quick-link">
                        <i class="dashicons dashicons-format-gallery"></i>
                        <h3>ヒーローライダー</h3>
                        <p>メインビジュアルの設定</p>
                    </a>
                    
                    <a href="<?php echo admin_url('admin.php?page=riff-footer-settings'); ?>" class="riff-quick-link">
                        <i class="dashicons dashicons-editor-aligncenter"></i>
                        <h3>フッター設定</h3>
                        <p>会社情報とSNSリンク</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php
}

// ==============================================
// ヘッダー設定ページ
// ==============================================

function riff_header_settings_page() {
    if (isset($_POST['riff_save_header']) && check_admin_referer('riff_header_settings')) {
        $header_data = array(
            'logo_type' => sanitize_text_field($_POST['logo_type']),
            'logo_text' => riff_kses_br($_POST['logo_text']),
            'logo_image' => esc_url_raw($_POST['logo_image']),
            'logo_width' => absint($_POST['logo_width']),
            'logo_height' => absint($_POST['logo_height']),
            'text_color' => sanitize_hex_color($_POST['text_color']),
            'text_color_scrolled' => sanitize_hex_color($_POST['text_color_scrolled']),
            'cta_text' => riff_kses_br($_POST['cta_text']),
            'cta_url' => esc_url_raw($_POST['cta_url']),
            'sns_links' => riff_sanitize_sns_links('header_sns'),
        );
        
        riff_save_settings('header', $header_data);
        echo '<div class="notice notice-success is-dismissible"><p><strong>✓ ヘッダー設定を保存しました!</strong></p></div>';
    }
    
    $header = riff_get_settings('header');
    ?>
    <div class="wrap riff-admin-wrap">
        <h1><i class="dashicons dashicons-editor-alignleft"></i> ヘッダー設定</h1>
        
        <form method="post" class="riff-form">
            <?php wp_nonce_field('riff_header_settings'); ?>
            
            <div class="riff-card">
                <h2>ロゴ設定</h2>
                
                <table class="form-table">
                    <tr>
                        <th><label for="logo_type">ロゴタイプ</label></th>
                        <td>
                            <select name="logo_type" id="logo_type">
                                <option value="text" <?php selected($header['logo_type'], 'text'); ?>>テキストロゴ</option>
                                <option value="image" <?php selected($header['logo_type'], 'image'); ?>>画像ロゴ</option>
                            </select>
                        </td>
                    </tr>
                    
                    <tr class="logo-text-row">
                        <th><label for="logo_text">ロゴテキスト</label></th>
                        <td>
                            <input type="text" name="logo_text" id="logo_text" value="<?php echo esc_attr($header['logo_text']); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr class="logo-image-row">
                        <th><label for="logo_image">ロゴ画像</label></th>
                        <td>
                            <div class="riff-logo-preview">
                                <?php if (!empty($header['logo_image'])) : ?>
                                    <img src="<?php echo esc_url($header['logo_image']); ?>" alt="ロゴ画像" style="max-width: <?php echo esc_attr($header['logo_width']); ?>px; max-height: <?php echo esc_attr($header['logo_height']); ?>px;">
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="logo_image" id="logo_image" value="<?php echo esc_url($header['logo_image']); ?>">
                            <button type="button" class="button riff-upload-logo">画像を選択</button>
                            <button type="button" class="button riff-remove-logo">削除</button>
                        </td>
                    </tr>
                    
                    <tr class="logo-image-row">
                        <th><label for="logo_width">ロゴ幅</label></th>
                        <td>
                            <input type="number" name="logo_width" id="logo_width" value="<?php echo esc_attr($header['logo_width']); ?>" class="small-text"> px
                            <p class="description">最大幅を指定します(デフォルト: 200px)</p>
                        </td>
                    </tr>
                    
                    <tr class="logo-image-row">
                        <th><label for="logo_height">ロゴ高さ</label></th>
                        <td>
                            <input type="number" name="logo_height" id="logo_height" value="<?php echo esc_attr($header['logo_height']); ?>" class="small-text"> px
                            <p class="description">最大高さを指定します(デフォルト: 60px)</p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="riff-card">
                <h2>テキストカラー設定</h2>
                
                <table class="form-table">
                    <tr>
                        <th><label for="text_color">テキストカラー(ページトップ)</label></th>
                        <td>
                            <input type="text" name="text_color" id="text_color" value="<?php echo esc_attr($header['text_color']); ?>" class="riff-color-picker">
                            <p class="description">ヒーローセクション上部での文字色</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="text_color_scrolled">テキストカラー(スクロール後)</label></th>
                        <td>
                            <input type="text" name="text_color_scrolled" id="text_color_scrolled" value="<?php echo esc_attr($header['text_color_scrolled']); ?>" class="riff-color-picker">
                            <p class="description">スクロール後の文字色</p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="riff-card">
                <h2>CTAボタン設定</h2>
                
                <table class="form-table">
                    <tr>
                        <th><label for="cta_text">ボタンテキスト</label></th>
                        <td>
                            <input type="text" name="cta_text" id="cta_text" value="<?php echo esc_attr($header['cta_text']); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="cta_url">リンク先URL</label></th>
                        <td>
                            <input type="text" name="cta_url" id="cta_url" value="<?php echo esc_url($header['cta_url']); ?>" class="regular-text">
                            <p class="description">例: #contact または https://example.com</p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="riff-card">
                <h2>SNSリンク設定（ハンバーガーメニュー内）</h2>
                <p class="description" style="padding:0 10px 15px;">有効にしてURLを入力すると、モバイルメニューにSNSアイコンが表示されます。</p>
                <table class="form-table">
                    <?php riff_render_sns_admin_fields('header_sns', isset($header['sns_links']) ? $header['sns_links'] : array()); ?>
                </table>
            </div>

            <p class="submit">
                <button type="submit" name="riff_save_header" class="button button-primary button-large">
                    <i class="dashicons dashicons-yes"></i> 設定を保存
                </button>
            </p>
        </form>
    </div>
    <?php
}

// ==============================================
// ヒーロー設定ページ
// ==============================================

function riff_hero_settings_page() {
    if (isset($_POST['riff_save_hero']) && check_admin_referer('riff_hero_settings')) {
        $slides = array();
        
        if (isset($_POST['slides']) && is_array($_POST['slides'])) {
            foreach ($_POST['slides'] as $slide) {
                $slides[] = array(
                    'background' => esc_url_raw($slide['background']),
                    'subtitle' => riff_kses_br($slide['subtitle']),
                    'title' => wp_kses_post($slide['title']),
                    'description' => riff_kses_br($slide['description']),
                    'button1_text' => sanitize_text_field($slide['button1_text']),
                    'button1_url' => esc_url_raw($slide['button1_url']),
                    'button2_text' => sanitize_text_field($slide['button2_text']),
                    'button2_url' => esc_url_raw($slide['button2_url']),
                    'content_image' => esc_url_raw($slide['content_image']),
                    'layout' => sanitize_text_field($slide['layout']),
                    'text_animation' => sanitize_text_field($slide['text_animation']),
                    'image_animation' => sanitize_text_field($slide['image_animation']),
                    'content_image_animation' => sanitize_text_field($slide['content_image_animation']),
                );
            }
        }
        
        $hero_data = array(
            'slides' => $slides,
            'autoplay' => isset($_POST['autoplay']),
            'autoplay_speed' => absint($_POST['autoplay_speed']),
            'transition_speed' => absint($_POST['transition_speed']),
        );
        
        riff_save_settings('hero', $hero_data);
        echo '<div class="notice notice-success is-dismissible"><p><strong>✓ ヒーロー設定を保存しました!</strong></p></div>';
    }
    
    $hero = riff_get_settings('hero');
    $slides = isset($hero['slides']) ? $hero['slides'] : array();
    ?>
    <div class="wrap riff-admin-wrap">
        <h1><i class="dashicons dashicons-format-gallery"></i> ヒーローライダー設定</h1>
        
        <form method="post" class="riff-form" id="riff-hero-form">
            <?php wp_nonce_field('riff_hero_settings'); ?>
            
            <div class="riff-card">
                <div class="riff-card-header">
                    <h2>スライド管理</h2>
                    <span class="description">現在のスライド数: <strong id="current-slide-count"><?php echo count($slides); ?></strong>個</span>
                </div>
                
                <div id="slides-container">
                    <?php
                    if (!empty($slides)) {
                        foreach ($slides as $index => $slide) {
                            riff_render_slide_field($index, $slide);
                        }
                    } else {
                        echo '<p class="description" style="text-align: center; padding: 40px; color: #999; background: #f9f9f9; border: 2px dashed #ddd; border-radius: 6px;">スライドがまだありません。下のボタンから追加してください。</p>';
                    }
                    ?>
                </div>
                
                <p style="margin-top: 20px;">
                    <button type="button" id="add-slide" class="button button-secondary button-large">
                        <i class="dashicons dashicons-plus-alt"></i> スライドを追加
                    </button>
                </p>
            </div>
            
            <div class="riff-card">
                <h2>スライダー設定</h2>
                
                <table class="form-table">
                    <tr>
                        <th><label for="autoplay">自動再生</label></th>
                        <td>
                            <label>
                                <input type="checkbox" name="autoplay" id="autoplay" value="1" <?php checked($hero['autoplay'], true); ?>>
                                自動的にスライドを切り替える
                            </label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="autoplay_speed">自動再生速度</label></th>
                        <td>
                            <input type="number" name="autoplay_speed" id="autoplay_speed" value="<?php echo esc_attr($hero['autoplay_speed']); ?>" class="small-text" min="1000" step="100"> ミリ秒
                            <p class="description">各スライドの表示時間(デフォルト: 5000 = 5秒)</p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="transition_speed">切り替え速度</label></th>
                        <td>
                            <input type="number" name="transition_speed" id="transition_speed" value="<?php echo esc_attr($hero['transition_speed']); ?>" class="small-text" min="100" step="100"> ミリ秒
                            <p class="description">スライド切り替えのアニメーション時間(デフォルト: 1000 = 1秒)</p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <p class="submit">
                <button type="submit" name="riff_save_hero" class="button button-primary button-large">
                    <i class="dashicons dashicons-yes"></i> 設定を保存
                </button>
            </p>
        </form>
    </div>
    
    <!-- スライドテンプレート -->
    <script type="text/template" id="slide-template">
        <?php 
        $template_slide = array(
            'background' => '',
            'subtitle' => '',
            'title' => '',
            'description' => '',
            'button1_text' => '',
            'button1_url' => '',
            'button2_text' => '',
            'button2_url' => '',
            'content_image' => '',
            'layout' => 'normal',
            'text_animation' => 'fadeInLeft',
            'image_animation' => 'fadeIn',
            'content_image_animation' => 'fadeInRight',
        );
        
        ob_start();
        riff_render_slide_field('{{INDEX}}', $template_slide);
        $template = ob_get_clean();
        echo $template;
        ?>
    </script>
    <?php
}

// スライドフィールドのレンダリング
function riff_render_slide_field($index, $slide = array()) {
    $defaults = array(
        'background' => '',
        'subtitle' => '',
        'title' => '',
        'description' => '',
        'button1_text' => '',
        'button1_url' => '',
        'button2_text' => '',
        'button2_url' => '',
        'content_image' => '',
        'layout' => 'normal',
        'text_animation' => 'fadeInLeft',
        'image_animation' => 'fadeIn',
        'content_image_animation' => 'fadeInRight',
    );
    
    $slide = wp_parse_args($slide, $defaults);
    $index_display = is_numeric($index) ? ($index + 1) : '新規';
    ?>
    <div class="riff-slide-item" data-index="<?php echo esc_attr($index); ?>">
        <div class="riff-slide-header">
            <span class="riff-slide-handle">
                <i class="dashicons dashicons-menu"></i>
            </span>
            <h3>スライド <?php echo esc_html($index_display); ?></h3>
            <button type="button" class="riff-slide-toggle" aria-label="トグル">
                <i class="dashicons dashicons-arrow-down-alt2"></i>
            </button>
            <button type="button" class="riff-slide-remove" aria-label="削除">
                <i class="dashicons dashicons-trash"></i>
            </button>
        </div>
        
        <div class="riff-slide-content">
            <table class="form-table">
                <tr>
                    <th colspan="2"><strong><i class="dashicons dashicons-images-alt2"></i> 背景設定</strong></th>
                </tr>
                
                <tr>
                    <th><label>背景画像</label></th>
                    <td>
                        <div class="riff-image-upload">
                            <div class="riff-image-preview">
                                <?php if (!empty($slide['background'])) : ?>
                                    <img src="<?php echo esc_url($slide['background']); ?>" alt="背景画像">
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="slides[<?php echo esc_attr($index); ?>][background]" class="riff-image-url" value="<?php echo esc_url($slide['background']); ?>">
                            <button type="button" class="button riff-upload-image">画像を選択</button>
                            <button type="button" class="button riff-remove-image">削除</button>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th><label>背景アニメーション</label></th>
                    <td>
                        <select name="slides[<?php echo esc_attr($index); ?>][image_animation]">
                            <option value="fadeIn" <?php selected($slide['image_animation'], 'fadeIn'); ?>>フェードイン</option>
                            <option value="zoomIn" <?php selected($slide['image_animation'], 'zoomIn'); ?>>ズームイン</option>
                            <option value="slideInUp" <?php selected($slide['image_animation'], 'slideInUp'); ?>>下からスライド</option>
                            <option value="slideInDown" <?php selected($slide['image_animation'], 'slideInDown'); ?>>上からスライド</option>
                            <option value="slideInLeft" <?php selected($slide['image_animation'], 'slideInLeft'); ?>>左からスライド</option>
                            <option value="slideInRight" <?php selected($slide['image_animation'], 'slideInRight'); ?>>右からスライド</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th colspan="2"><strong><i class="dashicons dashicons-edit"></i> テキストコンテンツ</strong></th>
                </tr>
                
                <tr>
                    <th><label>サブタイトル</label></th>
                    <td>
                        <input type="text" name="slides[<?php echo esc_attr($index); ?>][subtitle]" value="<?php echo esc_attr($slide['subtitle']); ?>" class="large-text">
                        <p class="description">PC改行: <code>&lt;br class="pc"&gt;</code> / スマホ改行: <code>&lt;br class="sp"&gt;</code></p>
                    </td>
                </tr>
                
                <tr>
                    <th><label>タイトル</label></th>
                    <td>
                        <textarea name="slides[<?php echo esc_attr($index); ?>][title]" rows="3" class="large-text"><?php echo esc_textarea($slide['title']); ?></textarea>
                        <p class="description">HTMLタグ使用可。改行は &lt;br class="pc"&gt; または &lt;br class="sp"&gt;</p>
                    </td>
                </tr>
                
                <tr>
                    <th><label>説明文</label></th>
                    <td>
                        <textarea name="slides[<?php echo esc_attr($index); ?>][description]" rows="3" class="large-text"><?php echo esc_textarea($slide['description']); ?></textarea>
                        <p class="description">PC改行: <code>&lt;br class="pc"&gt;</code> / スマホ改行: <code>&lt;br class="sp"&gt;</code></p>
                    </td>
                </tr>
                
                <tr>
                    <th><label>テキストアニメーション</label></th>
                    <td>
                        <select name="slides[<?php echo esc_attr($index); ?>][text_animation]">
                            <option value="fadeIn" <?php selected($slide['text_animation'], 'fadeIn'); ?>>フェードイン</option>
                            <option value="fadeInUp" <?php selected($slide['text_animation'], 'fadeInUp'); ?>>下からフェードイン</option>
                            <option value="fadeInDown" <?php selected($slide['text_animation'], 'fadeInDown'); ?>>上からフェードイン</option>
                            <option value="fadeInLeft" <?php selected($slide['text_animation'], 'fadeInLeft'); ?>>左からフェードイン</option>
                            <option value="fadeInRight" <?php selected($slide['text_animation'], 'fadeInRight'); ?>>右からフェードイン</option>
                            <option value="slideInUp" <?php selected($slide['text_animation'], 'slideInUp'); ?>>下からスライド</option>
                            <option value="slideInLeft" <?php selected($slide['text_animation'], 'slideInLeft'); ?>>左からスライド</option>
                            <option value="zoomIn" <?php selected($slide['text_animation'], 'zoomIn'); ?>>ズームイン</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th colspan="2"><strong><i class="dashicons dashicons-admin-links"></i> ボタン設定</strong></th>
                </tr>
                
                <tr>
                    <th><label>ボタン1 テキスト</label></th>
                    <td>
                        <input type="text" name="slides[<?php echo esc_attr($index); ?>][button1_text]" value="<?php echo esc_attr($slide['button1_text']); ?>" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th><label>ボタン1 URL</label></th>
                    <td>
                        <input type="text" name="slides[<?php echo esc_attr($index); ?>][button1_url]" value="<?php echo esc_url($slide['button1_url']); ?>" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th><label>ボタン2 テキスト</label></th>
                    <td>
                        <input type="text" name="slides[<?php echo esc_attr($index); ?>][button2_text]" value="<?php echo esc_attr($slide['button2_text']); ?>" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th><label>ボタン2 URL</label></th>
                    <td>
                        <input type="text" name="slides[<?php echo esc_attr($index); ?>][button2_url]" value="<?php echo esc_url($slide['button2_url']); ?>" class="regular-text">
                    </td>
                </tr>
                
                <tr>
                    <th colspan="2"><strong><i class="dashicons dashicons-format-image"></i> コンテンツ画像</strong></th>
                </tr>
                
                <tr>
                    <th><label>コンテンツ画像</label></th>
                    <td>
                        <div class="riff-image-upload">
                            <div class="riff-content-image-preview">
                                <?php if (!empty($slide['content_image'])) : ?>
                                    <img src="<?php echo esc_url($slide['content_image']); ?>" alt="コンテンツ画像">
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="slides[<?php echo esc_attr($index); ?>][content_image]" class="riff-content-image-url" value="<?php echo esc_url($slide['content_image']); ?>">
                            <button type="button" class="button riff-upload-content-image">画像を選択</button>
                            <button type="button" class="button riff-remove-content-image">削除</button>
                            <p class="description">テキストと並べて表示される画像</p>
                        </div>
                    </td>
                </tr>
                
                <tr>
                    <th><label>コンテンツ画像アニメーション</label></th>
                    <td>
                        <select name="slides[<?php echo esc_attr($index); ?>][content_image_animation]">
                            <option value="fadeIn" <?php selected($slide['content_image_animation'], 'fadeIn'); ?>>フェードイン</option>
                            <option value="fadeInUp" <?php selected($slide['content_image_animation'], 'fadeInUp'); ?>>下からフェードイン</option>
                            <option value="fadeInLeft" <?php selected($slide['content_image_animation'], 'fadeInLeft'); ?>>左からフェードイン</option>
                            <option value="fadeInRight" <?php selected($slide['content_image_animation'], 'fadeInRight'); ?>>右からフェードイン</option>
                            <option value="zoomIn" <?php selected($slide['content_image_animation'], 'zoomIn'); ?>>ズームイン</option>
                            <option value="float" <?php selected($slide['content_image_animation'], 'float'); ?>>フロート(ゆらゆら)</option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th colspan="2"><strong><i class="dashicons dashicons-editor-table"></i> レイアウト</strong></th>
                </tr>
                
                <tr>
                    <th><label>レイアウト</label></th>
                    <td>
                        <select name="slides[<?php echo esc_attr($index); ?>][layout]">
                            <option value="normal" <?php selected($slide['layout'], 'normal'); ?>>通常(テキスト左・画像右)</option>
                            <option value="reverse" <?php selected($slide['layout'], 'reverse'); ?>>反転(画像左・テキスト右)</option>
                        </select>
                        <p class="description">コンテンツ画像がある場合のレイアウト</p>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <?php
}

// ==============================================

// ==============================================
// フッター設定ページ（CTAフッター）
// ==============================================

function riff_footer_settings_page() {
    if (isset($_POST['riff_save_footer']) && check_admin_referer('riff_footer_settings')) {
        $footer_data = array(
            // 背景設定
            'background_color' => sanitize_hex_color($_POST['background_color']),
            
            // メインテキスト
            'main_text' => riff_kses_br($_POST['main_text']),

            // 左側エリア（電話情報）
            'business_hours' => riff_kses_br($_POST['business_hours']),
            'phone' => sanitize_text_field($_POST['phone']),
            'phone_subtext' => riff_kses_br($_POST['phone_subtext']),

            // ボタン設定
            'line_button_text' => riff_kses_br($_POST['line_button_text']),
            'line_button_url' => esc_url_raw($_POST['line_button_url']),
            'line_button_color' => sanitize_hex_color($_POST['line_button_color']),

            'email_button_text' => riff_kses_br($_POST['email_button_text']),
            'email_button_url' => esc_url_raw($_POST['email_button_url']),
            'email_button_bg_color' => sanitize_hex_color($_POST['email_button_bg_color']),
            'email_button_text_color' => sanitize_hex_color($_POST['email_button_text_color']),

            // 注意書き
            'notice_text' => riff_kses_br($_POST['notice_text']),

            // フッター下部
            'privacy_text' => riff_kses_br($_POST['privacy_text']),
            'privacy_url' => esc_url_raw($_POST['privacy_url']),
            'copyright' => riff_kses_br($_POST['copyright']),
            'sns_links' => riff_sanitize_sns_links('footer_sns'),
        );
        
        riff_save_settings('footer', $footer_data);
        echo '<div class="notice notice-success is-dismissible"><p><strong>✓ フッター設定を保存しました!</strong></p></div>';
    }
    
    $footer = riff_get_settings('footer');
    
    // デフォルト値の設定
    $defaults = array(
        'background_color' => '#4A7CC7',
        'main_text' => 'LINEならお写真を送るだけ！最短15分でお見積もりします',
        'business_hours' => '受付時間 8:00 〜 24:00',
        'phone' => '0123-45-6789',
        'phone_subtext' => 'お急ぎの方はお電話でご相談ください',
        'line_button_text' => 'LINEで無料見積',
        'line_button_url' => '#',
        'line_button_color' => '#06C755',
        'email_button_text' => 'メールで無料見積',
        'email_button_url' => '#',
        'email_button_bg_color' => '#FFFFFF',
        'email_button_text_color' => '#4A7CC7',
        'notice_text' => '※3営業日以内に返信いたします',
        'privacy_text' => 'プライバシーポリシー',
        'privacy_url' => '#',
        'copyright' => '© 2025 demo7',
    );
    
    $footer = array_merge($defaults, $footer);
    ?>
    <div class="wrap riff-admin-wrap">
        <h1><i class="dashicons dashicons-editor-aligncenter"></i> フッター設定</h1>
        
        <form method="post" class="riff-form">
            <?php wp_nonce_field('riff_footer_settings'); ?>
            
            <div class="riff-card">
                <h2>デザイン設定</h2>
                
                <table class="form-table">
                    <tr>
                        <th><label for="background_color">背景色</label></th>
                        <td>
                            <input type="text" name="background_color" id="background_color" value="<?php echo esc_attr($footer['background_color']); ?>" class="riff-color-picker">
                            <p class="description">フッター全体の背景色を設定します</p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="riff-card">
                <h2>メインコンテンツ</h2>
                
                <table class="form-table">
                    <tr>
                        <th><label for="main_text">メインテキスト</label></th>
                        <td>
                            <input type="text" name="main_text" id="main_text" value="<?php echo esc_attr($footer['main_text']); ?>" class="large-text">
                            <p class="description">フッター中央に表示される大きなテキスト。PC改行: <code>&lt;br class="pc"&gt;</code> / スマホ改行: <code>&lt;br class="sp"&gt;</code></p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="riff-card">
                <h2>電話情報（左側エリア）</h2>
                
                <table class="form-table">
                    <tr>
                        <th><label for="business_hours">受付時間</label></th>
                        <td>
                            <input type="text" name="business_hours" id="business_hours" value="<?php echo esc_attr($footer['business_hours']); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="phone">電話番号</label></th>
                        <td>
                            <input type="text" name="phone" id="phone" value="<?php echo esc_attr($footer['phone']); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="phone_subtext">電話番号下のテキスト</label></th>
                        <td>
                            <input type="text" name="phone_subtext" id="phone_subtext" value="<?php echo esc_attr($footer['phone_subtext']); ?>" class="large-text">
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="riff-card">
                <h2>ボタン設定</h2>
                
                <table class="form-table">
                    <tr>
                        <th colspan="2"><strong><i class="dashicons dashicons-admin-comments"></i> LINEボタン</strong></th>
                    </tr>
                    
                    <tr>
                        <th><label for="line_button_text">ボタンテキスト</label></th>
                        <td>
                            <input type="text" name="line_button_text" id="line_button_text" value="<?php echo esc_attr($footer['line_button_text']); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="line_button_url">リンクURL</label></th>
                        <td>
                            <input type="url" name="line_button_url" id="line_button_url" value="<?php echo esc_url($footer['line_button_url']); ?>" class="large-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="line_button_color">ボタン背景色</label></th>
                        <td>
                            <input type="text" name="line_button_color" id="line_button_color" value="<?php echo esc_attr($footer['line_button_color']); ?>" class="riff-color-picker">
                        </td>
                    </tr>
                    
                    <tr>
                        <th colspan="2"><strong><i class="dashicons dashicons-email"></i> メールボタン</strong></th>
                    </tr>
                    
                    <tr>
                        <th><label for="email_button_text">ボタンテキスト</label></th>
                        <td>
                            <input type="text" name="email_button_text" id="email_button_text" value="<?php echo esc_attr($footer['email_button_text']); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="email_button_url">リンクURL</label></th>
                        <td>
                            <input type="url" name="email_button_url" id="email_button_url" value="<?php echo esc_url($footer['email_button_url']); ?>" class="large-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="email_button_bg_color">ボタン背景色</label></th>
                        <td>
                            <input type="text" name="email_button_bg_color" id="email_button_bg_color" value="<?php echo esc_attr($footer['email_button_bg_color']); ?>" class="riff-color-picker">
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="email_button_text_color">ボタン文字色</label></th>
                        <td>
                            <input type="text" name="email_button_text_color" id="email_button_text_color" value="<?php echo esc_attr($footer['email_button_text_color']); ?>" class="riff-color-picker">
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="riff-card">
                <h2>その他のテキスト</h2>
                
                <table class="form-table">
                    <tr>
                        <th><label for="notice_text">注意書きテキスト</label></th>
                        <td>
                            <input type="text" name="notice_text" id="notice_text" value="<?php echo esc_attr($footer['notice_text']); ?>" class="large-text">
                            <p class="description">ボタン下に表示される注意書き</p>
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="riff-card">
                <h2>フッター下部</h2>
                
                <table class="form-table">
                    <tr>
                        <th><label for="privacy_text">プライバシーポリシーテキスト</label></th>
                        <td>
                            <input type="text" name="privacy_text" id="privacy_text" value="<?php echo esc_attr($footer['privacy_text']); ?>" class="regular-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="privacy_url">プライバシーポリシーURL</label></th>
                        <td>
                            <input type="url" name="privacy_url" id="privacy_url" value="<?php echo esc_url($footer['privacy_url']); ?>" class="large-text">
                        </td>
                    </tr>
                    
                    <tr>
                        <th><label for="copyright">コピーライト</label></th>
                        <td>
                            <input type="text" name="copyright" id="copyright" value="<?php echo esc_attr($footer['copyright']); ?>" class="large-text">
                        </td>
                    </tr>
                </table>
            </div>
            
            <div class="riff-card">
                <h2>SNSリンク設定（フッター）</h2>
                <p class="description" style="padding:0 10px 15px;">有効にしてURLを入力すると、フッターの電話番号下にSNSアイコンが表示されます。</p>
                <table class="form-table">
                    <?php riff_render_sns_admin_fields('footer_sns', isset($footer['sns_links']) ? $footer['sns_links'] : array()); ?>
                </table>
            </div>

            <p class="submit">
                <button type="submit" name="riff_save_footer" class="button button-primary button-large">
                    <i class="dashicons dashicons-yes"></i> 設定を保存
                </button>
            </p>
        </form>
    </div>
    <?php
}
?>