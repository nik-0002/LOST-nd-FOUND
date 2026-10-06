/* ============================================================
   LOST & FOUND — Enhanced Interactive JavaScript
   ============================================================ */

// ── Mouse-tracking gradient follow ──────────────────────────
document.addEventListener('mousemove', (e) => {
  document.body.style.setProperty('--mouse-x', e.clientX + 'px');
  document.body.style.setProperty('--mouse-y', e.clientY + 'px');
});

// ── Particle System ─────────────────────────────────────────
(function initParticles() {
  const canvas = document.getElementById('particles');
  if (!canvas) return;
  const ctx = canvas.getContext('2d');
  let particles = [];
  let mouse = { x: null, y: null };

  function resize() {
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;
  }
  resize();
  window.addEventListener('resize', resize);

  document.addEventListener('mousemove', (e) => {
    mouse.x = e.clientX;
    mouse.y = e.clientY;
  });

  class Particle {
    constructor() {
      this.reset();
    }
    reset() {
      this.x = Math.random() * canvas.width;
      this.y = Math.random() * canvas.height;
      this.size = Math.random() * 2 + 0.5;
      this.speedX = (Math.random() - 0.5) * 0.5;
      this.speedY = (Math.random() - 0.5) * 0.5;
      this.opacity = Math.random() * 0.5 + 0.1;
    }
    update() {
      this.x += this.speedX;
      this.y += this.speedY;

      // Mouse repulsion
      if (mouse.x !== null) {
        const dx = mouse.x - this.x;
        const dy = mouse.y - this.y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < 120) {
          this.x -= dx * 0.02;
          this.y -= dy * 0.02;
        }
      }

      if (this.x < 0 || this.x > canvas.width || this.y < 0 || this.y > canvas.height) {
        this.reset();
      }
    }
    draw() {
      ctx.fillStyle = `rgba(255, 255, 255, ${this.opacity})`;
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
      ctx.fill();
    }
  }

  for (let i = 0; i < 80; i++) particles.push(new Particle());

  function connectParticles() {
    for (let a = 0; a < particles.length; a++) {
      for (let b = a + 1; b < particles.length; b++) {
        const dx = particles[a].x - particles[b].x;
        const dy = particles[a].y - particles[b].y;
        const dist = Math.sqrt(dx * dx + dy * dy);
        if (dist < 120) {
          ctx.strokeStyle = `rgba(0, 255, 204, ${0.1 * (1 - dist / 120)})`;
          ctx.lineWidth = 0.5;
          ctx.beginPath();
          ctx.moveTo(particles[a].x, particles[a].y);
          ctx.lineTo(particles[b].x, particles[b].y);
          ctx.stroke();
        }
      }
    }
  }

  function animate() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    particles.forEach(p => { p.update(); p.draw(); });
    connectParticles();
    requestAnimationFrame(animate);
  }
  animate();
})();

// ── Button Ripple Effect ────────────────────────────────────
document.addEventListener('click', (e) => {
  const btn = e.target.closest('button');
  if (!btn) return;
  const ripple = document.createElement('span');
  ripple.classList.add('ripple');
  const rect = btn.getBoundingClientRect();
  ripple.style.left = (e.clientX - rect.left) + 'px';
  ripple.style.top = (e.clientY - rect.top) + 'px';
  btn.appendChild(ripple);
  setTimeout(() => ripple.remove(), 600);
});

// ── Scroll-triggered Animations (Intersection Observer) ─────
const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const scrollObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.classList.add('animate-in');
      scrollObserver.unobserve(entry.target);
    }
  });
}, observerOptions);

// Observe all animatable elements
document.querySelectorAll('.card, .stats div, .how-step, table, form, h2').forEach(el => {
  el.style.opacity = '0';
  el.style.transform = 'translateY(30px)';
  el.style.transition = 'all 0.7s cubic-bezier(0.25, 0.8, 0.25, 1)';
  scrollObserver.observe(el);
});

// Apply animation class
document.head.insertAdjacentHTML('beforeend', `<style>
  .animate-in { opacity: 1 !important; transform: translateY(0) !important; }
</style>`);

