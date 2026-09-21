/* Shared browser-side content store for the public site and admin dashboard. */
(function () {
  const KEY = 'aff_content_v1';
  // Tunapata "base path" moja kwa moja kutoka kwenye src ya script hii, badala ya kubandika
  // njia maalum ("/agape") iliyokuwa ikitumika kwenye server ya zamani. Hii inafanya kazi
  // kwa usahihi kwenye deployment yoyote (root domain, subdomain, au subpath yoyote AWS).
  const basePath = (() => {
    const suffix = '/assets/js/site-data.js';
    const current = document.currentScript && document.currentScript.src;
    if (current) {
      const p = new URL(current, location.href).pathname;
      if (p.endsWith(suffix)) return p.slice(0, p.length - suffix.length);
    }
    return '';
  })();
  const api = path => `${basePath}${path}`;
  const publicUrl = path => `${basePath}${path}`;
  const defaults = {
    whatsapp: '255700000000',
    // Watu wa kuwasiliana nao kwa WhatsApp — orodha ya juu ya watu 3, kila mmoja ana jina
    // (label) na namba, na admin anaweza kuongeza/kuhariri/kuondoa kutoka dashboard.
    whatsappContacts: [
      { label: 'Chairperson', number: '' },
      { label: 'Secretary', number: '' },
      { label: 'Developer', number: '255700000000' }
    ],
    heroImage: '',
    gallery: [
      ['https://images.unsplash.com/photo-1551650975-87deedd944c3?w=900&q=85&fit=crop', 'Morning Lessons', 'Dar es Salaam · 2024'],
      ['https://images.unsplash.com/photo-1465188162913-8fb5709d6d57?w=900&q=85&fit=crop', 'Afternoon Play', 'Mwanza Home · 2024'],
      ['https://images.unsplash.com/photo-1560252811-d1b94e64b1ff?w=900&q=85&fit=crop', 'Health Screening', 'Annual medical outreach · 2024'],
      ['https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?w=900&q=85&fit=crop', 'Community Day', 'Arusha · Annual gathering'],
      ['https://images.unsplash.com/photo-1509099652299-30938b0aeb63?w=900&q=85&fit=crop', 'Graduation Day', 'Primary school · Dodoma'],
      ['https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=900&q=85&fit=crop', 'New Arrivals', 'Welcome day · Mbeya'],
      ['https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=900&q=85&fit=crop', 'Together We Grow', 'Zanzibar outreach · 2023']
    ],
    reviews: [
      ['Amina, 11', 'Dar es Salaam · Joined 2022', "When Amina arrived, she hadn't been to school in two years. Today she reads aloud to younger children in the home and wants to be a teacher.", 'https://images.unsplash.com/photo-1542810634-71277d95dcbb?w=600&q=80&fit=crop&crop=faces'],
      ['Baraka, 9', 'Mwanza · Joined 2021', 'Baraka arrived malnourished and afraid. After 18 months of consistent care, he scored top in his class — his caregiver cried when she heard the news.', 'https://images.unsplash.com/photo-1578357078586-491adf1aa5ba?w=600&q=80&fit=crop&crop=faces'],
      ['Neema, 14', 'Arusha · Joined 2019', 'Neema is now preparing for secondary school. She speaks three languages and volunteers at the foundation on weekends, helping younger children settle in.', 'https://images.unsplash.com/photo-1497486751825-1233686d5d80?w=600&q=80&fit=crop&crop=faces']
    ],
    leaders: [
      ['Amina Mkamali', 'Executive Director', 'With 15 years in child welfare and NGO leadership, Amina founded AFF with a vision to transform the lives of orphaned children across Tanzania.', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=600&q=80&fit=crop&crop=faces'],
      ['Dr. Julius Mwenda', 'Head of Healthcare', 'A pediatrician with 20 years of experience, Julius leads our comprehensive healthcare initiatives ensuring every child receives quality medical care.', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=600&q=80&fit=crop&crop=faces'],
      ['Grace Mtoro', 'Education Coordinator', 'Grace oversees school enrollment, tutoring programs, and scholarship initiatives, ensuring every child has access to quality education.', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=600&q=80&fit=crop&crop=faces'],
      ['Emmanuel Njau', 'Finance Officer', 'Emmanuel ensures complete transparency in fund management and maintains detailed records of every Tanzanian shilling donated and how it is used.', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&q=80&fit=crop&crop=faces'],
      ['Rose Kipchoge', 'Community Liaison', 'Rose manages our community homes and ensures every child receives personalized care, mentorship, and the support they need to thrive.', 'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?w=600&q=80&fit=crop&crop=faces'],
      ['David Kiplagat', 'Program Manager', 'David oversees all program implementations across our 8 regions, coordinating with local partners and ensuring quality service delivery.', 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=600&q=80&fit=crop&crop=faces']
    ],
    adminProfile: { name: 'Administrator', image: '' },
    hero: { childrenCount: '520+', headline: 'Children Given a Second Chance', description: 'Providing education, healthcare, and daily support to orphaned children across Tanzania.' },
    programs: [
      ['Education', 'We enroll children in accredited schools, provide uniforms, books, and after-school tutoring — ensuring no child falls behind due to poverty.', '340+', 'children currently enrolled in school', 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&q=80&fit=crop'],
      ['Healthcare', 'Monthly medical check-ups, vaccinations, nutritional support, and mental health care — because a healthy child can truly learn and grow.', '1,200+', 'medical visits covered last year', 'https://images.unsplash.com/photo-1584515933487-779824d29309?w=600&q=80&fit=crop'],
      ['Community', 'We build family-like homes and connect children with caregivers, mentors, and community support systems that last a lifetime.', '6', 'community homes across Tanzania', 'https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=600&q=80&fit=crop']
    ],
    seo: { homeTitle: 'Agape Family Foundation | 520+ Children Given a Second Chance', homeDescription: 'Agape Family Foundation provides education, healthcare, and daily support to orphaned children across Tanzania.', shareImage: '' },
    donation: { paymentUrl: '', history: [] },
    // Machapisho ya blog — yanaonekana kwenye ukurasa wa Blog na sasa yanaweza kubadilishwa
    // kikamilifu na admin (ongeza/ondoa/hariri), badala ya kuwa maandishi yaliyowekwa moja kwa moja.
    // Kila chapisho lina id ya kipekee inayotumika kwenye ukurasa wake wa maelezo (blog-post.php).
    blog: [
      { id: 'seed-1', image: 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?w=600&q=80', date: 'May 2025', title: 'New Scholarship Program Launched', excerpt: "We're excited to announce a new scholarship program supporting 50 students pursuing secondary education. This initiative will change lives...", body: "We're excited to announce a new scholarship program supporting 50 students pursuing secondary education. Thanks to generous donors, these students will receive full tuition, books, and mentorship for the next four years. This initiative reflects our belief that education is the strongest path out of poverty, and we are proud to walk alongside these students as they pursue their dreams." },
      { id: 'seed-2', image: 'https://images.unsplash.com/photo-1551650975-87deedd944c3?w=600&q=80', date: 'April 2025', title: 'Annual Healthcare Outreach Reaches 1,200 Children', excerpt: "Our medical team completed a comprehensive health screening reaching over 1,200 children across 8 regions. Here's what we found...", body: 'Our medical team completed a comprehensive health screening reaching over 1,200 children across 8 regions. The outreach included vaccinations, dental checks, nutrition assessments, and referrals for children needing specialist care. We are grateful to the volunteer doctors and nurses who made this possible.' },
      { id: 'seed-3', image: 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&q=80', date: 'March 2025', title: 'Meet Amina: A Story of Hope and Determination', excerpt: "Two years ago, Amina couldn't read. Today, she's reading to other children. This is her story...", body: "Two years ago, Amina couldn't read a single word. Today, she's reading to other children in her home every evening. Her caregiver says the transformation has been remarkable to watch, and Amina now dreams of becoming a teacher herself one day." }
    ],
    contact: {
      email: 'info@agapefamilyfoundation.org',
      whatsapp: '255700000000',
      phone: '+255 22 212 3456',
      office: 'Dar es Salaam, Tanzania',
      regionalOffices: '8 Regional Offices',
      availability: 'Mon-Fri, 9 AM - 5 PM EAT',
      intro: "Have questions? Want to partner with us? Ready to make a difference? We'd love to hear from you. Reach out using any of the methods below."
    },
    analytics: [],
    messages: []
  };
  const copy = v => JSON.parse(JSON.stringify(v));
  // Badilisha muundo wa zamani wa whatsappContacts (object yenye chairperson/secretary/...) kuwa
  // muundo mpya (orodha ya watu, upeo wa 3) — ili data ya zamani kwenye server isipotee wakati
  // wa kuboresha kipengele hiki.
  function normalizeContacts(raw) {
    if (Array.isArray(raw)) return raw.slice(0, 3).map(c => ({ label: c.label || '', number: c.number || '' }));
    if (raw && typeof raw === 'object') {
      const order = [['chairperson', 'Chairperson'], ['secretary', 'Secretary'], ['developer', 'Developer'], ['groupAdmin', 'Group Admin']];
      return order.filter(([key]) => raw[key]).slice(0, 3).map(([key, label]) => ({ label, number: raw[key] }));
    }
    return [];
  }
  let cache = (() => { try { return JSON.parse(localStorage.getItem(KEY) || '{}'); } catch (_) { return {}; } })();
  const persistCache = data => { try { localStorage.setItem(KEY, JSON.stringify(data)); } catch (_) { /* Server data can exceed browser storage quotas; keep it in memory instead. */ } };
  window.AFF = {
    defaults,
    get() { return Object.assign(copy(defaults), cache); },
    save(data) { cache=data; persistCache(data); },
    api,
    publicUrl,
    async load(admin = false) { const response = await fetch(api(admin ? '/api/admin/content' : '/api/content'), {credentials:'same-origin'}); if (!response.ok) throw new Error('Unable to load saved content.'); cache=Object.assign(cache, await response.json()); persistCache(cache); apply(); return this.get(); },
    async saveRemote(data) { cache=data; persistCache(data); const response=await fetch(api('/api/admin/content'),{method:'PUT',credentials:'same-origin',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)}); if (!response.ok) throw new Error('Your secure session has expired. Sign in again.'); },
    async sendMessage(message) { this.save(Object.assign(this.get(),{messages:[...(this.get().messages||[]),message]})); const response=await fetch(api('/api/messages'),{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(message)}); if (!response.ok) throw new Error('Could not send message.'); },
    track(type, page, label) { if (location.protocol === 'file:' || localStorage.getItem('aff_cookie_consent') === 'rejected') return; fetch(api('/api/analytics'),{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({type,page:page||location.pathname,label:label||''})}).catch(()=>{}); },
    escape(text) { const el = document.createElement('div'); el.textContent = text || ''; return el.innerHTML; },
    apiUrl(path) { return api(path); },
    // Every image_path/photo_path stored by the admin is a relative path like
    // "uploads/xyz.jpg" — a bare path like that resolves relative to the
    // CURRENT page's URL in the browser, not the site root, so every image on
    // every client-rendered page needs this before it can be used in src=/url().
    storageUrl(path) { return path ? publicUrl(`/storage/${path}`) : ''; },
    normalizeContacts,
    language() { return localStorage.getItem('aff_language') || 'en'; },
    setLanguage(lang) { localStorage.setItem('aff_language', lang === 'sw' ? 'sw' : 'en'); document.documentElement.lang = this.language(); },
    imageFile(file, options = {}) {
      return new Promise((resolve, reject) => {
        if (!file || !file.type.startsWith('image/')) return reject(new Error('Choose an image file.'));
        const maxBytes = options.maxBytes || 2 * 1024 * 1024, maxSide = options.maxSide || 1400;
        if (file.size > maxBytes) return reject(new Error(`Images must be ${Math.round(maxBytes / 1024 / 1024 * 10) / 10} MB or smaller.`));
        const reader = new FileReader();
        reader.onload = () => { const img = new Image(); img.onload = () => {
          const targetWidth = options.targetWidth, targetHeight = options.targetHeight;
          const canvas = document.createElement('canvas');
          if (targetWidth && targetHeight) {
            const scale = Math.max(targetWidth / img.width, targetHeight / img.height);
            const sourceWidth = targetWidth / scale, sourceHeight = targetHeight / scale;
            const sourceX = (img.width - sourceWidth) / 2, sourceY = (img.height - sourceHeight) / 2;
            canvas.width = targetWidth; canvas.height = targetHeight;
            canvas.getContext('2d').drawImage(img, sourceX, sourceY, sourceWidth, sourceHeight, 0, 0, targetWidth, targetHeight);
          } else {
            const scale = Math.min(1, maxSide / Math.max(img.width, img.height));
            canvas.width = Math.round(img.width * scale); canvas.height = Math.round(img.height * scale);
            canvas.getContext('2d').drawImage(img, 0, 0, canvas.width, canvas.height);
          }
          resolve(canvas.toDataURL('image/jpeg', options.quality || .82));
        }; img.onerror = () => reject(new Error('That image could not be read.')); img.src = reader.result; };
        reader.readAsDataURL(file);
      });
    }
  };
  function apply() {
    const data = window.AFF.get();
    const hero = document.getElementById('heroBg');
    if (hero && data.heroImage) hero.style.backgroundImage = `url("${AFF.storageUrl(data.heroImage)}")`;
    if (hero && data.seo) { document.title=data.seo.homeTitle||document.title; const description=document.querySelector('meta[name="description"]'); if(description&&data.seo.homeDescription)description.content=data.seo.homeDescription;     if (data.seo.shareImage) { let og=document.querySelector('meta[property="og:image"]'); if(!og){og=document.createElement('meta');og.setAttribute('property','og:image');document.head.appendChild(og);} og.content=AFF.storageUrl(data.seo.shareImage); } }
    const heroContent = data.hero || {}, heroCount = document.getElementById('heroChildrenCount'), heroHeadline = document.getElementById('heroChildrenHeadline'), heroDescription = document.getElementById('heroChildrenDescription');
    if (heroCount) heroCount.textContent = heroContent.childrenCount || defaults.hero.childrenCount;
    if (heroHeadline) heroHeadline.textContent = heroContent.headline || defaults.hero.headline;
    if (heroDescription) heroDescription.textContent = heroContent.description || defaults.hero.description;
    const programs = document.getElementById('programsTrack');
    if (programs && data.programs && data.programs.length) programs.innerHTML = data.programs.map((program, i) => `<article class="program-card reveal visible" role="listitem"><div class="program-img"><div class="program-img-inner" style="background-image:url('${AFF.storageUrl(program[4])}')" role="img" aria-label="${AFF.escape(program[5] || program[0])}"></div><div class="program-icon-wrap" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21s-8-4.5-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.5-8 11-8 11Z"/></svg></div></div><div class="program-body"><h3 class="program-title">${AFF.escape(program[0])}</h3><p class="program-text">${AFF.escape(program[1])}</p><div class="program-stat"><strong>${AFF.escape(program[2])}</strong> ${AFF.escape(program[3])}</div></div></article>`).join('');
    const stage = document.getElementById('galleryStage');
    if (stage) { stage.innerHTML = data.gallery.map((item, i) => `<div class="gal-card" data-index="${i}"><div class="gal-card-img" style="background-image:url('${AFF.storageUrl(item[0])}')"></div><div class="gal-caption"><div class="gal-caption-title">${AFF.escape(item[1])}</div><div class="gal-caption-detail">${AFF.escape(item[2])}</div></div></div>`).join(''); stage.dispatchEvent(new Event('aff:gallery-updated')); }
    const grid = document.querySelector('.gallery-grid');
    if (grid) grid.innerHTML = data.gallery.map(item => `<div class="gallery-item"><img src="${AFF.storageUrl(item[0])}" alt="${AFF.escape(item[3] || item[1])}" loading="lazy"><div class="gallery-overlay"><div class="gallery-overlay-text"><strong>${AFF.escape(item[1])}</strong><br><span>${AFF.escape(item[2])}</span></div></div></div>`).join('');
    // Blog — inatoa data.blog (imesimamiwa na admin) badala ya maandishi yaliyowekwa moja kwa moja.
    // Kila kadi inaunganishwa na ukurasa wake wa maelezo (blog-post.php?id=...).
    const blogGrid = document.querySelector('.blog-grid');
    if (blogGrid && data.blog) blogGrid.innerHTML = data.blog.map(post => `<a class="blog-card" href="blog-post.php?id=${encodeURIComponent(post.id)}" style="text-decoration:none;color:inherit;display:block"><div class="blog-card-img" style="background-image:url('${AFF.storageUrl(post.image)}');background-size:cover;background-position:center"></div><div class="blog-card-body"><div class="blog-card-date">${AFF.escape(post.date)}</div><h3 class="blog-card-title">${AFF.escape(post.title)}</h3><p class="blog-card-excerpt">${AFF.escape(post.excerpt)}</p><span class="blog-card-link">Read More →</span></div></a>`).join('') || '<p style="color:var(--text-body)">No posts yet.</p>';
    const team = document.querySelector('.team-grid');
    if (team) team.innerHTML = data.leaders.map(leader => `<article class="team-card"><div class="team-card-img team-card-photo" style="background-image:url('${AFF.storageUrl(leader[3])}')" role="img" aria-label="${AFF.escape(leader[4] || leader[0])}"></div><div class="team-card-body"><div class="team-card-name">${AFF.escape(leader[0])}</div><div class="team-card-title">${AFF.escape(leader[1])}</div><p class="team-card-bio">${AFF.escape(leader[2])}</p></div></article>`).join('') || '<p>No leaders have been added yet.</p>';
    document.querySelectorAll('.story-card').forEach((card, i) => {
      const review = data.reviews[i]; if (!review) return;
      const name = card.querySelector('.story-name'), meta = card.querySelector('.story-age'), text = card.querySelector('.story-text');
      const image = card.querySelector('.story-img-inner');
      if (name) name.textContent = review[0]; if (meta) meta.textContent = review[1]; if (text) text.textContent = review[2];
      if (image && review[3]) image.style.backgroundImage = `url("${AFF.storageUrl(review[3])}")`;
    });
    const contact = data.contact || {};
    const contactText = (id, value) => { const el=document.getElementById(id); if(el && value) el.textContent=value; };
    const contactLink = (id, href, value) => { const el=document.getElementById(id); if(!el) return; if(value) el.textContent=value; if(href) el.href=href; };
    contactText('contactIntro', contact.intro);
    contactLink('contactEmail', contact.email ? `mailto:${contact.email}` : '', contact.email);
    const whatsappNumber=String(contact.whatsapp || '').replace(/\D/g,'');
    contactLink('contactWhatsapp', whatsappNumber ? `https://wa.me/${whatsappNumber}` : '', contact.whatsapp ? `+${whatsappNumber}` : '');
    contactLink('contactPhone', contact.phone ? `tel:${contact.phone.replace(/[^+\d]/g,'')}` : '', contact.phone);
    contactText('contactOffice', contact.office);
    contactText('contactRegionalOffices', contact.regionalOffices);
    contactText('contactAvailability', contact.availability);
    applyLanguage(AFF.language());
  }
  // Injini moja ya tafsiri (EN/SW) inayotumika kwenye kurasa zote za tovuti (isipokuwa index.php / route ya '/'
  // ambayo ina injini yake ya ndani). Inatembea kwenye kila kipengele chenye data-en/data-sw na
  // kubadilisha maandishi papo hapo — bila kupakia upya ukurasa (reload) tena.
  function applyLanguage(lang) {
    lang = lang === 'sw' ? 'sw' : 'en';
    document.documentElement.lang = lang;
    document.querySelectorAll('[data-en], [data-sw]').forEach(el => {
      const text = el.dataset[lang];
      if (!text) return;
      const hasSignificantChildren = Array.from(el.children).some(c => !['EM', 'STRONG', 'SPAN', 'B', 'I'].includes(c.tagName));
      if (!hasSignificantChildren) el.textContent = text;
    });
    document.querySelectorAll('[data-en-ph], [data-sw-ph]').forEach(el => {
      const text = el.dataset[lang === 'sw' ? 'swPh' : 'enPh'];
      if (text) el.setAttribute('placeholder', text);
    });
    document.querySelectorAll('.page-language-control button').forEach(btn => {
      btn.classList.toggle('active', btn.dataset.lang === lang);
    });
    addLanguageControl();
  }
  window.AFF.applyLanguage = applyLanguage;
  function addLanguageControl() {
    if (location.pathname === '/' || /index\.php$/.test(location.pathname) || document.getElementById('pageLanguageControl')) return;
    const header = document.querySelector('.header'); if (!header) return;
    const style = document.createElement('style'); style.textContent='.page-language-control{display:flex;gap:4px;margin-left:auto}.page-language-control button{border:1px solid rgba(255,255,255,.42);border-radius:6px;padding:5px 7px;color:#fff;font:600 11px DM Sans,sans-serif;cursor:pointer;background:transparent}.page-language-control button.active{background:#e8611a;border-color:#e8611a}@media(max-width:760px){.page-language-control{margin-left:auto}}'; document.head.appendChild(style);
    const control = document.createElement('div'); control.id='pageLanguageControl'; control.className='page-language-control'; control.setAttribute('aria-label','Language');
    ['en','sw'].forEach(lang => { const button=document.createElement('button'); button.type='button'; button.dataset.lang=lang; button.textContent=lang.toUpperCase(); button.classList.toggle('active', AFF.language()===lang); button.onclick=()=>{ AFF.setLanguage(lang); applyLanguage(lang); }; control.appendChild(button); });
    header.appendChild(control);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', apply); else apply();
  if (location.protocol !== 'file:') window.AFF.load().catch(() => {});
  if (location.protocol !== 'file:' && !sessionStorage.getItem('aff_view_'+location.pathname)) { sessionStorage.setItem('aff_view_'+location.pathname,'1'); window.AFF.track('pageview'); }

  // ============================================================
  // BANA YA IDHINI YA "COOKIES" (cookie consent) — inaonekana mara moja tu kwa kila kivinjari,
  // hadi mtumiaji achague Accept au Reject. Kukataa hakuzuii tovuti kufanya kazi au kupakia
  // haraka (hifadhi ya ndani ya maudhui bado inafanya kazi — hiyo ni ya lazima kiutendaji),
  // lakini inazima ufuatiliaji wa takwimu (AFF.track) kwa kivinjari hicho.
  // ============================================================
  function renderCookieBanner() {
    if (location.protocol === 'file:' || localStorage.getItem('aff_cookie_consent')) return;
    const lang = AFF.language();
    const text = lang === 'sw'
      ? { msg: 'Tunatumia hifadhi ya ndani ya kivinjari (cookies) kuboresha uzoefu wako na kufanya tovuti ipakie haraka zaidi. Takwimu za ufuatiliaji ni hiari.', accept: 'Nakubali', reject: 'Nakataa' }
      : { msg: 'We use browser storage to improve your experience and make the site load faster. Anonymous usage tracking is optional.', accept: 'Accept', reject: 'Reject' };
    const style = document.createElement('style');
    style.textContent = '#affCookieBanner{position:fixed;left:16px;right:16px;bottom:16px;max-width:560px;margin:0 auto;background:#0d1f3c;color:#fff;padding:18px 20px;border-radius:14px;box-shadow:0 12px 32px rgba(0,0,0,.28);font:14px/1.55 "DM Sans",sans-serif;z-index:9999;display:flex;flex-wrap:wrap;gap:12px;align-items:center}#affCookieBanner p{margin:0;flex:1 1 260px}#affCookieBanner button{border:none;border-radius:8px;padding:9px 16px;font:600 13px inherit;cursor:pointer}#affCookieBanner .aff-accept{background:#e8611a;color:#fff}#affCookieBanner .aff-reject{background:transparent;color:rgba(255,255,255,.75);border:1px solid rgba(255,255,255,.35)}';
    document.head.appendChild(style);
    const banner = document.createElement('div');
    banner.id = 'affCookieBanner';
    banner.setAttribute('role', 'dialog');
    banner.setAttribute('aria-label', 'Cookie consent');
    banner.innerHTML = `<p>${text.msg}</p><button class="aff-reject" type="button">${text.reject}</button><button class="aff-accept" type="button">${text.accept}</button>`;
    banner.querySelector('.aff-reject').onclick = () => { localStorage.setItem('aff_cookie_consent', 'rejected'); banner.remove(); };
    banner.querySelector('.aff-accept').onclick = () => { localStorage.setItem('aff_cookie_consent', 'accepted'); banner.remove(); };
    document.body.appendChild(banner);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', renderCookieBanner); else renderCookieBanner();

  // ============================================================
  // TANGAZO LA MATUKIO ("Updates") — dirisha ndogo (pop-up) inayoonekana mara moja mtumiaji
  // anapoanza kusogeza (scroll) ukurasa, ikionyesha tangazo MOJA kwa wakati (siyo yote 5 kwa
  // pamoja — hiyo ingekera mtumiaji). Kila tangazo linaonyeshwa MARA MOJA kwa kila kivinjari
  // (limekumbukwa kwenye localStorage), na yanayofuata yanaonyeshwa kwa mfuatano wa kuongezwa
  // (la kwanza kuongezwa ndilo la kwanza kuonyeshwa), kwenye ziara zinazofuata.
  // ============================================================
  const UPDATE_STRINGS = {
    en: { eyebrow: 'Latest Update', details: 'See Details', updates: 'See our Updates', close: 'Close', endsIn: h => h < 1 ? 'Ending soon' : `Active for ${Math.round(h)}h more` },
    sw: { eyebrow: 'Tangazo Jipya', details: 'Tazama Maelezo', updates: 'Tazama Matangazo Yetu', close: 'Funga', endsIn: h => h < 1 ? 'Inakaribia kuisha' : `Itaendelea kwa saa ${Math.round(h)} zaidi` }
  };
  function seenUpdateIds() { try { return JSON.parse(localStorage.getItem('aff_updates_seen') || '[]'); } catch { return []; } }
  function markUpdateSeen(id) { const seen = seenUpdateIds(); if (!seen.includes(id)) { seen.push(id); try { localStorage.setItem('aff_updates_seen', JSON.stringify(seen)); } catch {} } }

  function renderUpdatePopup() {
    if (location.protocol === 'file:' || document.getElementById('affUpdatePopup')) return;
    const updates = AFF.get().updates || [];
    const seen = seenUpdateIds();
    const next = updates.find(u => !seen.includes(u.id));
    if (!next) return;
    const t = UPDATE_STRINGS[AFF.language()] || UPDATE_STRINGS.en;
    const hoursLeft = next.expiresAt ? (new Date(next.expiresAt).getTime() - Date.now()) / 3600000 : null;

    const style = document.createElement('style');
    style.textContent = `
      #affUpdateOverlay{position:fixed;inset:0;background:rgba(8,22,41,.6);backdrop-filter:blur(2px);display:flex;align-items:center;justify-content:center;z-index:9998;padding:20px;opacity:0;transition:opacity .3s ease}
      #affUpdateOverlay.show{opacity:1}
      #affUpdatePopup{width:100%;max-width:380px;background:#fdfaf6;border-radius:20px;overflow:hidden;box-shadow:0 24px 60px rgba(0,0,0,.35);transform:translateY(18px) scale(.97);opacity:0;transition:transform .35s cubic-bezier(.2,.8,.2,1),opacity .35s ease;font-family:'DM Sans',sans-serif}
      #affUpdateOverlay.show #affUpdatePopup{transform:translateY(0) scale(1);opacity:1}
      #affUpdatePopup .aff-update-imgwrap{position:relative;width:100%;aspect-ratio:4/3;background:linear-gradient(135deg,#0d1f3c,#081629);overflow:hidden}
      #affUpdatePopup .aff-update-imgwrap img{width:100%;height:100%;object-fit:cover;display:block}
      #affUpdatePopup .aff-update-imgwrap::after{content:'';position:absolute;left:0;right:0;bottom:0;height:60%;background:linear-gradient(180deg,rgba(8,22,41,0) 0%,rgba(8,22,41,.55) 100%)}
      #affUpdatePopup .aff-update-close{position:absolute;top:12px;right:12px;width:32px;height:32px;border-radius:50%;background:rgba(8,22,41,.55);color:#fff;border:none;font-size:18px;line-height:1;cursor:pointer;z-index:2;display:flex;align-items:center;justify-content:center}
      #affUpdatePopup .aff-update-badge{position:absolute;left:14px;bottom:14px;z-index:2;background:#e8611a;color:#fff;font:700 11px 'DM Sans';letter-spacing:.04em;text-transform:uppercase;padding:5px 11px;border-radius:999px}
      #affUpdatePopup .aff-update-body{padding:20px 22px 22px}
      #affUpdatePopup h3{margin:0 0 8px;font:900 22px Georgia,'Playfair Display',serif;color:#0d1f3c;line-height:1.2}
      #affUpdatePopup p{margin:0 0 16px;font-size:13.5px;line-height:1.6;color:#3d4456;display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden}
      #affUpdatePopup .aff-update-time{font-size:11px;font-weight:700;color:#a06e00;margin:-8px 0 14px}
      #affUpdatePopup .aff-update-actions{display:flex;gap:10px}
      #affUpdatePopup .aff-update-cta{flex:1;background:#e8611a;color:#fff;border:none;border-radius:10px;padding:13px;font:700 14px inherit;cursor:pointer;text-align:center;text-decoration:none;display:block}
      @media (max-width:420px){#affUpdatePopup{max-width:100%}}
    `;
    document.head.appendChild(style);

    const overlay = document.createElement('div');
    overlay.id = 'affUpdateOverlay';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-label', t.eyebrow);
    overlay.innerHTML = `
      <div id="affUpdatePopup">
        <div class="aff-update-imgwrap">
          <img src="${AFF.storageUrl(next.image)}" alt="${AFF.escape(next.imageAlt || next.title)}" loading="lazy">
          <button type="button" class="aff-update-close" aria-label="${t.close}">&times;</button>
          <span class="aff-update-badge">${t.eyebrow}</span>
        </div>
        <div class="aff-update-body">
          <h3>${AFF.escape(next.title)}</h3>
          <p>${AFF.escape(next.details)}</p>
          ${hoursLeft !== null ? `<div class="aff-update-time">${t.endsIn(hoursLeft)}</div>` : ''}
          <div class="aff-update-actions">
            <a class="aff-update-cta" id="affUpdateCta" href="${(location.pathname.includes('/pages/') ? '' : 'pages/')}updates.php">${t.updates}</a>
          </div>
        </div>
      </div>`;
    document.body.appendChild(overlay);
    requestAnimationFrame(() => overlay.classList.add('show'));

    const dismiss = () => { markUpdateSeen(next.id); overlay.classList.remove('show'); setTimeout(() => overlay.remove(), 300); };
    overlay.querySelector('.aff-update-close').onclick = dismiss;
    overlay.addEventListener('click', e => { if (e.target === overlay) dismiss(); });
    overlay.querySelector('#affUpdateCta').addEventListener('click', () => markUpdateSeen(next.id));
  }

  // Onyesha TU baada ya mtumiaji kuanza kusogeza ukurasa (scroll) — si mara moja anapofungua
  // tovuti — ili isimkasirishe (annoying) mtumiaji anayeanza tu kusoma ukurasa.
  function armUpdatePopupOnScroll() {
    if (location.protocol === 'file:') return;
    let armed = false;
    const trigger = () => {
      if (armed || window.scrollY < 220) return;
      armed = true;
      window.removeEventListener('scroll', trigger);
      setTimeout(renderUpdatePopup, 200);
    };
    window.addEventListener('scroll', trigger, { passive: true });
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', armUpdatePopupOnScroll); else armUpdatePopupOnScroll();
})();
