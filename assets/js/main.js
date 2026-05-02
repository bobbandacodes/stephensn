(function () {
  // ---- Mobile menu toggle ----
  const toggle = document.querySelector('.nav-toggle');
  const body = document.body;
  if (toggle) {
    toggle.addEventListener('click', () => {
      const open = body.classList.toggle('menu-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
      toggle.setAttribute('aria-label', open ? 'Close menu' : 'Open menu');
      const menu = document.querySelector('.mobile-menu');
      if (menu) menu.setAttribute('aria-hidden', open ? 'false' : 'true');
    });
  }

  // Close menu when a link is tapped (in case it's an anchor on same page)
  document.querySelectorAll('.mobile-menu a').forEach(a => {
    a.addEventListener('click', () => body.classList.remove('menu-open'));
  });

  // Close on Escape
  document.addEventListener('keydown', e => {
    if (e.key === 'Escape' && body.classList.contains('menu-open')) {
      body.classList.remove('menu-open');
      if (toggle) toggle.setAttribute('aria-expanded', 'false');
    }
  });

  // ---- Header shadow on scroll ----
  const header = document.querySelector('.site-header');
  if (header) {
    const onScroll = () => header.classList.toggle('scrolled', window.scrollY > 8);
    onScroll();
    window.addEventListener('scroll', onScroll, { passive: true });
  }

  // ---- Auto-tag scroll-reveal targets ----
  // Anything inside a section.block (other than the hero) that hasn't opted out fades up on scroll.
  document.querySelectorAll('section.block > .wrap > *').forEach((el, i) => {
    if (!el.classList.contains('no-reveal')) {
      el.classList.add('reveal');
      if (i % 4) el.dataset.delay = (i % 4).toString();
    }
  });

  if ('IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
    document.querySelectorAll('.reveal').forEach(el => io.observe(el));
  } else {
    document.querySelectorAll('.reveal').forEach(el => el.classList.add('in'));
  }

  // ---- Gallery lightbox ----
  const items = document.querySelectorAll('.gallery-item[data-full]');
  if (items.length) {
    const box = document.createElement('div');
    box.className = 'lightbox';
    box.innerHTML = '<img alt="">';
    document.body.appendChild(box);
    const img = box.querySelector('img');
    items.forEach(it => {
      it.addEventListener('click', () => {
        img.src = it.dataset.full;
        box.classList.add('open');
      });
    });
    box.addEventListener('click', () => box.classList.remove('open'));
    document.addEventListener('keydown', e => {
      if (e.key === 'Escape') box.classList.remove('open');
    });
  }
})();