// ── 3D Card Tilt Effect ─────────────────────────────────────
document.querySelectorAll('.card').forEach(card => {
  card.addEventListener('mousemove', (e) => {
    const rect = card.getBoundingClientRect();
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;
    const centerX = rect.width / 2;
    const centerY = rect.height / 2;
    const rotateX = (y - centerY) / 15;
    const rotateY = (centerX - x) / 15;
    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-5px)`;
  });
  card.addEventListener('mouseleave', () => {
    card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
  });
});

// ── Animated Counter (Stat Numbers) ─────────────────────────
function animateCounters() {
  document.querySelectorAll('.stats b, .stat-number').forEach(el => {
    const target = parseInt(el.textContent) || 0;
    if (target === 0 || el.dataset.animated) return;
    el.dataset.animated = 'true';
    let current = 0;
    const increment = Math.max(1, Math.ceil(target / 40));
    const duration = 1200;
    const stepTime = duration / (target / increment);

    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        current = target;
        clearInterval(timer);
      }
      el.textContent = current;
    }, stepTime);
  });
}

const counterObserver = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      animateCounters();
      counterObserver.unobserve(entry.target);
    }
  });
}, { threshold: 0.5 });

document.querySelectorAll('.stats').forEach(el => counterObserver.observe(el));

// ── Toast Notification System ───────────────────────────────
window.showToast = function(message, type = 'info', duration = 4000) {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const icons = { success: '✅', error: '❌', info: 'ℹ️', warning: '⚠️' };
  const toast = document.createElement('div');
  toast.className = `toast ${type}`;
  toast.innerHTML = `<span class="icon">${icons[type] || icons.info}</span><span>${message}</span>`;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = 'toastOut 0.5s ease-out forwards';
    setTimeout(() => toast.remove(), 500);
  }, duration);
};

// ── Back to Top Button ──────────────────────────────────────
(function initBackToTop() {
  const btn = document.createElement('div');
  btn.className = 'back-to-top';
  btn.innerHTML = '↑';
  btn.title = 'Back to top';
  document.body.appendChild(btn);

  window.addEventListener('scroll', () => {
    btn.classList.toggle('visible', window.scrollY > 400);
  });

  btn.addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
})();

// ── Smooth Page Transitions ─────────────────────────────────
document.querySelectorAll('a[href]').forEach(link => {
  const href = link.getAttribute('href');
  if (!href || href.startsWith('#') || href.startsWith('javascript') || link.target === '_blank') return;
  
  link.addEventListener('click', function(e) {
    // Don't intercept form submit links, external links, or notification links
    if (this.closest('form') || this.closest('#nl')) return;
    
    e.preventDefault();
    const main = document.querySelector('main');
    if (main) {
      main.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
      main.style.opacity = '0';
      main.style.transform = 'translateY(20px)';
    }
    setTimeout(() => {
      window.location.href = href;
    }, 300);
  });
});

// ── Notifications (Enhanced) ────────────────────────────────
const bell = document.getElementById('bell');
if (bell) {
  const nl = document.getElementById('nl');
  const poll = () => fetch(BASE + '/notifications/fetch.php')
    .then(r => r.json())
    .then(d => {
      const cntEl = document.getElementById('cnt');
      if (cntEl) {
        cntEl.textContent = d.unread ? ' ' + d.unread : '';
        cntEl.style.display = d.unread ? 'flex' : 'none';
      }
      if (nl) {
        nl.innerHTML = d.items.map(n =>
          `<a href="${n.link}" style="animation: fadeInUp 0.3s ease-out">${n.is_read == 1 ? '' : '• '}${n.message}<br><small style="color:var(--fg-subtle)">${n.time || ''}</small></a>`
        ).join('') || '<div style="padding:20px;text-align:center;color:var(--fg-subtle)"><span style="font-size:2rem;display:block;margin-bottom:8px">🔔</span>No notifications</div>';
      }
    })
    .catch(() => {});

  bell.onclick = e => {
    e.preventDefault();
    if (nl) {
      const isOpen = nl.style.display === 'block';
      nl.style.display = isOpen ? 'none' : 'block';
      if (!isOpen) {
        fetch(BASE + '/notifications/fetch.php?read=1').then(() => setTimeout(poll, 500));
      }
    }
  };

  // Close notification panel when clicking outside
  document.addEventListener('click', (e) => {
    if (nl && !bell.contains(e.target) && !nl.contains(e.target)) {
      nl.style.display = 'none';
    }
  });

  poll();
  setInterval(poll, 10000);
}

// ── Quick Search (Homepage) ─────────────────────────────────
const quickSearch = document.getElementById('quick-search');
if (quickSearch) {
  let debounceTimer;
  quickSearch.addEventListener('input', () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
      const q = quickSearch.value.trim();
      if (q.length >= 2) {
        window.location.href = BASE + '/items/list.php?q=' + encodeURIComponent(q);
      }
    }, 800);
  });

  quickSearch.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      const q = quickSearch.value.trim();
      if (q) {
        window.location.href = BASE + '/items/list.php?q=' + encodeURIComponent(q);
      }
    }
  });
}

// ── Keyboard Shortcuts ──────────────────────────────────────
document.addEventListener('keydown', (e) => {
  // Ctrl/Cmd + K = Focus search
  if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
    e.preventDefault();
    const searchInput = document.getElementById('quick-search') || document.querySelector('.filters input[name="q"]');
    if (searchInput) searchInput.focus();
  }
});

// ── Image Lightbox ──────────────────────────────────────────
document.querySelectorAll('.card img, .item-detail img, main img[src*="uploads"]').forEach(img => {
  img.style.cursor = 'zoom-in';
  img.addEventListener('click', (e) => {
    e.stopPropagation();
    const overlay = document.createElement('div');
    overlay.style.cssText = `
      position:fixed;top:0;left:0;width:100%;height:100%;
      background:rgba(0,0,0,0.9);z-index:10000;display:flex;
      align-items:center;justify-content:center;cursor:zoom-out;
      animation:fadeInScale 0.3s ease-out;
      backdrop-filter:blur(10px);
    `;
    const bigImg = document.createElement('img');
    bigImg.src = img.src;
    bigImg.style.cssText = `
      max-width:90vw;max-height:90vh;border-radius:16px;
      box-shadow:0 25px 50px rgba(0,0,0,0.5);
      animation:fadeInScale 0.4s ease-out;
    `;
    overlay.appendChild(bigImg);
    overlay.addEventListener('click', () => {
      overlay.style.opacity = '0';
      overlay.style.transition = 'opacity 0.3s ease';
      setTimeout(() => overlay.remove(), 300);
    });
    document.body.appendChild(overlay);
  });
});

// ── Scroll Progress Bar ─────────────────────────────────────
(function initScrollProgress() {
  const bar = document.createElement('div');
  bar.style.cssText = `
    position:fixed;top:0;left:0;height:3px;z-index:9999;
    background:linear-gradient(90deg, #ff3366, #00ffcc, #3399ff);
    width:0%;transition:width 0.15s ease;
    box-shadow:0 0 10px rgba(0,255,204,0.5);
  `;
  document.body.appendChild(bar);

  window.addEventListener('scroll', () => {
    const scrolled = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
    bar.style.width = Math.min(scrolled, 100) + '%';
  });
})();

// ── Lazy Image Loading with Blur-up ─────────────────────────
document.querySelectorAll('.card img').forEach(img => {
  img.style.filter = 'blur(5px)';
  img.style.transition = 'filter 0.5s ease';
  if (img.complete) {
    img.style.filter = 'blur(0)';
  } else {
    img.addEventListener('load', () => {
      img.style.filter = 'blur(0)';
    });
  }
});

// ── Enhanced filter live-search with animation ──────────────
const filterForm = document.getElementById('f');
if (filterForm) {
  const grid = document.getElementById('grid');
  filterForm.addEventListener('input', () => {
    if (grid) {
      grid.style.opacity = '0.5';
      grid.style.transition = 'opacity 0.2s ease';
    }
    clearTimeout(filterForm._debounce);
    filterForm._debounce = setTimeout(() => {
      fetch('list.php?ajax=1&' + new URLSearchParams(new FormData(filterForm)))
        .then(r => r.text())
        .then(h => {
          if (grid) {
            grid.innerHTML = h;
            grid.style.opacity = '1';
            // Re-apply card animations
            grid.querySelectorAll('.card').forEach((card, i) => {
              card.style.opacity = '0';
              card.style.animation = `fadeInUp 0.5s ease-out ${i * 0.08}s forwards`;
              // Re-apply 3D tilt
              card.addEventListener('mousemove', (e) => {
                const rect = card.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;
                const centerX = rect.width / 2;
                const centerY = rect.height / 2;
                const rotateX = (y - centerY) / 15;
                const rotateY = (centerX - x) / 15;
                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-5px)`;
              });
              card.addEventListener('mouseleave', () => {
                card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0)';
              });
            });
            // Re-apply lightbox
            grid.querySelectorAll('.card img').forEach(img => {
              img.style.cursor = 'zoom-in';
              img.addEventListener('click', (e) => {
                e.stopPropagation();
                const overlay = document.createElement('div');
                overlay.style.cssText = `position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);z-index:10000;display:flex;align-items:center;justify-content:center;cursor:zoom-out;backdrop-filter:blur(10px);`;
                const bigImg = document.createElement('img');
                bigImg.src = img.src;
                bigImg.style.cssText = `max-width:90vw;max-height:90vh;border-radius:16px;box-shadow:0 25px 50px rgba(0,0,0,0.5);animation:fadeInScale 0.4s ease-out;`;
                overlay.appendChild(bigImg);
                overlay.addEventListener('click', () => { overlay.style.opacity = '0'; overlay.style.transition = 'opacity 0.3s ease'; setTimeout(() => overlay.remove(), 300); });
                document.body.appendChild(overlay);
              });
            });
          }
        });
    }, 300);
  });
}

// ── Dark mode text cursor glow ──────────────────────────────
document.querySelectorAll('input, textarea').forEach(input => {
  input.addEventListener('focus', () => {
    input.style.caretColor = '#00ffcc';
  });
});
