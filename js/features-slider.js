/**
 * Features Slider - 連続スクロール版（速度変更対応）
 * 途切れることなく流れ続けるスライダー
 */

(function() {
  'use strict';

  class FeaturesSliderManager {
    constructor(sliderId, options = {}) {
      this.sliderId = sliderId;
      this.container = document.querySelector(`[data-slider="${sliderId}"]`);
      this.track = this.container ? this.container.querySelector('[class$="-slider-track"]') : null;
      this.originalSlides = this.track ? Array.from(this.track.children) : [];
      this.dotsContainer = document.querySelector(`[data-dots="${sliderId}"]`);
      
      this.options = {
        autoplay: true,
        speed: 15, // スクロール速度（秒）低いほど速い
        pauseOnHover: true,
        continuousScroll: true,
        slidesPerView: {
          desktop: 5,
          tablet: 3,
          mobile: 2,
          small: 1
        },
        ...options
      };
      
      this.originalSlideCount = this.originalSlides.length;
      this.currentSlidesPerView = this.getSlidesPerView();
      this.dotClassName = this.getDotClassName();
      this.animationPaused = false;
      this.animationId = 'features-scroll-' + Date.now(); // ユニークID
      
      if (this.container && this.track && this.originalSlides.length > 0) {
        this.init();
      }
    }

    getDotClassName() {
      const containerClass = this.container.className.split(' ')[0];
      const prefix = containerClass.replace('-slider-container', '');
      return `${prefix}-dot`;
    }

    init() {
      this.setupContinuousScroll();
      
      // ドットナビゲーションを非表示（連続スクロール時は不要）
      if (this.dotsContainer) {
        this.dotsContainer.style.display = 'none';
      }
      
      this.applyContinuousAnimation();
      this.attachEvents();
      
      console.log('[FeaturesSlider] 連続スクロール初期化:', {
        sliderId: this.sliderId,
        speed: this.options.speed + '秒',
        animationId: this.animationId
      });
    }

    getSlidesPerView() {
      const width = window.innerWidth;
      const breakpoints = this.options.slidesPerView;
      
      if (width <= 480) {
        return breakpoints.small;
      } else if (width <= 768) {
        return breakpoints.mobile;
      } else if (width <= 992) {
        return breakpoints.tablet;
      } else {
        return breakpoints.desktop;
      }
    }

    setupContinuousScroll() {
      // 元のスライドを2セット複製して連続スクロールを実現
      const cloneSet1 = this.originalSlides.map(slide => {
        const clone = slide.cloneNode(true);
        clone.classList.add('clone-set1');
        return clone;
      });
      
      const cloneSet2 = this.originalSlides.map(slide => {
        const clone = slide.cloneNode(true);
        clone.classList.add('clone-set2');
        return clone;
      });
      
      // 複製したスライドを追加
      cloneSet1.forEach(clone => this.track.appendChild(clone));
      cloneSet2.forEach(clone => this.track.appendChild(clone));
    }

    applyContinuousAnimation() {
      // 古いスタイルを削除
      const oldStyles = document.querySelectorAll('[id^="features-continuous-scroll-"]');
      oldStyles.forEach(style => style.remove());
      
      // 新しいスタイルを作成
      const styleId = 'features-continuous-scroll-' + this.animationId;
      const styleElement = document.createElement('style');
      styleElement.id = styleId;
      
      const duration = this.options.speed;
      
      // CSSアニメーションを動的に生成
      styleElement.textContent = `
        @keyframes ${this.animationId} {
          0% {
            transform: translateX(0%);
          }
          100% {
            transform: translateX(-${100 / 3}%);
          }
        }
        
        [data-slider="${this.sliderId}"] .features-slider-track {
          animation: ${this.animationId} ${duration}s linear infinite !important;
          will-change: transform;
        }
        
        [data-slider="${this.sliderId}"] .features-slider-track.paused {
          animation-play-state: paused !important;
        }
      `;
      
      document.head.appendChild(styleElement);
      
      // trackに直接スタイルも適用（念のため）
      this.track.style.animation = `${this.animationId} ${duration}s linear infinite`;
      
      console.log('[FeaturesSlider] アニメーション適用:', {
        name: this.animationId,
        duration: duration + 's'
      });
    }

    attachEvents() {
      // ホバー時に一時停止
      if (this.options.pauseOnHover) {
        this.container.addEventListener('mouseenter', () => {
          this.track.classList.add('paused');
          this.animationPaused = true;
        });
        
        this.container.addEventListener('mouseleave', () => {
          this.track.classList.remove('paused');
          this.animationPaused = false;
        });
      }
      
      // リサイズ時の処理
      let resizeTimer;
      window.addEventListener('resize', () => {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(() => {
          const newSlidesPerView = this.getSlidesPerView();
          if (newSlidesPerView !== this.currentSlidesPerView) {
            this.currentSlidesPerView = newSlidesPerView;
            // アニメーションを再適用
            this.applyContinuousAnimation();
          }
        }, 250);
      });
      
      // タブが非表示になった時にアニメーションを一時停止
      document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
          this.track.classList.add('paused');
        } else if (!this.animationPaused) {
          this.track.classList.remove('paused');
        }
      });
    }

    // 速度を動的に変更するメソッド
    updateSpeed(newSpeed) {
      this.options.speed = newSpeed;
      this.applyContinuousAnimation();
      console.log('[FeaturesSlider] 速度更新:', newSpeed + '秒');
    }

    // インスタンスを破棄
    destroy() {
      const styleElements = document.querySelectorAll('[id^="features-continuous-scroll-"]');
      styleElements.forEach(el => el.remove());
      console.log('[FeaturesSlider] 破棄');
    }
  }

  // グローバルに公開
  window.FeaturesSlider = {
    init: function(sliderId, options = {}) {
      return new FeaturesSliderManager(sliderId, options);
    },
    Manager: FeaturesSliderManager
  };

  // ページ読み込み時に自動初期化
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initFeaturesSlider);
  } else {
    initFeaturesSlider();
  }

  function initFeaturesSlider() {
    const featuresContainer = document.querySelector('[data-slider="features-slider"]');
    
    if (featuresContainer) {
      // スライダーを初期化（速度30秒で滑らかにスクロール）
      window.featuresSliderInstance = window.FeaturesSlider.init('features-slider', {
        speed: 15, //  速度を30秒に設定（より滑らかに）
        pauseOnHover: true
      });
      
      console.log('[FeaturesSlider] 初期化完了（速度: 30秒）');
    }
  }

})();