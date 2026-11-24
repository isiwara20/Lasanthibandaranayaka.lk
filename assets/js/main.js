/**
 * LASANTHI BANDARANAYAKA SALON & ACADEMY
 * Official JavaScript Controller with Admin Dynamic Connectors
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Header scroll effect
  const header = document.querySelector('.site-header');
  window.addEventListener('scroll', () => {
    if (window.scrollY > 40) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  });

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
      heroVideo.play().catch(() => {});
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

  // 1.2 Smooth Scroller Animation Observer
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
      threshold: 0.12,
      rootMargin: '0px 0px -30px 0px'
    });

    revealElements.forEach(el => revealObserver.observe(el));
  } else {
    revealElements.forEach(el => el.classList.add('is-revealed'));
  }

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

  // 3. Video Players Controller for Reels
  const videoCards = document.querySelectorAll('.video-card');
  videoCards.forEach(card => {
    const video = card.querySelector('video');
    const playBtn = card.querySelector('.video-play-btn');

    if (video && playBtn) {
      const togglePlay = () => {
        if (video.paused) {
          // Pause other reels
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

  // 4. Testimonial Carousel Data & Logic
  const testimonials = [
    {
      quote: "Lasanthi Salon gave me the most beautiful bridal look. Highly recommended! Every detail of my Kandyan jewelry and headpiece was placed to absolute perfection.",
      name: "Nimali Perera",
      role: "Kandyan Bride",
      avatar: "assets/images/client_nimali.jpg"
    },
    {
      quote: "The bridal dressing experience at Lasanthi Bandaranayaka Salon was beyond a dream. Lasanthi is truly an artist. My wedding photos look so royal and elegant!",
      name: "Dilki Madushani",
      role: "Bridal Client",
      avatar: "assets/images/bridal_1.jpg"
    },
    {
      quote: "I completed the Advanced Bridal & Hair Styling Diploma at Lasanthi Academy. The hands-on practical training gave me the exact confidence to launch my own salon!",
      name: "Sanduni Jayawardena",
      role: "Academy Graduate",
      avatar: "assets/images/academy_training.jpg"
    }
  ];

  window.setTestimonial = (index) => {
    const item = testimonials[index];
    const quoteElem = document.getElementById('testimonial-quote');
    const nameElem = document.getElementById('client-name');
    const roleElem = document.getElementById('client-role');
    const avatarElem = document.getElementById('client-avatar');
    const dots = document.querySelectorAll('.t-dot');

    if (quoteElem) quoteElem.textContent = `“${item.quote}”`;
    if (nameElem) nameElem.textContent = item.name;
    if (roleElem) roleElem.textContent = item.role;
    if (avatarElem) avatarElem.src = item.avatar;

    dots.forEach((dot, idx) => {
      dot.style.opacity = idx === index ? '1' : '0.4';
      dot.style.transform = idx === index ? 'scale(1.25)' : 'scale(1)';
    });
  };

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
      }).catch(() => {});

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
