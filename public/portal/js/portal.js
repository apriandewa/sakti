/**
 * PORTAL DISKOMINFOTIK INHU — "NAVIGASI" DESIGN SYSTEM
 * Handles scrolling animations, Swiper carousels, Isotope filtering, GLightbox,
 * hero counters, hero sailboat animation, night-sky starfield (particles.js)
 * and hero search redirect.
 */

(function() {
  "use strict";

  /** Apply .scrolled class to body when page is scrolled down */
  function toggleScrolled() {
    const selectBody = document.querySelector('body');
    const selectHeader = document.querySelector('#header');
    if (!selectHeader) return;
    if (!selectHeader.classList.contains('scroll-up-sticky') && !selectHeader.classList.contains('sticky-top') && !selectHeader.classList.contains('fixed-top')) return;
    window.scrollY > 100 ? selectBody.classList.add('scrolled') : selectBody.classList.remove('scrolled');
  }
  document.addEventListener('scroll', toggleScrolled);
  window.addEventListener('load', toggleScrolled);

  /** Mobile nav toggle */
  const mobileNavToggleBtn = document.querySelector('.mobile-nav-toggle');
  if (mobileNavToggleBtn) {
    mobileNavToggleBtn.addEventListener('click', function() {
      document.querySelector('body').classList.toggle('mobile-nav-active');
      this.classList.toggle('bi-list');
      this.classList.toggle('bi-x');
    });
  }

  /** Hide mobile nav on same-page/hash links click */
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

  /** Toggle mobile nav dropdowns */
  document.querySelectorAll('.navmenu .dropdown > a').forEach(navmenu => {
    navmenu.addEventListener('click', function(e) {
      if (window.innerWidth < 1200) {
        e.preventDefault();
        this.parentNode.classList.toggle('active');
        const nextEl = this.nextElementSibling;
        if (nextEl) nextEl.classList.toggle('dropdown-active');
      }
    });
  });

  /** Preloader */
  const preloader = document.querySelector('#preloader');
  if (preloader) {
    window.addEventListener('load', () => {
      setTimeout(() => {
        preloader.style.opacity = '0';
        setTimeout(() => preloader.remove(), 500);
      }, 300);
    });
  }

  /** Scroll top button */
  let scrollTop = document.querySelector('.scroll-top');
  if (scrollTop) {
    const toggleScrollTop = () => {
      window.scrollY > 100 ? scrollTop.classList.add('active') : scrollTop.classList.remove('active');
    };
    scrollTop.addEventListener('click', (e) => {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
    window.addEventListener('load', toggleScrollTop);
    document.addEventListener('scroll', toggleScrollTop);
  }

  /** AOS init */
  function aosInit() {
    if (typeof AOS !== 'undefined') {
      AOS.init({ duration: 800, easing: 'ease-out-back', once: true, mirror: false });
    }
  }
  window.addEventListener('load', aosInit);

  /** GLightbox */
  if (typeof GLightbox !== 'undefined') {
    window.glightbox = GLightbox({ selector: '.glightbox', zoomable: true, draggable: true, loop: true });
  }

  /** Isotope filter and layout initialization */
  window.addEventListener('load', () => {
    document.querySelectorAll('.isotope-layout').forEach(function(isotopeItem) {
      let layout = isotopeItem.getAttribute('data-layout') ?? 'masonry';
      let filter = isotopeItem.getAttribute('data-default-filter') ?? '*';
      let sort = isotopeItem.getAttribute('data-sort') ?? 'original-order';
      let container = isotopeItem.querySelector('.isotope-container');
      if (container && typeof Isotope !== 'undefined') {
        imagesLoaded(container, function() {
          let initIsotope = new Isotope(container, { itemSelector: '.isotope-item', layoutMode: layout, filter: filter, sortBy: sort });
          isotopeItem.querySelectorAll('.isotope-filters li').forEach(function(filterBtn) {
            filterBtn.addEventListener('click', function() {
              isotopeItem.querySelector('.isotope-filters .filter-active').classList.remove('filter-active');
              this.classList.add('filter-active');
              initIsotope.arrange({ filter: this.getAttribute('data-filter') });
              if (typeof aosInit === 'function') aosInit();
            });
          });
        });
      }
    });
  });

  /** FAQ / Awards toggle */
  document.querySelectorAll('.faq-item h3, .faq-item .faq-toggle').forEach((el) => {
    el.addEventListener('click', () => {
      const parent = el.closest('.faq-item');
      if (parent) parent.classList.toggle('faq-active');
    });
  });

  /** Scroll to hash on load */
  window.addEventListener('load', function() {
    if (window.location.hash) {
      const targetSection = document.querySelector(window.location.hash);
      if (targetSection) {
        setTimeout(() => {
          let scrollMarginTop = getComputedStyle(targetSection).scrollMarginTop;
          window.scrollTo({ top: targetSection.offsetTop - parseInt(scrollMarginTop || 0) - 80, behavior: 'smooth' });
        }, 100);
      }
    }
  });

  /** Navmenu scrollspy */
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

  /** Swiper sliders initialization (image swiper removed from hero; carousels below kept) */
  window.addEventListener('load', () => {
    if (document.querySelector('.teamSwiper')) {
      new Swiper('.teamSwiper', {
        loop: true, speed: 800, spaceBetween: 25,
        autoplay: { delay: 4000, disableOnInteraction: false },
        pagination: { el: '.team-swiper-pagination', clickable: true },
        navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
        breakpoints: { 0: { slidesPerView: 1 }, 576: { slidesPerView: 1 }, 768: { slidesPerView: 2 }, 992: { slidesPerView: 3 }, 1200: { slidesPerView: 4 } }
      });
    }

    /** Bidang (Services) — 3D coverflow carousel, ref: jdih.kaboki.go.id */
    if (document.querySelector('.bidangSwiper')) {
      new Swiper('.bidangSwiper', {
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: 'auto',
        loop: true,
        speed: 700,
        autoplay: { delay: 4000, disableOnInteraction: false },
        coverflowEffect: { rotate: 30, stretch: 0, depth: 120, modifier: 1, slideShadows: false },
        pagination: { el: '.bidang-swiper-pagination', clickable: true }
      });
    }

    /** Berita (News) — 3D coverflow carousel, ref: jdih.kaboki.go.id */
    if (document.querySelector('.beritaSwiper')) {
      new Swiper('.beritaSwiper', {
        effect: 'coverflow',
        grabCursor: true,
        centeredSlides: true,
        slidesPerView: 'auto',
        loop: true,
        speed: 800,
        autoplay: { delay: 4500, disableOnInteraction: false },
        coverflowEffect: { rotate: 30, stretch: 0, depth: 120, modifier: 1, slideShadows: false },
        pagination: { el: '.berita-swiper-pagination', clickable: true }
      });
    }

    if (document.querySelector('.unduhanSwiper')) {
      new Swiper('.unduhanSwiper', {
        loop: true, speed: 800, spaceBetween: 25,
        autoplay: { delay: 5000, disableOnInteraction: false },
        pagination: { el: '.unduhan-swiper-pagination', clickable: true },
        breakpoints: { 0: { slidesPerView: 1 }, 992: { slidesPerView: 2 } }
      });
    }
    if (document.querySelector('.testimonialsSwiper')) {
      new Swiper('.testimonialsSwiper', {
        loop: true, speed: 800, spaceBetween: 25,
        autoplay: { delay: 4500, disableOnInteraction: false },
        pagination: { el: '.testimonials-swiper-pagination', clickable: true },
        breakpoints: { 0: { slidesPerView: 1 }, 992: { slidesPerView: 3 } }
      });
    }
    if (document.querySelector('.tautanSwiper')) {
      new Swiper('.tautanSwiper', {
        loop: true, speed: 600,
        autoplay: { delay: 3500, disableOnInteraction: false },
        pagination: { el: '.tautan-swiper-pagination', clickable: true },
        breakpoints: { 320: { slidesPerView: 2, spaceBetween: 20 }, 576: { slidesPerView: 3, spaceBetween: 30 }, 768: { slidesPerView: 4, spaceBetween: 40 }, 992: { slidesPerView: 5, spaceBetween: 50 } }
      });
    }
  });

  /** Running text ticker */
  document.addEventListener("DOMContentLoaded", function () {
    const tickerItems = document.querySelectorAll("#info-list li");
    if (tickerItems.length > 0) {
      let index = 0;
      const showItem = (i) => {
        tickerItems.forEach(el => { el.style.opacity = '0'; el.style.transform = 'translateY(100%)'; });
        tickerItems[i].style.opacity = '1';
        tickerItems[i].style.transform = 'translateY(0)';
      };
      showItem(index);
      setInterval(() => { index = (index + 1) % tickerItems.length; showItem(index); }, 3500);
    }
  });

  /** Day & date */
  document.addEventListener("DOMContentLoaded", function () {
    const el = document.getElementById("tanggal-text");
    if (el) {
      const hari = ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"];
      const bulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
      const now = new Date();
      el.innerHTML = `${hari[now.getDay()]}, ${now.getDate()} ${bulan[now.getMonth()]} ${now.getFullYear()}`;
    }
  });

  /** PureCounter fallback */
  if (typeof PureCounter !== 'undefined') new PureCounter();

  /**
   * Hero stat counters — animates numbers with data-target when they enter view.
   * Markup: <span class="hero-stat-num" data-target="1234">0</span>
   */
  document.addEventListener('DOMContentLoaded', function() {
    const counters = document.querySelectorAll('.hero-stat-num[data-target], .stat-number[data-target], .vstat-number[data-target]');
    if (!counters.length) return;

    const animateCounter = (el) => {
      const target = parseInt(el.getAttribute('data-target'), 10) || 0;
      const duration = 1200;
      const start = performance.now();
      function tick(now) {
        const progress = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - progress, 3);
        el.textContent = Math.floor(eased * target).toLocaleString('id-ID');
        if (progress < 1) requestAnimationFrame(tick);
        else el.textContent = target.toLocaleString('id-ID');
      }
      requestAnimationFrame(tick);
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          animateCounter(entry.target);
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.4 });

    counters.forEach(c => observer.observe(c));
  });

  /** Theme switcher */
  document.addEventListener('DOMContentLoaded', () => {
    const themeToggleBtn = document.getElementById('theme-toggle');
    if (themeToggleBtn) {
      themeToggleBtn.addEventListener('click', () => {
        const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme_frontend', newTheme);
      });
    }
  });

  /**
   * SAKTI hero — klik logo memicu kapal berlayar dari sisi paling kiri
   * ke sisi paling kanan section, melewati seluruh lapisan gelombang.
   * Markup: <button id="saktiLogoBtn">...logo + ring...</button>
   *         <div id="heroSailboat">...svg kapal...</div>
   */
  document.addEventListener('DOMContentLoaded', function() {
    const saktiBtn = document.getElementById('saktiLogoBtn');
    const sailboat = document.getElementById('heroSailboat');
    if (!saktiBtn || !sailboat) return;

    saktiBtn.addEventListener('click', function() {
      if (sailboat.classList.contains('sailing')) return;
      sailboat.classList.add('sailing');
    });

    sailboat.addEventListener('animationend', function() {
      sailboat.classList.remove('sailing');
    });
  });

  /**
   * Night-sky starfield — particles.js powers the twinkling stars behind the
   * hero's dark-theme sky. Runs continuously (very cheap); visibility of the
   * whole layer is controlled purely via CSS based on [data-theme].
   */
  function initHeroParticles() {
    if (typeof particlesJS === 'undefined') return;
    const target = document.getElementById('hero-particles-night');
    if (!target) return;

    particlesJS('hero-particles-night', {
      particles: {
        number: { value: 90, density: { enable: true, value_area: 900 } },
        color: { value: ['#ffffff', '#f8e9c7', '#cfe0ff'] },
        shape: { type: 'circle' },
        opacity: {
          value: 0.85,
          random: true,
          anim: { enable: true, speed: 0.6, opacity_min: 0.1, sync: false }
        },
        size: {
          value: 1.8,
          random: true,
          anim: { enable: true, speed: 1.5, size_min: 0.3, sync: false }
        },
        line_linked: { enable: false },
        move: {
          enable: true,
          speed: 0.15,
          direction: 'none',
          random: true,
          straight: false,
          out_mode: 'out'
        }
      },
      interactivity: {
        detect_on: 'window',
        events: {
          onhover: { enable: false },
          onclick: { enable: false },
          resize: true
        }
      },
      retina_detect: true
    });
  }

  window.addEventListener('load', initHeroParticles);

})();