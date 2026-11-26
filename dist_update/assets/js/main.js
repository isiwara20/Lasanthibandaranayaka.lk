/**
 * LASANTHI BANDARANAYAKA SALON & ACADEMY
 * Official JavaScript Controller with Admin Dynamic Connectors
 */

document.addEventListener('DOMContentLoaded', () => {
  // 0. Theme Toggle Controller (Light & Dark Mode)
  const themeToggleBtn = document.getElementById('theme-toggle');

  const applyTheme = (theme) => {
    document.documentElement.setAttribute('data-theme', theme);
    if (document.body) {
      document.body.setAttribute('data-theme', theme);
    }
    localStorage.setItem('theme', theme);
  };

  const savedTheme = localStorage.getItem('theme') || 'dark';
  applyTheme(savedTheme);

  if (themeToggleBtn) {
    themeToggleBtn.addEventListener('click', () => {
      const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
      const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
      applyTheme(newTheme);
    });
  }

  // 1. Header scroll effect & Luxury Scroll Progress Bar
  const header = document.querySelector('.site-header');

  // Top Scroll Progress Bar element
  let scrollProgressBar = document.querySelector('.scroll-progress-bar');
  if (!scrollProgressBar) {
    scrollProgressBar = document.createElement('div');
    scrollProgressBar.className = 'scroll-progress-bar';
    document.body.prepend(scrollProgressBar);
  }

  // Floating Back to Top Button
  let scrollTopBtn = document.getElementById('scroll-to-top');
  if (!scrollTopBtn) {
    scrollTopBtn = document.createElement('button');
    scrollTopBtn.id = 'scroll-to-top';
    scrollTopBtn.className = 'scroll-top-btn';
    scrollTopBtn.setAttribute('aria-label', 'Scroll to top');
    scrollTopBtn.innerHTML = '<i class="fa-solid fa-arrow-up"></i>';
    document.body.appendChild(scrollTopBtn);

    scrollTopBtn.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    });
  }

  // Optimized Scroll Listener (Throttled with requestAnimationFrame)
  let isTicking = false;
  window.addEventListener('scroll', () => {
    if (!isTicking) {
      window.requestAnimationFrame(() => {
        const scrollY = window.scrollY;
        const docHeight = document.documentElement.scrollHeight - window.innerHeight;

        // 1. Header scrolled style
        if (header) {
          if (scrollY > 40) {
            header.classList.add('scrolled');
          } else {
            header.classList.remove('scrolled');
          }
        }

        // 2. Scroll Progress Bar
        if (scrollProgressBar && docHeight > 0) {
          const progress = Math.min(100, Math.max(0, (scrollY / docHeight) * 100));
          scrollProgressBar.style.width = progress + '%';
        }

        // 3. Scroll to top button visibility
        if (scrollTopBtn) {
          if (scrollY > 350) {
            scrollTopBtn.classList.add('is-visible');
          } else {
            scrollTopBtn.classList.remove('is-visible');
          }
        }

        isTicking = false;
      });
      isTicking = true;
    }
  }, { passive: true });

  // 1.1 Responsive Dual Video Auto-Player (Desktop vs Mobile versions)
  const heroVideo = document.getElementById('hero-bg-video');
  const heroSoundToggle = document.getElementById('hero-sound-toggle');

  let bannerVideos = [
    {
      video_desktop: 'assets/videos/bridal_reel_1.mp4',
      video_mobile: 'assets/videos/bridal_reel_1.mp4'
    },
    {
      video_desktop: 'assets/videos/bridal_reel_2.mp4',
      video_mobile: 'assets/videos/bridal_reel_2.mp4'
    },
    {
      video_desktop: 'assets/videos/bridal_reel_3.mp4',
      video_mobile: 'assets/videos/bridal_reel_3.mp4'
    }
  ];
  let currentVideoIndex = 0;

  // Function to choose appropriate video source depending on screen width
  const getVideoSource = (videoItem) => {
    const isMobile = window.innerWidth <= 768;
    return isMobile ? (videoItem.video_mobile || videoItem.video_desktop) : (videoItem.video_desktop || videoItem.video_mobile);
  };

  const playVideoAtIndex = (index) => {
    if (!heroVideo || !bannerVideos.length) return;
    currentVideoIndex = index % bannerVideos.length;
    const nextSrc = getVideoSource(bannerVideos[currentVideoIndex]);

    heroVideo.style.opacity = '0.35';
    setTimeout(() => {
      heroVideo.src = nextSrc;
      heroVideo.load();
      heroVideo.play().catch(() => { });
      heroVideo.style.opacity = '1';
    }, 300);
  };

  // Try fetching dynamic banner videos from admin API
  fetch('api/get_banner_videos.php')
    .then(res => res.json())
    .then(data => {
      if (Array.isArray(data) && data.length > 0) {
        bannerVideos = data;
        playVideoAtIndex(0);
      }
    })
    .catch(() => {
      // Fallback to local default videos
      playVideoAtIndex(0);
    });

  if (heroVideo) {
    heroVideo.addEventListener('ended', () => {
      playVideoAtIndex(currentVideoIndex + 1);
    });

    if (heroSoundToggle) {
      heroSoundToggle.addEventListener('click', () => {
        heroVideo.muted = !heroVideo.muted;
        const icon = heroSoundToggle.querySelector('i');
        if (icon) {
          if (heroVideo.muted) {
            icon.classList.remove('fa-volume-high');
            icon.classList.add('fa-volume-xmark');
          } else {
            icon.classList.remove('fa-volume-xmark');
            icon.classList.add('fa-volume-high');
          }
        }
      });
    }

    // Adjust video on window resize if crossing mobile/desktop boundary
    let lastWidth = window.innerWidth;
    window.addEventListener('resize', () => {
      const isMobileNow = window.innerWidth <= 768;
      const wasMobile = lastWidth <= 768;
      if (isMobileNow !== wasMobile) {
        lastWidth = window.innerWidth;
        playVideoAtIndex(currentVideoIndex);
      }
    });
  }

  // 1.2 Smooth Scroller Animation Observer & Staggered Reveal
  const revealElements = document.querySelectorAll('.reveal-on-scroll');
  if ('IntersectionObserver' in window) {
    const revealObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-revealed');
          observer.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.08,
      rootMargin: '0px 0px -20px 0px'
    });

    revealElements.forEach(el => revealObserver.observe(el));
  } else {
    revealElements.forEach(el => el.classList.add('is-revealed'));
  }

  // 1.3 Animated Stats Number Counter on Scroll
  const initStatsCounterAnimation = () => {
    const statCards = document.querySelectorAll('.trust-stats-bar .stat-number');
    const statsBar = document.querySelector('.trust-stats-bar');
    if (!statCards.length || !statsBar) return;

    const statsData = [
      { target: 12, suffix: '+', isComma: false },
      { target: 1500, suffix: '+', isComma: true },
      { target: 850, suffix: '+', isComma: false },
      { target: 100, suffix: '%', isComma: false }
    ];

    let started = false;
    const runCounters = () => {
      if (started) return;
      started = true;

      statCards.forEach((el, idx) => {
        const item = statsData[idx] || { target: parseInt(el.textContent.replace(/[^0-9]/g, '')) || 100, suffix: '+', isComma: false };
        const duration = 1800;
        const startTime = performance.now();

        const updateCount = (currentTime) => {
          const elapsed = currentTime - startTime;
          const progress = Math.min(1, elapsed / duration);
          // Ease-out exponential
          const ease = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
          const current = Math.floor(ease * item.target);
          const formatted = item.isComma ? current.toLocaleString() : current;
          el.textContent = `${formatted}${item.suffix}`;

          if (progress < 1) {
            requestAnimationFrame(updateCount);
          } else {
            const finalFormatted = item.isComma ? item.target.toLocaleString() : item.target;
            el.textContent = `${finalFormatted}${item.suffix}`;
          }
        };
        requestAnimationFrame(updateCount);
      });
    };

    if ('IntersectionObserver' in window) {
      const statsObserver = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting) {
          runCounters();
          statsObserver.disconnect();
        }
      }, { threshold: 0.2 });
      statsObserver.observe(statsBar);
    } else {
      runCounters();
    }
  };
  initStatsCounterAnimation();

  // 2. Mobile Menu Toggle
  const mobileToggle = document.querySelector('.mobile-toggle');
  const navMenu = document.querySelector('.nav-menu');
  if (mobileToggle && navMenu) {
    mobileToggle.addEventListener('click', () => {
      navMenu.classList.toggle('open');
      const icon = mobileToggle.querySelector('i');
      if (icon) {
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-xmark');
      }
    });

    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', () => {
        navMenu.classList.remove('open');
        const icon = mobileToggle.querySelector('i');
        if (icon) {
          icon.classList.add('fa-bars');
          icon.classList.remove('fa-xmark');
        }
      });
    });
  }

  // 2.1 Services Ribbon (Native responsive grid - accessible booking modal)

  // 3. Dynamic Video Showcase & Carousel Slider
  const videoTrack = document.getElementById('videos-slider-track');
  const prevBtn = document.getElementById('video-slider-prev');
  const nextBtn = document.getElementById('video-slider-next');
  const dotsContainer = document.getElementById('video-carousel-dots');
  const counterContainer = document.getElementById('video-carousel-counter');
  const allVideosGrid = document.getElementById('all-videos-grid');

  const setupVideoCardListeners = (container = document) => {
    const videoCards = container.querySelectorAll('.video-card');
    videoCards.forEach(card => {
      const video = card.querySelector('video');
      const playBtn = card.querySelector('.video-play-btn');

      if (video && playBtn && !playBtn._hasListener) {
        playBtn._hasListener = true;
        const togglePlay = () => {
          if (video.paused) {
            // Pause all other video reels
            document.querySelectorAll('.video-card video').forEach(v => {
              if (v !== video) {
                v.pause();
                const parent = v.closest('.video-card');
                if (parent) parent.classList.remove('playing');
              }
            });

            video.play();
            card.classList.add('playing');
          } else {
            video.pause();
            card.classList.remove('playing');
          }
        };

        playBtn.addEventListener('click', togglePlay);
        video.addEventListener('click', togglePlay);

        video.addEventListener('ended', () => {
          card.classList.remove('playing');
          video.currentTime = 0;
        });
      }
    });
  };

  // Video Slider Carousel
  if (videoTrack) {
    let currentSlide = 0;
    let totalCards = 0;

    const getVisibleCount = () => {
      const w = window.innerWidth;
      if (w > 1080) return 3;
      if (w > 680) return 2;
      return 1;
    };

    const updateSlider = () => {
      const cards = videoTrack.querySelectorAll('.video-card');
      totalCards = cards.length;
      if (totalCards === 0) return;

      const visibleCount = getVisibleCount();
      const maxSlide = Math.max(0, totalCards - visibleCount);

      if (currentSlide > maxSlide) currentSlide = maxSlide;
      if (currentSlide < 0) currentSlide = 0;

      const firstCard = cards[0];
      const cardWidth = firstCard ? firstCard.getBoundingClientRect().width : 340;
      const gap = 24;
      const offset = currentSlide * (cardWidth + gap);

      videoTrack.style.transform = `translateX(-${offset}px)`;

      // Arrows
      if (prevBtn) prevBtn.disabled = (currentSlide === 0);
      if (nextBtn) nextBtn.disabled = (currentSlide >= maxSlide);

      // Dots
      if (dotsContainer) {
        dotsContainer.innerHTML = '';
        const numDots = maxSlide + 1;
        if (numDots > 1) {
          for (let i = 0; i < numDots; i++) {
            const dot = document.createElement('span');
            dot.className = `dot ${i === currentSlide ? 'active' : ''}`;
            dot.addEventListener('click', () => {
              currentSlide = i;
              updateSlider();
            });
            dotsContainer.appendChild(dot);
          }
        }
      }

      // Counter
      if (counterContainer) {
        if (totalCards > visibleCount) {
          counterContainer.textContent = `${currentSlide + 1} - ${Math.min(currentSlide + visibleCount, totalCards)} of ${totalCards}`;
        } else {
          counterContainer.textContent = '';
        }
      }
    };

    if (prevBtn) {
      prevBtn.addEventListener('click', () => {
        if (currentSlide > 0) {
          currentSlide--;
          updateSlider();
        }
      });
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', () => {
        const visibleCount = getVisibleCount();
        const maxSlide = Math.max(0, totalCards - visibleCount);
        if (currentSlide < maxSlide) {
          currentSlide++;
          updateSlider();
        }
      });
    }

    // Touch swipe support
    let startX = 0;
    let isSwiping = false;

    videoTrack.addEventListener('touchstart', (e) => {
      startX = e.touches[0].clientX;
      isSwiping = true;
    }, { passive: true });

    videoTrack.addEventListener('touchend', (e) => {
      if (!isSwiping) return;
      isSwiping = false;
      const endX = e.changedTouches[0].clientX;
      const diff = startX - endX;
      const visibleCount = getVisibleCount();
      const maxSlide = Math.max(0, totalCards - visibleCount);

      if (diff > 50 && currentSlide < maxSlide) {
        currentSlide++;
        updateSlider();
      } else if (diff < -50 && currentSlide > 0) {
        currentSlide--;
        updateSlider();
      }
    }, { passive: true });

    window.addEventListener('resize', () => {
      updateSlider();
    });

    // Fetch dynamic videos from API if available
    fetch('api/get_videos.php')
      .then(res => res.json())
      .then(data => {
        if (Array.isArray(data) && data.length > 0) {
          videoTrack.innerHTML = '';
          data.forEach((v, idx) => {
            const card = document.createElement('div');
            card.className = 'video-card';
            card.innerHTML = `
              <div class="video-wrapper">
                <span class="video-badge"><i class="fa-solid fa-play"></i> ${v.badge || ('Reel ' + (idx + 1))}</span>
                <video src="${v.video_url}" playsinline loop preload="metadata" poster="${v.poster_url || ''}"></video>
                <button class="video-play-btn" aria-label="Play video"><i class="fa-solid fa-play"></i></button>
              </div>
              <div class="video-info">
                <h3 class="video-title">${v.title || ''}</h3>
                <p class="video-sub">${v.subtitle || ''}</p>
              </div>
            `;
            videoTrack.appendChild(card);
          });
          setupVideoCardListeners(videoTrack);
          updateSlider();
        } else {
          setupVideoCardListeners(videoTrack);
          updateSlider();
        }
      })
      .catch(() => {
        setupVideoCardListeners(videoTrack);
        updateSlider();
      });
  } else {
    // Other pages (e.g. videos.html)
    if (allVideosGrid) {
      fetch('api/get_videos.php')
        .then(res => res.json())
        .then(data => {
          if (Array.isArray(data) && data.length > 0) {
            allVideosGrid.innerHTML = '';
            data.forEach((v, idx) => {
              const card = document.createElement('div');
              card.className = 'video-card';
              card.innerHTML = `
                <div class="video-wrapper">
                  <span class="video-badge"><i class="fa-solid fa-film"></i> ${v.badge || ('Reel ' + (idx + 1))}</span>
                  <video src="${v.video_url}" playsinline loop preload="metadata" poster="${v.poster_url || ''}"></video>
                  <button class="video-play-btn" aria-label="Play video"><i class="fa-solid fa-play"></i></button>
                </div>
                <div class="video-info">
                  <h3 class="video-title">${v.title || ''}</h3>
                  <p class="video-sub">${v.subtitle || ''}</p>
                </div>
              `;
              allVideosGrid.appendChild(card);
            });
            setupVideoCardListeners(allVideosGrid);
          } else {
            setupVideoCardListeners(allVideosGrid);
          }
        })
        .catch(() => {
          setupVideoCardListeners(allVideosGrid);
        });
    } else {
      setupVideoCardListeners();
    }
  }

  // 4. Testimonials Swipeable Carousel Logic
  const tTrack = document.getElementById('testimonials-container');
  const tViewport = document.getElementById('testimonials-viewport');
  const tPrev = document.getElementById('testimonials-prev-btn');
  const tNext = document.getElementById('testimonials-next-btn');
  const tDots = document.getElementById('testimonials-dots');
  const tCounter = document.getElementById('testimonials-counter');

  if (tTrack && tViewport) {
    let tSlide = 0;

    const escapeText = (str) => {
      if (!str) return '';
      const div = document.createElement('div');
      div.textContent = str;
      return div.innerHTML;
    };

    const getVisibleTestimonialCount = () => {
      const w = window.innerWidth;
      if (w > 1024) return 3;
      if (w > 768) return 2;
      return 1;
    };

    const updateTestimonialSlider = () => {
      const cards = tTrack.querySelectorAll('.testimonial-luxury-card');
      const total = cards.length;
      if (total === 0) return;

      const visible = getVisibleTestimonialCount();
      const maxSlide = Math.max(0, total - visible);

      if (tSlide > maxSlide) tSlide = maxSlide;
      if (tSlide < 0) tSlide = 0;

      const firstCard = cards[0];
      const cardWidth = firstCard ? firstCard.getBoundingClientRect().width : 340;
      const gap = parseFloat(window.getComputedStyle(tTrack).gap) || 24;
      const offset = tSlide * (cardWidth + gap);

      tTrack.style.transform = `translateX(-${offset}px)`;

      if (tPrev) tPrev.disabled = (tSlide === 0);
      if (tNext) tNext.disabled = (tSlide >= maxSlide);

      if (tCounter) {
        tCounter.textContent = `${tSlide + 1} / ${maxSlide + 1}`;
      }

      if (tDots) {
        tDots.innerHTML = '';
        const numDots = maxSlide + 1;
        for (let i = 0; i < numDots; i++) {
          const dot = document.createElement('span');
          dot.className = `dot ${i === tSlide ? 'active' : ''}`;
          dot.addEventListener('click', () => {
            tSlide = i;
            updateTestimonialSlider();
          });
          tDots.appendChild(dot);
        }
      }
    };

    if (tPrev) {
      tPrev.addEventListener('click', () => {
        if (tSlide > 0) {
          tSlide--;
          updateTestimonialSlider();
        }
      });
    }

    if (tNext) {
      tNext.addEventListener('click', () => {
        const cards = tTrack.querySelectorAll('.testimonial-luxury-card');
        const maxSlide = Math.max(0, cards.length - getVisibleTestimonialCount());
        if (tSlide < maxSlide) {
          tSlide++;
          updateTestimonialSlider();
        }
      });
    }

    // Touch Swipe Support
    let tStartX = 0;
    let tStartY = 0;
    let isTSwiping = false;

    tViewport.addEventListener('touchstart', (e) => {
      tStartX = e.touches[0].clientX;
      tStartY = e.touches[0].clientY;
      isTSwiping = true;
    }, { passive: true });

    tViewport.addEventListener('touchend', (e) => {
      if (!isTSwiping) return;
      isTSwiping = false;
      const diffX = tStartX - e.changedTouches[0].clientX;
      const diffY = tStartY - e.changedTouches[0].clientY;

      if (Math.abs(diffX) > Math.abs(diffY) && Math.abs(diffX) > 40) {
        const cards = tTrack.querySelectorAll('.testimonial-luxury-card');
        const maxSlide = Math.max(0, cards.length - getVisibleTestimonialCount());
        if (diffX > 0 && tSlide < maxSlide) {
          tSlide++;
          updateTestimonialSlider();
        } else if (diffX < 0 && tSlide > 0) {
          tSlide--;
          updateTestimonialSlider();
        }
      }
    }, { passive: true });

    // Mouse Drag Support
    let isMouseDown = false;
    let mouseStartX = 0;

    tViewport.addEventListener('mousedown', (e) => {
      isMouseDown = true;
      mouseStartX = e.clientX;
      tViewport.classList.add('is-dragging');
    });

    window.addEventListener('mouseup', (e) => {
      if (!isMouseDown) return;
      isMouseDown = false;
      tViewport.classList.remove('is-dragging');
      const diffX = mouseStartX - e.clientX;
      if (Math.abs(diffX) > 50) {
        const cards = tTrack.querySelectorAll('.testimonial-luxury-card');
        const maxSlide = Math.max(0, cards.length - getVisibleTestimonialCount());
        if (diffX > 0 && tSlide < maxSlide) {
          tSlide++;
          updateTestimonialSlider();
        } else if (diffX < 0 && tSlide > 0) {
          tSlide--;
          updateTestimonialSlider();
        }
      }
    });

    window.addEventListener('resize', updateTestimonialSlider);

    // Fetch dynamic testimonials from API
    fetch('api/get_testimonials.php')
      .then(res => res.json())
      .then(data => {
        if (Array.isArray(data) && data.length > 0) {
          tTrack.innerHTML = data.map((t) => {
            const count = Math.min(5, Math.max(1, parseInt(t.rating) || 5));
            const stars = Array(count).fill('<i class="fa-solid fa-star"></i>').join('');
            const avatar = t.avatar || 'assets/images/client_nimali.jpg';
            return `
              <div class="testimonial-luxury-card">
                <div class="t-card-top">
                  <div class="t-card-stars">${stars}</div>
                  <span class="t-service-pill"><i class="fa-solid fa-gem"></i> ${escapeText(t.service || 'Salon Service')}</span>
                </div>
                <p class="t-quote-text">&ldquo;${escapeText(t.quote)}&rdquo;</p>
                <div class="t-client-row">
                  <img src="${escapeText(avatar)}" alt="${escapeText(t.name)}" class="t-client-avatar">
                  <div class="t-client-info">
                    <h4 class="t-client-name">${escapeText(t.name)}</h4>
                    <div class="t-client-sub">${escapeText(t.role || 'Valued Client')} • ${escapeText(t.location || 'Dambulla')}</div>
                  </div>
                  <div class="t-verified-badge" title="Verified Review">
                    <i class="fa-solid fa-circle-check"></i>
                  </div>
                </div>
              </div>
            `;
          }).join('');
          updateTestimonialSlider();
        }
      })
      .catch(() => {
        updateTestimonialSlider();
      });

    setTimeout(updateTestimonialSlider, 100);
  }

  // 5. Booking Modal Logic
  const modal = document.getElementById('appointment-modal');
  const openModalBtns = document.querySelectorAll('[data-open-modal="booking"]');
  const closeModalBtn = document.querySelector('.modal-close-btn');

  const openBookingModal = (serviceName = '') => {
    if (modal) {
      modal.classList.add('active');
      if (serviceName) {
        const serviceSelect = document.getElementById('booking-service');
        if (serviceSelect) {
          serviceSelect.value = serviceName;
        }
      }
    }
  };

  const closeBookingModal = () => {
    if (modal) modal.classList.remove('active');
  };

  openModalBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const s = btn.getAttribute('data-service') || '';
      openBookingModal(s);
    });
  });

  if (closeModalBtn) {
    closeModalBtn.addEventListener('click', closeBookingModal);
  }

  if (modal) {
    modal.addEventListener('click', (e) => {
      if (e.target === modal) closeBookingModal();
    });
  }

  // 6. Booking Form Submission -> Saves to Admin API + WhatsApp
  const bookingForm = document.getElementById('booking-form');
  if (bookingForm) {
    bookingForm.addEventListener('submit', (e) => {
      e.preventDefault();
      const name = document.getElementById('booking-name').value.trim();
      const phone = document.getElementById('booking-phone').value.trim();
      const service = document.getElementById('booking-service').value;
      const date = document.getElementById('booking-date').value;
      const notes = document.getElementById('booking-notes').value.trim();

      // Send to Admin Backend
      const formData = new FormData();
      formData.append('name', name);
      formData.append('phone', phone);
      formData.append('service', service);
      formData.append('date', date);
      formData.append('notes', notes);

      fetch('api/save_booking.php', {
        method: 'POST',
        body: formData
      }).catch(() => { });

      // Open WhatsApp directly
      const text = `*New Booking Request - Lasanthi Salon & Academy*%0A%0A` +
        `👤 *Client Name:* ${encodeURIComponent(name)}%0A` +
        `📞 *Phone:* ${encodeURIComponent(phone)}%0A` +
        `✨ *Service Required:* ${encodeURIComponent(service)}%0A` +
        `📅 *Preferred Date:* ${encodeURIComponent(date || 'Not specified')}%0A` +
        `📝 *Notes:* ${encodeURIComponent(notes || 'None')}`;

      const waUrl = `https://wa.me/94777958184?text=${text}`;
      window.open(waUrl, '_blank');
      closeBookingModal();
    });
  }

  // 7. Lightbox Preview for Gallery Photos
  const galleryItems = document.querySelectorAll('.gallery-item');
  const lightboxModal = document.getElementById('lightbox-modal');
  const lightboxImg = document.getElementById('lightbox-img');
  const lightboxTitle = document.getElementById('lightbox-caption');
  const lightboxClose = document.querySelector('.lightbox-close');

  galleryItems.forEach(item => {
    item.addEventListener('click', () => {
      const img = item.querySelector('img');
      const title = item.querySelector('.gallery-item-title');
      if (img && lightboxImg && lightboxModal) {
        lightboxImg.src = img.src;
        if (lightboxTitle && title) lightboxTitle.textContent = title.textContent;
        lightboxModal.classList.add('active');
      }
    });
  });

  if (lightboxClose && lightboxModal) {
    lightboxClose.addEventListener('click', () => {
      lightboxModal.classList.remove('active');
    });
    lightboxModal.addEventListener('click', (e) => {
      if (e.target === lightboxModal) {
        lightboxModal.classList.remove('active');
      }
    });
  }
});
