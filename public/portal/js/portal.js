/**
 * PORTAL DISKOMINFOTIK - MODERN FUTURISTIC JAVASCRIPT
 * Handles scrolling animations, Swiper carousels, Isotope filtering, GLightbox, and custom animations.
 */

(function() {
  "use strict";

  /**
   * Apply .scrolled class to body when page is scrolled down
   */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader) return;
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }

  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  /**
   * Mobile nav toggle
   */
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');
  if (mobileNavToggleBtn) {
    mobileNavToggleBtn.addEventListener('click', function(e) {
      document.querySelector('body').classList.toggle('mobile-nav-active');
      this.classList.toggle('bi-list');
      this.classList.toggle('bi-x');
    });
  }

  /**
   * Hide mobile nav on same-page/hash links click
   */
  document.querySelectorAll('#navmenu a').forEach(navlink => {
    navlink.addEventListener('click', () => {
      if (document.querySelector('.mobile-nav-active')) {
        document.querySelector('body').classList.remove('mobile-nav-active');
        if (mobileNavToggleBtn) {
          mobileNavToggleBtn.classList.add('bi-list');
          mobileNavToggleBtn.classList.remove('bi-x');
        }
      }
    });
  });

  /**
   * Toggle mobile nav dropdowns
   */
  document.querySelectorAll('.navmenu .dropdown > a').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      if (window.innerWidth < 1200) {
        e.preventDefault();
        this.parentNode.classList.toggle('active');
        const nextEl = this.nextElementSibling;
        if (nextEl) {
          nextEl.classList.toggle('dropdown-active');
        }
      }
    });
  });

  /**
   * Preloader
   */
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      setTimeout(() => {
        preloader.style.opacity = '0';
        setTimeout(() => {
          preloader.remove();
        }, 500);
      }, 300);
    });
  }

  /**
   * Scroll top button
   */
  let scrollTop = document.querySelector('.scroll-top');
  if (scrollTop) {
    const toggleScrollTop = () => {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    };
    scrollTop.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });
    window.addEventListener('load', toggleScrollTop);
    document.addEventListener('scroll', toggleScrollTop);
  }

  /**
   * Animation on scroll (AOS) init
   */
  function aosInit() {
    if (typeof AOS !== 'undefined') {
      AOS.init({
        duration: 800,
        easing: 'ease-out-back',
        once: true,
        mirror: false
      });
    }
  }
  window.addEventListener('load', aosInit);

  /**
   * GLightbox zoomable portfolio
   */
  if (typeof GLightbox !== 'undefined') {
    window.glightbox = GLightbox({
      selector: '.glightbox',
      zoomable: true,
      draggable: true,
      loop: true
    });
  }

  /**
   * Isotope filter and layout initialization
   */
  window.addEventListener('load', () => {
    document.querySelectorAll('.isotope-layout').forEach(function(isotopeItem) {
      let layout = isotopeItem.getAttribute('data-layout') ?? 'masonry';
      let filter = isotopeItem.getAttribute('data-default-filter') ?? '*';
      let sort = isotopeItem.getAttribute('data-sort') ?? 'original-order';

      let container = isotopeItem.querySelector('.isotope-container');
      if (container && typeof Isotope !== 'undefined') {
        imagesLoaded(container, function() {
          let initIsotope = new Isotope(container, {
            itemSelector: '.isotope-item',
            layoutMode: layout,
            filter: filter,
            sortBy: sort
          });

          isotopeItem.querySelectorAll('.isotope-filters li').forEach(function(filterBtn) {
            filterBtn.addEventListener('click', function() {
              isotopeItem.querySelector('.isotope-filters .filter-active').classList.remove('filter-active');
              this.classList.add('filter-active');
              initIsotope.arrange({
                filter: this.getAttribute('data-filter')
              });
              if (typeof aosInit === 'function') {
                aosInit();
              }
            });
          });
        });
      }
    });
  });

  /**
   * Frequently Asked Questions / Awards Toggle
   */
  document.querySelectorAll('.faq-item h3, .faq-item .faq-toggle').forEach((el) => {
    el.addEventListener('click', () => {
      const parent = el.closest('.faq-item');
      if (parent) {
        parent.classList.toggle('faq-active');
      }
    });
  });

  /**
   * Correct scrolling position upon page load for URLs containing hash links.
   */
  window.addEventListener('load', function() {
    if (window.location.hash) {
      const targetSection = document.querySelector(window.location.hash);
      if (targetSection) {
        setTimeout(() => {
          let scrollMarginTop = getComputedStyle(targetSection).scrollMarginTop;
          window.scrollTo({
            top: targetSection.offsetTop - parseInt(scrollMarginTop || 0) - 80,
            behavior: 'smooth'
          });
        }, 100);
      }
    }
  });

  /**
   * Navmenu Scrollspy
   */
  let navmenulinks = document.querySelectorAll('.navmenu a');
  function navmenuScrollspy() {
    navmenulinks.forEach(navmenulink => {
      if (!navmenulink.hash) return;
      let section = document.querySelector(navmenulink.hash);
      if (!section) return;
      let position = window.scrollY + 200;
      if (position >= section.offsetTop && position <= (section.offsetTop + section.offsetHeight)) {
        document.querySelectorAll('.navmenu a.active').forEach(link => link.classList.remove('active'));
        navmenulink.classList.add('active');
      } else {
        navmenulink.classList.remove('active');
      }
    });
  }
  window.addEventListener('load', navmenuScrollspy);
  document.addEventListener('scroll', navmenuScrollspy);

  /**
   * Swiper sliders initialization
   */
  window.addEventListener('load', () => {
    // 1. Hero Carousel Swiper
    if (document.querySelector('.hero-swiper')) {
      new Swiper('.hero-swiper', {
        loop: true,
        speed: 1000,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false
        },
        effect: 'fade',
        fadeEffect: {
          crossFade: true
        },
        pagination: {
          el: '.hero-swiper-pagination',
          clickable: true
        },
        navigation: {
          nextEl: '.hero-swiper-button-next',
          prevEl: '.hero-swiper-button-prev'
        }
      });
    }

    // 2. Team Organizasi Swiper
    if (document.querySelector('.teamSwiper')) {
      new Swiper('.teamSwiper', {
        loop: true,
        speed: 800,
        spaceBetween: 25,
        autoplay: {
          delay: 4000,
          disableOnInteraction: false
        },
        pagination: {
          el: '.team-swiper-pagination',
          clickable: true
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev'
        },
        breakpoints: {
          0: { slidesPerView: 1 },
          576: { slidesPerView: 1 },
          768: { slidesPerView: 2 },
          992: { slidesPerView: 3 },
          1200: { slidesPerView: 4 }
        }
      });
    }

    // 3. News Swiper (Berita Slider)
    if (document.querySelector('.beritaSwiper')) {
      new Swiper('.beritaSwiper', {
        loop: true,
        speed: 800,
        spaceBetween: 25,
        autoplay: {
          delay: 4500,
          disableOnInteraction: false
        },
        pagination: {
          el: '.berita-swiper-pagination',
          clickable: true
        },
        breakpoints: {
          0: { slidesPerView: 1 },
          768: { slidesPerView: 2 },
          1200: { slidesPerView: 3 }
        }
      });
    }

    // 4. Unduhan Swiper (Downloads Slider)
    if (document.querySelector('.unduhanSwiper')) {
      new Swiper('.unduhanSwiper', {
        loop: true,
        speed: 800,
        spaceBetween: 25,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false
        },
        pagination: {
          el: '.unduhan-swiper-pagination',
          clickable: true
        },
        breakpoints: {
          0: { slidesPerView: 1 },
          992: { slidesPerView: 2 }
        }
      });
    }

    // 5. Testimonial / Ulasan Swiper
    if (document.querySelector('.testimonialsSwiper')) {
      new Swiper('.testimonialsSwiper', {
        loop: true,
        speed: 800,
        spaceBetween: 25,
        autoplay: {
          delay: 4500,
          disableOnInteraction: false
        },
        pagination: {
          el: '.testimonials-swiper-pagination',
          clickable: true
        },
        breakpoints: {
          0: { slidesPerView: 1 },
          992: { slidesPerView: 3 }
        }
      });
    }

    // 6. Tautan / Link Partner Swiper
    if (document.querySelector('.tautanSwiper')) {
      new Swiper('.tautanSwiper', {
        loop: true,
        speed: 600,
        autoplay: {
          delay: 3500,
          disableOnInteraction: false
        },
        pagination: {
          el: '.tautan-swiper-pagination',
          clickable: true
        },
        breakpoints: {
          320: { slidesPerView: 2, spaceBetween: 20 },
          576: { slidesPerView: 3, spaceBetween: 30 },
          768: { slidesPerView: 4, spaceBetween: 40 },
          992: { slidesPerView: 5, spaceBetween: 50 }
        }
      });
    }
  });

  /**
   * Running Text Ticker
   */
  document.addEventListener("DOMContentLoaded", function () {
    const tickerItems = document.querySelectorAll("#info-list li");
    if (tickerItems.length > 0) {
      let index = 0;
      const showItem = (i) => {
        tickerItems.forEach(el => {
          el.style.opacity = '0';
          el.style.transform = 'translateY(100%)';
        });
        tickerItems[i].style.opacity = '1';
        tickerItems[i].style.transform = 'translateY(0)';
      };

      showItem(index);
      setInterval(() => {
        index = (index + 1) % tickerItems.length;
        showItem(index);
      }, 3500);
    }
  });

  /**
   * Day & Date Calculator
   */
  document.addEventListener("DOMContentLoaded", function () {
    const el = document.getElementById("tanggal-text");
    if (el) {
      const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
      const bulan = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
      ];
      const now = new Date();
      const hariIni = hari[now.getDay()];
      const tanggal = now.getDate();
      const bulanIni = bulan[now.getMonth()];
      const tahun = now.getFullYear();

      el.innerHTML = `${hariIni}, ${tanggal} ${bulanIni} ${tahun}`;
    }
  });

  /**
   * Initiate PureCounter if loaded
   */
  if (typeof PureCounter !== 'undefined') {
    new PureCounter();
  }

  /**
   * Theme Switcher Logic
   */
  document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtn = document.getElementById('theme-toggle');
    if (themeToggleBtn) {
      themeToggleBtn.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
      });
    }
  });

})();
