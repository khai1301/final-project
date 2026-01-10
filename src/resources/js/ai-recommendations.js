/**
 * AI Recommendation System - Frontend Integration
 * Multi-layer caching: localStorage (1h) + Laravel cache (24h)
 * Auto-loads recommendations without user action
 */

class AIRecommendations {
    constructor() {
        this.CACHE_DURATION = 3600000; // 1 hour in milliseconds  
        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.setupEventListeners();
                this.autoLoadRecommendations();
            });
        } else {
            this.setupEventListeners();
            this.autoLoadRecommendations();
        }
    }

    setupEventListeners() {
        // Manual refresh button (optional)
        document.querySelectorAll('[data-ai-refresh]').forEach(button => {
            button.addEventListener('click', (e) => this.handleRefreshClick(e));
        });
    }

    /**
     * Auto-load recommendations on page load
     */
    autoLoadRecommendations() {
        // For students: Auto-load tutor recommendations if requestId exists
        const tutorContainer = document.querySelector('[data-ai-tutors]');
        if (tutorContainer) {
            const requestId = tutorContainer.dataset.requestId;
            if (requestId) {
                this.loadTutorRecommendations(requestId, tutorContainer);
            }
        }

        // For tutors: Auto-load request recommendations if tutorProfileId exists
        const requestContainer = document.querySelector('[data-ai-requests]');
        if (requestContainer) {
            const tutorProfileId = requestContainer.dataset.tutorProfileId;
            if (tutorProfileId) {
                this.loadRequestRecommendations(tutorProfileId, requestContainer);
            }
        }
    }

    /**
     * Load tutor recommendations with caching
     */
    async loadTutorRecommendations(requestId, container) {
        // Check localStorage first
        const cached = this.getCachedData('tutors', requestId);
        if (cached) {
            console.log('📦 Loading tutor recommendations from cache');
            this.displayInline(cached.data, 'tutors', container);
            this.showCacheIndicator(container, cached.cached_at);
            return;
        }

        // Load from server (which checks Laravel cache)
        try {
            this.showLoading(container);

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };

            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.content;
            }

            const response = await fetch(`/api/recommendations/tutors/${requestId}`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.data.length > 0) {
                // Cache in localStorage
                this.setCachedData('tutors', requestId, data);

                this.displayInline(data.data, 'tutors', container);
                console.log('🤖 Loaded fresh tutor recommendations from AI');
            } else {
                this.showEmptyState(container, data.message || 'Không tìm thấy gia sư phù hợp');
            }
        } catch (error) {
            console.error('Error loading tutor recommendations:', error);
            this.showError(container, error.message);
        } finally {
            this.hideLoading(container);
        }
    }

    /**
     * Load request recommendations with caching
     */
    async loadRequestRecommendations(tutorProfileId, container) {
        // Check localStorage first
        const cached = this.getCachedData('requests', tutorProfileId);
        if (cached) {
            console.log('📦 Loading request recommendations from cache');
            this.displayInline(cached.data, 'requests', container);
            this.showCacheIndicator(container, cached.cached_at);
            return;
        }

        // Load from server
        try {
            this.showLoading(container);

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            const headers = {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            };

            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken.content;
            }

            const response = await fetch(`/api/recommendations/requests/${tutorProfileId}`, {
                method: 'GET',
                headers: headers
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success && data.data.length > 0) {
                this.setCachedData('requests', tutorProfileId, data);

                this.displayInline(data.data, 'requests', container);
                console.log('🤖 Loaded fresh request recommendations from AI');
            } else {
                this.showEmptyState(container, data.message || 'Không tìm thấy yêu cầu phù hợp');
            }
        } catch (error) {
            console.error('Error loading request recommendations:', error);
            this.showError(container, error.message);
        } finally {
            this.hideLoading(container);
        }
    }

    /**
     * Get cached data from localStorage
     */
    getCachedData(type, id) {
        const cacheKey = `ai_rec_v2_${type}_${id}`;
        const cached = localStorage.getItem(cacheKey);

        if (!cached) return null;

        try {
            const data = JSON.parse(cached);
            const now = Date.now();

            // Check expiration
            if (now - data.cached_at > this.CACHE_DURATION) {
                localStorage.removeItem(cacheKey);
                return null;
            }

            return data;
        } catch (e) {
            localStorage.removeItem(cacheKey);
            return null;
        }
    }

    /**
     * Save to localStorage cache
     */
    setCachedData(type, id, data) {
        const cacheKey = `ai_rec_v2_${type}_${id}`;
        const cacheData = {
            ...data,
            cached_at: Date.now()
        };

        try {
            localStorage.setItem(cacheKey, JSON.stringify(cacheData));
        } catch (e) {
            console.warn('localStorage full:', e);
        }
    }

    /**
     * Display recommendations inline (not modal)
     */
    displayInline(items, type, container) {
        if (type === 'tutors') {
            container.innerHTML = this.createTutorCards(items);
        } else {
            container.innerHTML = this.createRequestCards(items);
        }
    }

    /**
     * Create tutor cards HTML - matching existing design 100%
     */
    createTutorCards(tutors) {
        // Check if user is authenticated student
        const isStudent = document.querySelector('meta[name="user-role"]')?.content === 'student';
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        return tutors.slice(0, 4).map((tutor, index) => {
            // Avatar URL with S3 fallback (like existing cards)
            const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(tutor.tutor_name)}&size=100`;

            // Action button HTML
            let actionButton;
            if (isStudent && csrfToken) {
                // Student sees "Connect" button
                actionButton = `
                    <form action="/matching/connect" method="POST" class="w-100" style="margin: 0;">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <input type="hidden" name="tutor_id" value="${tutor.user_id}">
                        <button type="submit" class="home-tutor-view-btn" style="width: auto;">
                            Kết nối ngay
                        </button>
                    </form>
                `;
            } else {
                // Others see "View profile"
                actionButton = `<a href="/tutors/${tutor.user_id}" class="home-tutor-view-btn">Xem profile</a>`;
            }

            return `
                <div class="col-sm-6 col-lg-3">
                    <div class="home-tutor-card">
                        <div class="home-tutor-header">
                            <div class="home-tutor-avatar-wrapper">
                                <img src="${avatarUrl}" 
                                     alt="${this.escapeHtml(tutor.tutor_name)}" 
                                     class="home-tutor-avatar">
                                <div class="home-tutor-verified">
                                    <span class="material-symbols-outlined">check</span>
                                </div>
                            </div>
                            <div class="home-tutor-rating">
                                <span class="material-symbols-outlined">star</span>
                                ${tutor.rating_avg || '5.0'}
                            </div>
                        </div>
                        <div>
                            <h3 class="home-tutor-name">${this.escapeHtml(tutor.tutor_name)}</h3>
                            <p class="home-tutor-subject">${this.escapeHtml(tutor.education || 'Gia sư')}</p>
                        </div>
                        <div class="ai-match-info mb-3">
                            <div class="ai-match-badge">
                                <span class="material-symbols-outlined">psychology</span>
                                <strong>${tutor.match_score}% Match</strong>
                            </div>
                            <p class="ai-match-reason small text-muted mb-0">
                                ${this.escapeHtml(tutor.match_reason)}
                            </p>
                        </div>
                        <div class="home-tutor-footer">
                            <span class="home-tutor-price">
                                ${Math.round(tutor.hourly_rate_min / 1000)}k-${Math.round(tutor.hourly_rate_max / 1000)}k
                                <span class="home-tutor-price-unit">₫/giờ</span>
                            </span>
                            ${actionButton}
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }

    /**
     * Create request cards HTML
     */
    /**
     * Create request cards HTML
     * Synchronized with requests/browse.blade.php design
     */
    /**
     * Create request cards HTML
     * Synchronized with requests/browse.blade.php design
     */
    createRequestCards(requests) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        return requests.slice(0, 5).map((req, index) => {
            // Verified badge HTML
            const verifiedBadge = req.student_is_verified
                ? `<span class="text-primary ms-1" title="Đã xác thực"><span class="material-symbols-outlined" style="font-size: 18px; vertical-align: text-bottom;">verified</span></span>`
                : '';

            // Format price
            const minPrice = Math.round(req.budget_min / 1000);
            const maxPrice = Math.round(req.budget_max / 1000);

            // Badges
            let badgesHtml = '';
            if (req.subject) badgesHtml += `<span class="badge bg-primary me-2">${this.escapeHtml(req.subject)}</span>`;
            if (req.education_level) badgesHtml += `<span class="badge bg-info me-2">${this.escapeHtml(req.education_level)}</span>`;
            if (req.learning_mode) badgesHtml += `<span class="badge bg-success me-2">${this.escapeHtml(req.learning_mode)}</span>`;

            // Description truncate
            const description = this.escapeHtml(req.description || '');
            const truncatedDesc = description.length > 150 ? description.substring(0, 150) + '...' : description;

            // Format time ago (re-calculated on client sideto avoid stale cache)
            const timeAgo = this.formatTimeAgo(req.created_at);

            return `
            <div class="col-12">
                <div class="card shadow-sm hover-shadow h-100">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h5 class="card-title">
                                    ${this.escapeHtml(req.title)}
                                    ${verifiedBadge}
                                </h5>
                                
                                <div class="d-flex flex-wrap mb-3">
                                    ${badgesHtml}
                                </div>

                                <!-- AI Match Overlay -->
                                <div class="alert alert-light border-primary p-2 mb-3">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="material-symbols-outlined text-primary me-2">psychology</span>
                                        <strong class="text-primary me-auto">${req.match_score}% Phù hợp</strong>
                                    </div>
                                    <small class="d-block text-muted">AI: ${this.escapeHtml(req.match_reason)}</small>
                                </div>

                                <p class="text-muted mb-3">
                                    ${truncatedDesc}
                                </p>

                                <div class="d-flex flex-wrap gap-3 small text-muted">
                                    <div class="d-flex align-items-center">
                                        <span class="material-symbols-outlined me-1" style="font-size: 16px;">location_on</span>
                                        ${this.escapeHtml(req.location)}
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="material-symbols-outlined me-1" style="font-size: 16px;">schedule</span>
                                        ${timeAgo}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 d-flex flex-column justify-content-between align-items-end">
                                <div class="text-end mb-3">
                                    <div class="h4 text-primary mb-0">
                                        ${minPrice}k - ${maxPrice}k ₫
                                    </div>
                                    <small class="text-muted">/ giờ</small>
                                </div>

                                <form action="/matching/connect" method="POST">
                                    <input type="hidden" name="_token" value="${csrfToken}">
                                    <input type="hidden" name="request_id" value="${req.request_id}">
                                    <button type="submit" class="btn btn-primary">
                                        <span class="material-symbols-outlined" style="font-size: 18px; vertical-align: middle;">send</span>
                                        Kết nối ngay
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            `;
        }).join('');
    }

    /**
     * Calculate relative time from ISO string
     */
    formatTimeAgo(isoDate) {
        if (!isoDate) return 'Mới đây';
        const date = new Date(isoDate);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);

        if (seconds < 60) return 'Vừa xong';
        const minutes = Math.floor(seconds / 60);
        if (minutes < 60) return `${minutes} phút trước`;
        const hours = Math.floor(minutes / 60);
        if (hours < 24) return `${hours} giờ trước`;
        const days = Math.floor(hours / 24);
        if (days < 30) return `${days} ngày trước`;
        const months = Math.floor(days / 30);
        if (months < 12) return `${months} tháng trước`;
        return `${Math.floor(days / 365)} năm trước`;
    }

    showLoading(container) {
        container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Đang phân tích với AI...</p></div>';
    }

    hideLoading(container) {
        // Loading is replaced by content
    }

    showEmptyState(container, message) {
        container.innerHTML = `<div class="alert alert-info">${message}</div>`;
    }

    showError(container, errorMessage = '') {
        const message = errorMessage ? `: ${errorMessage}` : '';
        container.innerHTML = `<div class="alert alert-danger">Có lỗi xảy ra khi tải gợi ý${message}</div>`;
    }

    showCacheIndicator(container, cachedAt) {
        const minutes = Math.floor((Date.now() - cachedAt) / 60000);
        const timeAgo = minutes < 1 ? 'vừa xong' : `${minutes} phút trước`;

        const indicator = document.createElement('small');
        indicator.className = 'text-muted cache-indicator';
        indicator.textContent = `(Đã lưu ${timeAgo})`;
        container.insertBefore(indicator, container.firstChild);
    }

    handleRefreshClick(event) {
        event.preventDefault();
        const button = event.currentTarget;
        const type = button.dataset.aiRefresh;
        const id = button.dataset.id;

        // Clear cache
        localStorage.removeItem(`ai_rec_v2_${type}_${id}`);

        // Reload
        if (type === 'tutors') {
            const container = document.querySelector('[data-ai-tutors]');
            this.loadTutorRecommendations(id, container);
        } else {
            const container = document.querySelector('[data-ai-requests]');
            this.loadRequestRecommendations(id, container);
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize
const aiRecommendations = new AIRecommendations();
