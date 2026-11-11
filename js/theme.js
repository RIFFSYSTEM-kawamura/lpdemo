/**
 * RIFF Theme JavaScript - ドットナビゲーション削除版
 */

(function($) {
    'use strict';

    $(document).ready(function() {
        
        // ===================================
        // ヒーロースライダー
        // ===================================
        initHeroSlider();
        
        // ===================================
        // ヘッダースクロール効果(Heroと一体化 + Zennスタイル)
        // ===================================
        const header = $('.site-header');
        const heroSection = $('.hero-section');
        let lastScroll = 0;
        const scrollThreshold = 5; // スクロール検知の閾値
        
        // ヘッダー状態を更新する関数
        function updateHeaderState() {
            const currentScroll = $(window).scrollTop();
            const heroHeight = heroSection.length ? heroSection.outerHeight() : 600;
            const heroThreshold = heroHeight - 100;
            
            // ページトップ(Hero内):透明状態
            if (currentScroll < heroThreshold) {
                header.removeClass('scrolled hide').addClass('at-top show');
                lastScroll = currentScroll;
                return;
            }
            
            // Hero通過後:白背景
            header.removeClass('at-top').addClass('scrolled');
            
            // スクロール方向の判定(小さなスクロールは無視)
            if (Math.abs(currentScroll - lastScroll) < scrollThreshold) {
                return;
            }
            
            if (currentScroll > lastScroll && currentScroll > heroThreshold) {
                // 下にスクロール:ヘッダーを隠す
                header.removeClass('show').addClass('hide');
            } else {
                // 上にスクロール:ヘッダーを表示
                header.removeClass('hide').addClass('show');
            }
            
            lastScroll = currentScroll;
        }
        
        // ページロード直後に即座に状態を設定
        const initialScroll = $(window).scrollTop();
        const heroHeight = heroSection.length ? heroSection.outerHeight() : 600;
        
        if (initialScroll < heroHeight - 100) {
            // 初期状態:Hero内なので透明
            header.addClass('at-top show');
        } else {
            // 初期状態:Hero通過後なので白背景
            header.addClass('scrolled show');
        }
        
        // スクロール時に状態を更新
        $(window).on('scroll', updateHeaderState);
        
        // リサイズ時にも再計算
        $(window).on('resize', function() {
            updateHeaderState();
        });
        
        // ===================================
        // モバイルメニュートグル
        // ===================================
        const mobileMenuToggle = $('.mobile-menu-toggle');
        const mainNavigation = $('.main-navigation');
        
        mobileMenuToggle.on('click', function() {
            $(this).toggleClass('active');
            mainNavigation.toggleClass('active');
            header.toggleClass('mobile-menu-open');
            $('body').toggleClass('menu-open');
            
            // アクセシビリティ
            const expanded = $(this).attr('aria-expanded') === 'true';
            $(this).attr('aria-expanded', !expanded);
        });
        
        // メニューリンククリック時にメニューを閉じる
        $('.main-navigation a').on('click', function() {
            if ($(window).width() <= 768) {
                mobileMenuToggle.removeClass('active');
                mainNavigation.removeClass('active');
                header.removeClass('mobile-menu-open');
                $('body').removeClass('menu-open');
                mobileMenuToggle.attr('aria-expanded', 'false');
            }
        });
        
        // ウィンドウサイズ変更時の処理
        $(window).on('resize', function() {
            if ($(window).width() > 768) {
                mobileMenuToggle.removeClass('active');
                mainNavigation.removeClass('active');
                header.removeClass('mobile-menu-open');
                $('body').removeClass('menu-open');
                mobileMenuToggle.attr('aria-expanded', 'false');
            }
        });
        
        // ===================================
        // スムーススクロール
        // ===================================
        $('a[href^="#"]').on('click', function(e) {
            const target = $(this.getAttribute('href'));
            
            if (target.length) {
                e.preventDefault();
                const headerHeight = $('.site-header').outerHeight();
                const targetPosition = target.offset().top - headerHeight;
                
                $('html, body').stop().animate({
                    scrollTop: targetPosition
                }, 800, 'swing');
            }
        });
        
        // ===================================
        // フェードインアニメーション
        // ===================================
        function fadeInOnScroll() {
            $('.fade-in-element').each(function() {
                const elementTop = $(this).offset().top;
                const elementBottom = elementTop + $(this).outerHeight();
                const viewportTop = $(window).scrollTop();
                const viewportBottom = viewportTop + $(window).height();
                
                if (elementBottom > viewportTop && elementTop < viewportBottom) {
                    $(this).addClass('visible');
                }
            });
        }
        
        // 初回実行
        fadeInOnScroll();
        
        // スクロール時に実行
        $(window).on('scroll', fadeInOnScroll);
        
        // ===================================
        // パララックス効果(ヒーロー背景)
        // ===================================
        $(window).on('scroll', function() {
            const scrolled = $(window).scrollTop();
            const heroBackground = $('.hero-background');
            
            if (heroBackground.length) {
                const parallaxSpeed = 0.5;
                heroBackground.css('transform', 'translateY(' + (scrolled * parallaxSpeed) + 'px)');
            }
        });
        
        // ===================================
        // 動画の自動再生制御
        // ===================================
        const heroVideo = $('.hero-background video');
        
        if (heroVideo.length) {
            // 動画が読み込まれたら再生
            heroVideo.on('loadeddata', function() {
                this.play();
            });
            
            // ページから離れたら一時停止
            $(document).on('visibilitychange', function() {
                if (document.hidden) {
                    heroVideo[0].pause();
                } else {
                    heroVideo[0].play();
                }
            });
        }
        
        // ===================================
        // アクティブメニューハイライト
        // ===================================
        const sections = $('section[id]');
        const navItems = $('.nav-menu a[href^="#"]');
        
        $(window).on('scroll', function() {
            let current = '';
            const scrollPosition = $(window).scrollTop() + 100;
            
            sections.each(function() {
                const sectionTop = $(this).offset().top;
                const sectionHeight = $(this).outerHeight();
                
                if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                    current = $(this).attr('id');
                }
            });
            
            navItems.removeClass('active');
            navItems.filter('[href="#' + current + '"]').addClass('active');
        });
        
        // ===================================
        // ローディングアニメーション
        // ===================================
        $(window).on('load', function() {
            $('body').addClass('loaded');
        });
        
        // ===================================
        // 外部リンクの処理
        // ===================================
        $('a[href^="http"]').not('[href*="' + window.location.hostname + '"]').attr({
            target: '_blank',
            rel: 'noopener noreferrer'
        });
        
        // ===================================
        // カスタムイベント: ページ読み込み完了
        // ===================================
        $(window).on('load', function() {
            // カスタムイベントをトリガー
            $(document).trigger('riff_theme_loaded');
        });
        
    });
    
    // ===================================
    // ヒーロースライダー - ドットナビゲーション削除版
    // ===================================
    function initHeroSlider() {
        const settings = window.riffHeroSettings || {
            autoplay: true,
            autoplaySpeed: 5000,
            transitionSpeed: 1000,
            slideCount: 1
        };
        
        // 初期表示時に最初のスライドのコンテンツをアクティブに
        $('.hero-content-grid').first().addClass('active');
        $('.hero-content-grid').first().find('.hero-content').addClass('active');
        
        if (settings.slideCount <= 1) {
            return; // スライドが1つ以下なら何もしない
        }
        
        let currentSlide = 0;
        let isAnimating = false;
        let autoplayTimer = null;
        
        const $heroSlides = $('.hero-slide');
        const $heroContentGrids = $('.hero-content-grid');
        
        // スライド切り替え関数
        function goToSlide(index) {
            if (isAnimating) return;
            if (index === currentSlide) return;
            if (index < 0) index = settings.slideCount - 1;
            if (index >= settings.slideCount) index = 0;
            
            isAnimating = true;
            
            // 現在のスライドを非アクティブに
            $heroSlides.eq(currentSlide).removeClass('active');
            $heroContentGrids.eq(currentSlide).removeClass('active');
            $heroContentGrids.eq(currentSlide).find('.hero-content').removeClass('active');
            
            // 次のスライドをアクティブに
            $heroSlides.eq(index).addClass('active');
            $heroContentGrids.eq(index).addClass('active');
            
            // 少し遅延させてコンテンツをアニメーション
            setTimeout(function() {
                $heroContentGrids.eq(index).find('.hero-content').addClass('active');
            }, 100);
            
            currentSlide = index;
            
            setTimeout(function() {
                isAnimating = false;
            }, settings.transitionSpeed);
            
            // 自動再生をリセット
            if (settings.autoplay) {
                stopAutoplay();
                startAutoplay();
            }
        }
        
        // 次のスライドへ
        function nextSlide() {
            goToSlide(currentSlide + 1);
        }
        
        // 前のスライドへ(予備)
        function prevSlide() {
            goToSlide(currentSlide - 1);
        }
        
        // 自動再生開始
        function startAutoplay() {
            if (!settings.autoplay) return;
            autoplayTimer = setInterval(nextSlide, settings.autoplaySpeed);
        }
        
        // 自動再生停止
        function stopAutoplay() {
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                autoplayTimer = null;
            }
        }
        
        // キーボード操作
        $(document).on('keydown', function(e) {
            if (e.key === 'ArrowLeft') {
                prevSlide();
            } else if (e.key === 'ArrowRight') {
                nextSlide();
            }
        });
        
        // タッチスワイプ
        let touchStartX = 0;
        let touchEndX = 0;
        
        $('.hero-section').on('touchstart', function(e) {
            touchStartX = e.touches[0].clientX;
        });
        
        $('.hero-section').on('touchend', function(e) {
            touchEndX = e.changedTouches[0].clientX;
            handleSwipe();
        });
        
        function handleSwipe() {
            const swipeThreshold = 50;
            const diff = touchStartX - touchEndX;
            
            if (Math.abs(diff) > swipeThreshold) {
                if (diff > 0) {
                    nextSlide();
                } else {
                    prevSlide();
                }
            }
        }
        
        // 自動再生開始
        if (settings.autoplay) {
            startAutoplay();
        }
        
        // ページが非表示になったら自動再生を停止
        $(document).on('visibilitychange', function() {
            if (document.hidden) {
                stopAutoplay();
            } else if (settings.autoplay) {
                startAutoplay();
            }
        });
    }
    
    // ===================================
    // ユーティリティ関数
    // ===================================
    
    // デバウンス関数
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // スロットル関数
    function throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
    
    // グローバルに公開
    window.riffTheme = {
        debounce: debounce,
        throttle: throttle
    };
    
})(jQuery);