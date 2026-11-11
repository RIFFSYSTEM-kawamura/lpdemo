<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<?php
$header = riff_get_settings('header');
?>

<!-- 動的スタイル -->
<style>
    :root {
        --header-text-color-scrolled: <?php echo esc_attr($header['text_color_scrolled']); ?>;
        --logo-width: <?php echo esc_attr($header['logo_width']); ?>px;
        --logo-height: <?php echo esc_attr($header['logo_height']); ?>px;
    }
</style>

<header class="site-header" id="masthead">
    <div class="header-container">
        <div class="site-branding">
            <?php if ($header['logo_type'] === 'image' && !empty($header['logo_image'])) : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo-link">
                    <img src="<?php echo esc_url($header['logo_image']); ?>" 
                         alt="<?php bloginfo('name'); ?>" 
                         class="site-logo-image">
                </a>
            <?php else : ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo">
                    <?php echo riff_kses_br($header['logo_text']); ?>
                </a>
            <?php endif; ?>
        </div>
        
        <nav class="main-navigation" id="site-navigation">
            <?php
            if (has_nav_menu('primary')) {
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class'     => 'nav-menu',
                    'container'      => false,
                ));
            } else {
                echo '<ul class="nav-menu">';
                echo '<li><a href="#about">About</a></li>';
                echo '<li><a href="#services">Services</a></li>';
                echo '<li><a href="#works">Works</a></li>';
                echo '<li><a href="#contact">Contact</a></li>';
                echo '</ul>';
            }
            ?>
            
            <?php if (!empty($header['cta_text'])) : ?>
                <a href="<?php echo esc_url($header['cta_url']); ?>" class="header-cta">
                    <?php echo riff_kses_br($header['cta_text']); ?>
                </a>
            <?php endif; ?>

            <?php
            $header_sns     = isset($header['sns_links']) ? $header['sns_links'] : array();
            $sns_platforms  = riff_get_sns_platforms();
            $has_header_sns = false;
            foreach ($sns_platforms as $k => $p) {
                if (!empty($header_sns[$k]['enabled'])) {
                    $has_header_sns = true;
                    break;
                }
            }
            if ($has_header_sns) : ?>
                <div class="mobile-sns-links">
                    <?php foreach ($sns_platforms as $k => $p) :
                        if (empty($header_sns[$k]['enabled'])) continue;
                    ?>
                        <a href="<?php echo esc_url($header_sns[$k]['url']); ?>"
                           class="mobile-sns-link sns-<?php echo esc_attr($k); ?>"
                           target="_blank" rel="noopener noreferrer"
                           aria-label="<?php echo esc_attr($p['label']); ?>">
                            <i class="<?php echo esc_attr($p['icon']); ?>"></i>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </nav>
        
        <button class="mobile-menu-toggle" aria-label="メニュー" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>