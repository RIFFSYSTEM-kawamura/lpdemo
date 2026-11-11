/**
 * Testimonials Slider - フェード切り替え版
 * 途切れなく滑らかにフェードで切り替わるスライダー
 */

(function() {
  'use strict';

  class TestimonialsSliderManager {
    constructor(sliderId, options = {}) {
      this.sliderId = sliderId;
      this.slider = document.querySelector(`[data-slider="${sliderId}"]`);
      this.slides = this.slider ? Array.from(this.slider.querySelectorAll('.testimonials-slide')) : [];
      this.dotsContainer = document.querySelector(`[data-dots="${sliderId}"]`);
      
      this.options = {
        autoplay: true,
        autoplayDelay: 3000, // 5秒ごとに自動切り替え
        transitionSpeed: 600, // フェード速度（ミリ秒）
        pauseOnHover: true,
        ...options
      };
      
      this.currentSlide = 0;
      this.autoplayTimer = null;
      this.isTransitioning = false;
      
      if (this.slider && this.slides.length > 0) {
        this.init();
      }
    }

    init() {
      // 最初のスライドをアクティブに
      if (this.slides.length > 0) {
        this.slides[0].classList.add('active');
      }
      
      // ドットを生成
      this.createDots();
      
      // イベントをアタッチ
      this.attachEvents();
      
      // 自動再生開始
      if (this.options.autoplay && this.slides.length > 1) {
        this.startAutoplay();
      }
      
      console.log('[TestimonialsSlider] 初期化完了:', {
        sliderId: this.sliderId,
        slideCount: this.slides.length,
        autoplayDelay: this.options.autoplayDelay + 'ms'
      });
    }

    createDots() {
      if (!this.dotsContainer || this.slides.length <= 1) {
        if (this.dotsContainer) {
          this.dotsContainer.style.display = 'none';
        }
        return;
      }
      
      this.dotsContainer.innerHTML = '';
      this.dotsContainer.style.display = 'flex';
      
      this.slides.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.className = 'testimonials-dot';
        if (index === 0) {
          dot.classList.add('active');
        }
        
        dot.addEventListener('click', () => this.goToSlide(index));
        this.dotsContainer.appendChild(dot);
      });
    }

    goToSlide(index) {
      if (this.isTransitioning) return;
      if (index === this.currentSlide) return;
      if (index < 0 || index >= this.slides.length) return;
      
      this.isTransitioning = true;
      
      // 現在のスライドを非アクティブに
      this.slides[this.currentSlide].classList.remove('active');
      this.updateDot(this.currentSlide, false);
      
      // 新しいスライドをアクティブに
      this.currentSlide = index;
      this.slides[this.currentSlide].classList.add('active');
      this.updateDot(this.currentSlide, true);
      
      // トランジション完了後にフラグをリセット
      setTimeout(() => {
        this.isTransitioning = false;
      }, this.options.transitionSpeed);
      
      // 自動再生をリセット
      if (this.options.autoplay) {
        this.resetAutoplay();
      }
    }

    updateDot(index, isActive) {
      if (!this.dotsContainer) return;
      
      const dots = this.dotsContainer.querySelectorAll('.testimonials-dot');
      if (dots[index]) {
        if (isActive) {
          dots[index].classList.add('active');
        } else {
          dots[index].classList.remove('active');
        }
      }
    }

    nextSlide() {
      const next = (this.currentSlide + 1) % this.slides.length;
      this.goToSlide(next);
    }

    prevSlide() {
      const prev = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
      this.goToSlide(prev);
    }

    startAutoplay() {
      if (this.slides.length <= 1) return;
      
      this.stopAutoplay();
      this.autoplayTimer = setInterval(() => {
        this.nextSlide();
      }, this.options.autoplayDelay);
    }

    stopAutoplay() {
      if (this.autoplayTimer) {
        clearInterval(this.autoplayTimer);
        this.autoplayTimer = null;
      }
    }

    resetAutoplay() {
      this.stopAutoplay();
      this.startAutoplay();
    }

    attachEvents() {
      // ホバーで一時停止
      if (this.options.pauseOnHover && this.slider) {
        this.slider.addEventListener('mouseenter', () => {
          this.stopAutoplay();
        });
        
        this.slider.addEventListener('mouseleave', () => {
          if (this.options.autoplay && this.slides.length > 1) {
            this.startAutoplay();
          }
        });
      }
      
      // タッチスワイプ対応
      if (this.slider) {
        let touchStartX = 0;
        let touchEndX = 0;
        
        this.slider.addEventListener('touchstart', (e) => {
          touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });
        
        this.slider.addEventListener('touchend', (e) => {
          touchEndX = e.changedTouches[0].screenX;
          this.handleSwipe(touchStartX, touchEndX);
        }, { passive: true });
      }
      
      // キーボード操作
      document.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowLeft') {
          this.prevSlide();
        } else if (e.key === 'ArrowRight') {
          this.nextSlide();
        }
      });
      
      // ページが非表示になったら自動再生を停止
      document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
          this.stopAutoplay();
        } else if (this.options.autoplay && this.slides.length > 1) {
          this.startAutoplay();
        }
      });
    }

    handleSwipe(startX, endX) {
      const swipeThreshold = 50;
      const diff = startX - endX;
      
      if (Math.abs(diff) > swipeThreshold) {
        if (diff > 0) {
          // 左スワイプ（次へ）
          this.nextSlide();
        } else {
          // 右スワイプ（前へ）
          this.prevSlide();
        }
      }
    }

    // 速度を動的に変更するメソッド
    updateSpeed(newDelay) {
      this.options.autoplayDelay = newDelay;
      if (this.options.autoplay) {
        this.resetAutoplay();
      }
      console.log('[TestimonialsSlider] 速度更新:', newDelay + 'ms');
    }

    // インスタンスを破棄
    destroy() {
      this.stopAutoplay();
      console.log('[TestimonialsSlider] 破棄');
    }
  }

  // グローバルに公開
  window.TestimonialsSlider = {
    init: function(sliderId, options = {}) {
      return new TestimonialsSliderManager(sliderId, options);
    },
    Manager: TestimonialsSliderManager
  };

  // ページ読み込み時に自動初期化
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initTestimonialsSlider);
  } else {
    initTestimonialsSlider();
  }

  function initTestimonialsSlider() {
    const testimonialsSlider = document.querySelector('[data-slider="testimonials"]');
    
    if (testimonialsSlider) {
      // スライダーを初期化（5秒ごとに切り替え）
      window.testimonialsSliderInstance = window.TestimonialsSlider.init('testimonials', {
        autoplay: true,
        autoplayDelay: 3000, // 5秒
        transitionSpeed: 600, // 0.6秒のフェード
        pauseOnHover: true
      });
      
      console.log('[TestimonialsSlider] 初期化完了（速度: 5秒）');
    }
  }

})();